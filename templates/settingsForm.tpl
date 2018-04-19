{**
 * plugins/generic/orcidProfile/settingsForm.tpl
 *
 * Copyright (c) 2015-2018 University of Pittsburgh
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Copyright (c) 2017-2018 University Library Heidelberg
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ORCID Profile plugin settings
 *
 *}
<div id="orcidProfileSettings">

<div id="description">
	{translate key="plugins.generic.orcidProfile.manager.settings.description" }
</div>

<h3>{translate key="plugins.generic.orcidProfile.manager.orcidProfileSettings"}</h3>


<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#orcidProfileSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="orcidProfileSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="orcidProfileSettingsFormNotification"}
	{fbvFormArea id="orcidProfileSettings"}
	{if $globallyConfigured}
	<p>
	{translate key="plugins.generic.orcidProfile.manager.settings.description.globallyconfigured" }
    </p>
	<table width="100%" class="data">
		<tr valign="top">
			<td width="20%" class="label">{translate key="plugins.generic.orcidProfile.manager.settings.orcidProfileAPIPath"}</td>
			<td width="80%" class="value">
				{$orcidProfileAPIPath}
			</td>
		</tr>
		<tr valign="top">
			<td class="label">{translate key="plugins.generic.orcidProfile.manager.settings.orcidClientId"}</td>
			<td class="value">
				{$orcidClientId|escape}
			</td>
		</tr>		
		<tr valign="top">
			<td class="label">{translate key="plugins.generic.orcidProfile.manager.settings.orcidClientSecret"}</td>
			<td class="value">
				<i>hidden</i>
			</td>
		</tr>
	</table>	
	{else}	
	<table width="100%" class="data">
		<tr valign="top">
			<td width="20%" class="label">{fieldLabel name="orcidProfileAPIPath" required="true" key="plugins.generic.orcidProfile.manager.settings.orcidProfileAPIPath"}</td>
			<td width="80%" class="value">
				{html_options_translate name="orcidProfileAPIPath" options=$orcidApiUrls selected=$orcidProfileAPIPath}
			</td>
		</tr>
		<tr valign="top">
			<td class="label">{fieldLabel name="orcidClientId" required="true" key="plugins.generic.orcidProfile.manager.settings.orcidClientId"}</td>
			<td class="value">
				<input type="text" name="orcidClientId" id="orcidClientId" value="{$orcidClientId|escape}" size="40" class="textField" />
			</td>
		</tr>
		<tr valign="top">
			<td class="label">{fieldLabel name="orcidClientSecret" required="true" key="plugins.generic.orcidProfile.manager.settings.orcidClientSecret"}</td>
			<td class="value">
				<input type="text" name="orcidClientSecret" id="orcidClientSecret" value="{$orcidClientSecret|escape}" size="40" class="textField" />
			</td>
		</tr>
	</table>
	{/if}	
	{fbvFormSection for="sendMailToAuthorOnPublication" title="plugins.generic.orcidProfile.manager.settings.mailSectionTitle" list="true"}
		{fbvElement type="checkbox" name="sendMailToAuthorsOnPublication" label="plugins.generic.orcidProfile.manager.settings.sendMailToAuthorsOnPublication" id="sendMailToAuthorsOnPublication" checked=$sendMailToAuthorsOnPublication}
	{/fbvFormSection}
	{fbvFormSection for="logLevel" title="plugins.generic.orcidProfile.manager.settings.logSectionTitle"}
		{fbvElement id="logLevel" name="logLevel" type="select" from=$logLevelOptions selected=$logLevel}
	{/fbvFormSection}
	{/fbvFormArea}
	{fbvFormButtons}	
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
