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
<form id="orcidProfileForm" action="https://orcid.org/oauth/authorize?client_id={$orcidClientId|escape}&response_type=code&scope=/authenticate&redirect_uri={url|urlencode page="orcidapi" escape="false"}">
	<input type="submit" value="{translate key='plugins.generic.orcidProfile.submitAction'}" />
</form>
<hr />
