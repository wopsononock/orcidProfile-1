{**
 * plugins/generic/orcidProfile/orcidProfile.tpl
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ORCID Profile plugin settings
 *
 *}
<p id="orcidProfileInstructions">
	{translate key="plugins.generic.orcidProfile.instructions"}
</p>
<!-- FIXME: Use the API URL from the settings form. -->
<form id="orcidProfileForm" action="https://orcid.org/oauth/authorize">
	<input type="hidden" name="client_id" value="{$orcidClientId|escape}" />
	<input type="hidden" name="response_type" value="code" />
	<input type="hidden" name="scope" value="/authenticate" />
	<input type="hidden" name="redirect_uri" value="{url page="orcidapi" op="orcidAuthorize"}" />
	<input type="submit" value="{translate key='plugins.generic.orcidProfile.submitAction'}" />
</form>
<hr />
