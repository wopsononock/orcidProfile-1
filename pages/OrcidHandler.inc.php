<?php

/**
 * @file plugins/generic/orcidProfile/OrcidHandler.inc.php
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2 or later. For full terms see the file docs/COPYING.
 *
 * @class OrcidHandler
 * @ingroup plugins_generic_orcidprofile
 *
 * @brief Pass off internal ORCID API requests to ORCID
 */

import('classes.handler.Handler');

class OrcidHandler extends Handler {
	/**
	 * Authorize handler
	 * @param $args array
	 * @param $request Request
	 */
	function orcidAuthorize($args, &$request) {
		define('OAUTH_TOKEN_URL', 'https://pub.orcid.org/oauth/token'); // public

		$journal = Request::getJournal();
		$op = Request::getRequestedOp();
		$plugin =& PluginRegistry::getPlugin('generic', 'orcidprofileplugin');

		// fetch the access token
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => OAUTH_TOKEN_URL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Accept: application/json'),
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query(array(
				'code' => Request::getUserVar('code'),
				'grant_type' => 'authorization_code',
				'client_id' => $plugin->getSetting($journal->getId(), 'orcidClientId'),
				'client_secret' => $plugin->getSetting($journal->getId(), 'orcidClientSecret')
			))
		));
		$result = curl_exec($curl);
		$response = json_decode($result, true);

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'http://pub.orcid.org/v1.2/' . htmlspecialchars($response['orcid']) . '/orcid-profile',
			CURLOPT_POST => false,
			CURLOPT_HTTPHEADER => array('Accept: application/json'),
		));
		$result = curl_exec($curl);
		$info = curl_getinfo($curl);
		if ($info['http_code'] == 200) {
			$json = json_decode($result, true);
		}

		Request::redirect(null, 'user', 'register', null, array(
			'firstName' => $json['orcid-profile']['orcid-bio']['personal-details']['given-names']['value'],
			'lastName' => $json['orcid-profile']['orcid-bio']['personal-details']['family-name']['value'],
			'email' => $json['orcid-profile']['orcid-bio']['contact-details']['email'][0]['value'],
			'orcid' => $response['orcid'],
			'hideOrcid' => true
		));
		
	}
}

?>
