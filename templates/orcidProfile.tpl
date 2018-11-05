{**
 * plugins/generic/orcidProfile/orcidProfile.tpl
 *
 * Copyright (c) 2015-2018 University of Pittsburgh
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ORCID Profile authorization form
 *
 *}
<script type="text/javascript">
	function openORCID() {ldelim}
		// First sign out from ORCID to make sure no other user is logged in
		// with ORCID
		$.ajax({ldelim}
            url: '{$orcidUrl|escape}userStatus.json?logUserOut=true',
            dataType: 'jsonp',
            success: function(result,status,xhr) {ldelim}
                console.log("ORCID Logged In: " + result.loggedIn);
            {rdelim},
            error: function (xhr, status, error) {ldelim}
                console.log(status + ", error: " + error);
            {rdelim}
        {rdelim});
		var oauthWindow = window.open("{$orcidOAuthUrl}", "_blank", "toolbar=no, scrollbars=yes, width=500, height=700, top=500, left=500");
		oauthWindow.opener = self;
		return false;
	{rdelim}
{if $targetOp eq 'profile'}
	$(document).ready(function() {ldelim}
		$('input[name=orcid]').attr('readonly', "true");
	{rdelim});
{/if}
</script>

<button id="connect-orcid-button" class="cmp_button" onclick="return openORCID();">
	{$orcidIcon}
	{translate key='plugins.generic.orcidProfile.connect'}
</button>

{if $targetOp eq 'register'}
	{fbvElement type="hidden" name="orcid" id="orcid" value=$orcid maxlength="37"}
{/if}
