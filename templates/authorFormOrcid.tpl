{**
 * plugins/generic/orcidProfile/authorFormOrcid.tpl
 *
 * Copyright (c) 2017 University Library Heidelberg
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Extensions to Submissioni Metadata Author add/edit Form
 *
 *}
{** TODO add message if an ORCID access token for this author is already stored*}
{fbvFormSection list="true" title="ORCID" translate=false}
    {fbvElement type="checkbox" label="plugins.generic.orcidProfile.author.requestAuthorization" id="requestOrcidAuthorization" checked=false}
{/fbvFormSection}
