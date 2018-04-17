{**
 * plugins/generic/orcidProfile/authorFormOrcid.tpl
 *
 * Copyright (c) 2017-2018 University Library Heidelberg
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Extensions to Submissioni Metadata Author add/edit Form
 *
 *}
{fbvFormSection list="true" title="ORCID" translate=false}
	{if $orcidAccessToken}
    	<p>{translate key='plugins.generic.orcidProfile.author.accessTokenStored'} {$orcidAccessExpiresOn|date_format:$datetimeFormatShort}</p>
    {/if}
    {if $orcidAccessDenied}
    	<p>{translate key='plugins.generic.orcidProfile.author.accessDenied'} {$orcidAccessDenied|date_format:$datetimeFormatShort}</p>
    {/if}
    {fbvElement type="checkbox" label="plugins.generic.orcidProfile.author.requestAuthorization" id="requestOrcidAuthorization" checked=false}
    
{/fbvFormSection}
