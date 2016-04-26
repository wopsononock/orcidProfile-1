{**
 * plugins/generic/orcidProfile/orcidProfile.tpl
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ORCID Profile authorization form
 *
 *}
<form id="orcidProfileForm" action="{$orcidProfileOauthPath|escape}authorize">
	<input type="hidden" name="client_id" value="{$orcidClientId|escape}" />
	<input type="hidden" name="response_type" value="code" />
	<input type="hidden" name="scope" value="/authenticate" />
	<input type="hidden" name="redirect_uri" value="{url page="orcidapi" op="orcidAuthorize" targetOp=$targetOp}" />
	<button type='submit' form='orcidProfileForm' id="connect-orcid-button" onclick="openORCID()"><img id="orcid-id-logo" src="http://orcid.org/sites/default/files/images/orcid_24x24.png" width='24' height='24' alt="{translate key='plugins.generic.orcidProfile.submitAction'}"/>Create or Connect your ORCID iD</button>
</form>
<hr />
