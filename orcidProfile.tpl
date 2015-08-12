{**
 * plugins/generic/orcidProfile/orcidProfile.tpl
 *
 * Copyright (c) 2015 University of Pittsburgh
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * ORCID Profile plugin settings
 *
 *}
{literal}
<script type="text/javascript">
$( document ).ready( function () {
	$( "#orcidProfileForm" ).submit( function ( event ) {
		event.preventDefault();
		$.ajax( {
			url: $( this ).attr('action')+'?'+$( this ).serialize(),
			dataType: 'json',
			success: function ( data ) {
				$.each( data, function( key, val ) {
					$('#'+key).val( val );
					$('#'+key).addClass( 'modified' );
				});
			}
		});
	});
} );
</script>
{/literal}
<p id="orcidProfileInstructions">
{translate key="plugins.generic.orcidProfile.instructions"}
</p>
<form id="orcidProfileForm" action="{url page='orcidapi'}">
<label for="q">{translate key="plugins.generic.orcidProfile.emailOrOrcid"}</label><input type="text" name="q" /><input type="submit" value="{translate key='plugins.generic.orcidProfile.submitAction'}" />
</form>
<hr />
