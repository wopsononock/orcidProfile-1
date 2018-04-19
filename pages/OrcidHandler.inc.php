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
	const TEMPLATE = 'orcidVerify.tpl';

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
		$curl = curl_init();
		// Use proxy if configured
		if ($httpProxyHost = Config::getVar('proxy', 'http_host')) {
			curl_setopt($curl, CURLOPT_PROXY, $httpProxyHost);
			curl_setopt($curl, CURLOPT_PROXYPORT, Config::getVar('proxy', 'http_port', '80'));
			if ($username = Config::getVar('proxy', 'username')) {
				curl_setopt($curl, CURLOPT_PROXYUSERPWD, $username . ':' . Config::getVar('proxy', 'password'));
			}
		}
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
		// fetch the access token
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
		curl_close($curl);
		$orcid_uri = 'https://orcid.org/' . $response['orcid'];

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
		$submissionId = $request->getUserVar('articleId');
		$authorDao = DAORegistry::getDAO('AuthorDAO');
		$authors = $authorDao->getBySubmissionId($submissionId);		

		$authorToVerify = null;
		// Find the author entry, for which the ORCID verification was requested
		if($request->getUserVar('token')) {
			foreach ($authors as $author) {
				if ($author->getData('orcidEmailToken') == $request->getUserVar('token')) {
					$authorToVerify = $author;
				}
			}
		}
		// initialise template parameters
		$templateMgr->assign(array(
				'currentUrl' => $request->url(null, 'index'),
				'verifySuccess' => false,
				'authFailure' => false,
				'notPublished' => false,
				'sendSubmission' => false,
				'sendSubmissionSuccess' => false,
				'denied' => false));

		if ($authorToVerify == null) {
			// no Author exists in the database with the supplied orcidEmailToken
			$plugin->logError('OrcidHandler::orcidverify - No author found with supplied token');
			$templateMgr->assign('verifySuccess', false);
			$templateMgr->display($plugin->getTemplatePath() . self::TEMPLATE);
			return;
		}		
		if ( $request->getUserVar('error') === 'access_denied' ) {
			// User denied access			
			// Store the date time the author denied ORCID access to remember this
			$authorToVerify->setData('orcidAccessDenied', Core::getCurrentDate());
			// remove all previously stored ORCID access token
			$authorToVerify->setData('orcidAccessToken', null);
			$authorToVerify->setData('orcidAccessScope', null);
			$authorToVerify->setData('orcidRefreshToken', null);
			$authorToVerify->setData('orcidAccessExpiresOn', null);
			$authorToVerify->setData('orcidEmailToken', null);
			$authorDao->updateLocaleFields($authorToVerify);
			$plugin->logError('OrcidHandler::orcidverify - ORCID access denied. Error description: '
				. $request->getUserVar('error_description'));
			$templateMgr->assign('denied', true);
			$templateMgr->display($plugin->getTemplatePath() . self::TEMPLATE);
			return;
		}

		// fetch the access token
		$url = $plugin->getSetting($contextId, 'orcidProfileAPIPath').OAUTH_TOKEN_URL;
		$header = array('Accept: application/json');
		$ch = curl_init($url);
		$postData = http_build_query(array(
			'code' => $request->getUserVar('code'),
			'grant_type' => 'authorization_code',
			'client_id' => $plugin->getSetting($contextId, 'orcidClientId'),
			'client_secret' => $plugin->getSetting($contextId, 'orcidClientSecret')
		));
		$plugin->logInfo('POST ' . $url);
		$plugin->logInfo('Request header: ' . var_export($header, true));
		$plugin->logInfo('Request body: ' . $postData);
		// Use proxy if configured
		if ($httpProxyHost = Config::getVar('proxy', 'http_host')) {
			curl_setopt($ch, CURLOPT_PROXY, $httpProxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, Config::getVar('proxy', 'http_port', '80'));
			if ($username = Config::getVar('proxy', 'username')) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . Config::getVar('proxy', 'password'));
			}
		}
		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postData
		));
		$result = curl_exec($ch);
		if (!$result) {
			$plugin->logError('OrcidHandler::orcidverify - CURL error: ' . curl_error($ch));
			$templateMgr->assign('authFailure', true);
			$templateMgr->display($plugin->getTemplatePath() . self::TEMPLATE);
			return;
		}
		$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);		
		$plugin->logInfo('Response body: ' . $result);
		$response = json_decode($result, true);
		if (!isset($response['orcid']) || !isset($response['access_token'])) {			
			$plugin->logError("Response status: $httpstatus . Invalid ORCID response: $result");
			$templateMgr->assign('authFailure', true);
			$templateMgr->display($plugin->getTemplatePath() . self::TEMPLATE);
			return;
		}
		// Save the access token
		$orcidAccessExpiresOn = Carbon\Carbon::now();
		// expires_in field from the response contains the lifetime in seconds of the token
		// See https://members.orcid.org/api/get-oauthtoken
		$orcidAccessExpiresOn->addSeconds($response['expires_in']);
		$orcidUri = 'https://orcid.org/' . $response['orcid'];
		$authorToVerify->setData('orcid', $orcidUri);
		if ($plugin->getSetting($contextId, 'orcidProfileAPIPath') == ORCID_API_URL_MEMBER_SANDBOX ||
			$plugin->getSetting($contextId, 'orcidProfileAPIPath') == ORCID_API_URL_PUBLIC_SANDBOX) {
			// Set a flag to mark that the stored orcid id and access token came form the sandbox api			
			$authorToVerify->setData('orcidSandbox', true);
			$templateMgr->assign('orcid', 'https://sandbox.orcid.org/' . $response['orcid']);
		}
		else {
			$templateMgr->assign('orcid', $orcidUri);
		}
		// remove the email token
		$authorToVerify->setData('orcidEmailToken', null);
		// remove the access denied marker, because now the access was granted
		$authorToVerify->setData('orcidAccessDenied', null);
		$authorToVerify->setData('orcidAccessToken', $response['access_token']);
		$authorToVerify->setData('orcidAccessScope', $response['scope']);
		$authorToVerify->setData('orcidRefreshToken', $response['refresh_token']);
		$authorToVerify->setData('orcidAccessExpiresOn', $orcidAccessExpiresOn->toDateTimeString());		
		$authorDao->updateObject($authorToVerify);
		if( $plugin->isMemberApiEnabled($contextId) ) {			
			if ( $plugin->isSubmissionPublished($submissionId) ) {
				$templateMgr->assign('sendSubmission', true);
				$sendResult = $plugin->sendSubmissionToOrcid($submissionId, $request);	
				if ( $sendResult === true || ( is_array( $sendResult ) && $sendResult[$response['orcid']] ) ) {
					$templateMgr->assign('sendSubmissionSuccess', true);
				}
			}
			else {
				$templateMgr->assign('submissionNotPublished', true);
			}
		}		
		$templateMgr->assign([
			'verifySuccess' => true,
			'orcidIcon' => $plugin->getIcon()
			]);
		$templateMgr->display($plugin->getTemplatePath() . self::TEMPLATE);
	}
}

?>
