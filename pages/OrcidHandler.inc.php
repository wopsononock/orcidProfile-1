<?php

/**
 * @file plugins/generic/orcidProfile/OrcidHandler.inc.php
 *
 * Copyright (c) 2015-2016 University of Pittsburgh
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Copyright (c) 2017-2018 University Library Heidelberg
 * Distributed under the GNU GPL v2 or later. For full terms see the file docs/COPYING.
 *
 * @class OrcidHandler
 * @ingroup plugins_generic_orcidprofile
 *
 * @brief Pass off internal ORCID API requests to ORCID
 */

import('classes.handler.Handler');

class OrcidHandler extends Handler {
	const MESSAGE_TPL = 'frontend/pages/message.tpl';

	/**
	 * Authorize handler
	 * @param $args array
	 * @param $request Request
	 */
	function orcidAuthorize($args, $request) {
		$context = Request::getContext();
		$op = Request::getRequestedOp();
		$plugin = PluginRegistry::getPlugin('generic', 'orcidprofileplugin');
		$contextId = ($context == null) ? 0 : $context->getId();

		// fetch the access token
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $plugin->getSetting($contextId, 'orcidProfileAPIPath').OAUTH_TOKEN_URL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Accept: application/json'),
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query(array(
				'code' => Request::getUserVar('code'),
				'grant_type' => 'authorization_code',
				'client_id' => $plugin->getSetting($contextId, 'orcidClientId'),
				'client_secret' => $plugin->getSetting($contextId, 'orcidClientSecret')
			))
		));
		$result = curl_exec($curl);
		if (!$result) error_log('CURL error: ' . curl_error($curl));
		$response = json_decode($result, true);

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL =>	$url = $plugin->getSetting($contextId, 'orcidProfileAPIPath') . ORCID_API_VERSION_URL . urlencode($response['orcid']) . '/' . ORCID_PROFILE_URL,
			CURLOPT_POST => false,
			CURLOPT_HTTPHEADER => array('Accept: application/json'),
		));
		$result = curl_exec($curl);
		if (!$result) error_log('CURL error: ' . curl_error($curl));
		$info = curl_getinfo($curl);
		if ($info['http_code'] == 200) {
			$json = json_decode($result, true);
		}

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL =>	$url = $plugin->getSetting($contextId, 'orcidProfileAPIPath') . ORCID_API_VERSION_URL . urlencode($response['orcid']) . '/' . ORCID_EMAIL_URL,
			CURLOPT_POST => false,
			CURLOPT_HTTPHEADER => array('Accept: application/json'),
		));
		$result = curl_exec($curl);
		if (!$result) error_log('CURL error: ' . curl_error($curl));
		$info = curl_getinfo($curl);
		if ($info['http_code'] == 200) {
			$json_email = json_decode($result, true);
			$json['email']['value'] = $json_email['email'][0]['email'];
		}

		$orcid_uri = 'http://orcid.org/' . $response['orcid'];

		switch (Request::getUserVar('targetOp')) {
			case 'register':
				echo '<html><body><script type="text/javascript">
					opener.document.getElementById("firstName").value = ' . json_encode($json['name']['given-names']['value']) . ';
					opener.document.getElementById("lastName").value = ' . json_encode($json['name']['family-name']['value']) . ';
					opener.document.getElementById("email").value = ' . json_encode($json['email']['value']) . ';
					opener.document.getElementById("orcid").value = ' . json_encode($orcid_uri). ';
					opener.document.getElementById("connect-orcid-button").style.display = "none";
					window.close();
				</script></body></html>';
				break;
			case 'profile':
				// Set the ORCiD in the user profile from the response
				echo '<html><body><script type="text/javascript">
					opener.document.getElementsByName("orcid")[0].value = ' . json_encode($orcid_uri). ';
					opener.document.getElementById("connect-orcid-button").style.display = "none";
					window.close();
				</script></body></html>';
				break;
			case 'submit':
				// Submission process: Pre-fill the first author's ORCiD from the ORCiD data
				echo '<html><body><script type="text/javascript">
					opener.document.getElementById("authors-0-orcid").value = ' . json_encode($orcid_uri). ';
					opener.document.getElementById("connect-orcid-button").style.display = "none";
					window.close();
				</script></body></html>';
				break;
			default: assert(false);
		}
	}

	/**
	 * Verify an incoming author claim for an ORCiD association.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function orcidVerify($args, $request) {
		$context = $request->getContext();
		$plugin = PluginRegistry::getPlugin('generic', 'orcidprofileplugin');
		$templateMgr = TemplateManager::getManager($request);
		$contextId = ($context == null) ? 0 : $context->getId();

		// User denied access
		if ( $request->getUserVar('error') === 'access_denied' ) {
			// TODO specify special message if user denied access
			$templateMgr->assign(array(
				'currentUrl' => $request->url(null, 'index'),
				'pageTitle' => 'plugins.generic.orcidProfile.author.submission',
				'message' => 'plugins.generic.orcidProfile.authFailure',
			));
			error_log('OrcidHandler::orcidverify - ORCID verfifcation error: '. $request->getUserVar('error_description'));
			$templateMgr->display(self::MESSAGE_TPL);
			exit();
		}

		// fetch the access token
		$url = $plugin->getSetting($contextId, 'orcidProfileAPIPath').OAUTH_TOKEN_URL;
		$header = array('Accept: application/json');
		$curl = curl_init($url);
		$postData = http_build_query(array(
			'code' => $request->getUserVar('code'),
			'grant_type' => 'authorization_code',
			'client_id' => $plugin->getSetting($contextId, 'orcidClientId'),
			'client_secret' => $plugin->getSetting($contextId, 'orcidClientSecret')
		));
		OrcidProfilePlugin::log('POST ' . $url);
		OrcidProfilePlugin::log('Request header: ' . var_export($header, true));
		OrcidProfilePlugin::log('Request body: ' . $postData);
		curl_setopt_array($curl, array(			
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postData
		));
		$result = curl_exec($curl);
		if (!$result) error_log('OrcidHandler::orcidverify - CURL error ' . curl_error($curl));
		
		$response = json_decode($result, true);

		if (!isset($response['orcid'])) {
			$templateMgr->assign(array(
				'currentUrl' => $request->url(null, 'index'),
				'pageTitle' => 'plugins.generic.orcidProfile.author.submission',
				'message' => 'plugins.generic.orcidProfile.authFailure',
			));
			error_log('OrcidHandler::orcidverify - Invalid ORCID response '. $result);
			$templateMgr->display(self::MESSAGE_TPL);
			exit();
		}

		if (!isset($response['access_token'])) {
			$templateMgr->assign(array(
				'currentUrl' => $request->url(null, 'index'),
				'pageTitle' => 'plugins.generic.orcidProfile.author.submission',
				'message' => 'plugins.generic.orcidProfile.authFailure',
			));
			$templateMgr->display(self::MESSAGE_TPL);
			exit();
		}
		$authorDao = DAORegistry::getDAO('AuthorDAO');
		$submissionId = $request->getUserVar('articleId');
		$authors = $authorDao->getBySubmissionId($submissionId);		
		$orcidSandbox = false;
		if ($plugin->getSetting($contextId, 'orcidProfileAPIPath') == ORCID_API_URL_MEMBER_SANDBOX ||
			$plugin->getSetting($contextId, 'orcidProfileAPIPath') == ORCID_PUBLIC_URL_MEMBER_SANDBOX) {
			$orcidSandbox = true;
		}
		// Find the author in the database, for whom the authorization link was generated
		foreach ($authors as $author) {
			if ($author->getData('orcidToken') == $request->getUserVar('orcidToken')) {
				$orcidAccessExpiresOn = Carbon\Carbon::now();
				// expires_in field from the response contains the lifetime in seconds of the token
				// See https://members.orcid.org/api/get-oauthtoken
				$orcidAccessExpiresOn->addSeconds($response['expires_in']);
				$author->setData('orcid', 'https://orcid.org' . $response['orcid']);
				if ($orcidSandbox) {
					$author->setData('orcidSandbox', $orcidSandbox);
				}				
				$author->setData('orcidAccessToken', $response['access_token']);
				$author->setData('orcidRefreshToken', $response['refresh_token']);
				$author->setData('orcidAccessExpiresOn', $orcidAccessExpiresOn->toDateTimeString());
				$author->setData('orcidToken', null);
				$authorDao->updateObject($author);
				$plugin->sendSubmissionToOrcid($submissionId, $request);
				$templateMgr->assign(array(
					'currentUrl' => $request->url(null, 'index'),
					'pageTitle' => 'plugins.generic.orcidProfile.author.submission',
					'message' => 'plugins.generic.orcidProfile.author.submission.success',
				));
				$templateMgr->display(self::MESSAGE_TPL);
				exit();
			}
		}
		// Only reaches here if no Author exists in the database with the supplied orcidToken
		// Display a failure message
		$templateMgr->assign(array(
			'currentUrl' => $request->url(null, 'index'),
			'pageTitle' => 'plugins.generic.orcidProfile.author.submission',
			'message' => 'plugins.generic.orcidProfile.author.submission.failure',
		));
		$templateMgr->display(self::MESSAGE_TPL);
	}
}

?>
