{**
 * plugins/generic/orcidProfile/settingsForm.tpl
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ORCID Profile plugin settings
 *
 *}
{strip}
{assign var="pageTitle" value="plugins.generic.orcidProfile.manager.orcidProfileSettings"}
{include file="common/header.tpl"}
{/strip}
<div id="orcidProfileSettings">
<div id="description">{translate key="plugins.generic.orcidProfile.manager.settings.description"}</div>

<div class="separator"></div>

<form method="post" action="{plugin_url path="settings"}">
{include file="common/formErrors.tpl"}

<table width="100%" class="data">
	<tr valign="top">
		<td width="20%" class="label">{fieldLabel name="orcidProfileAPIPath" required="true" key="plugins.generic.orcidProfile.manager.settings.orcidProfileAPIPath"}</td>
		<td width="80%" class="value"><input type="text" name="orcidProfileAPIPath" id="orcidProfileAPIPath" value="{$orcidProfileAPIPath|escape}" size="40" class="textField" />
			<br />
			<span class="instruct">{translate key="plugins.generic.orcidProfile.manager.settings.orcidProfileAPIPathInstructions"}</span>
		</td>
	</tr>
</table>

<input type="submit" name="save" class="button defaultButton" value="{translate key="common.save"}"/><input type="button" class="button" value="{translate key="common.cancel"}" onclick="history.go(-1)"/>
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
{include file="common/footer.tpl"}
