/**
 * @file cypress/tests/functional/CustomLocale.spec.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

describe("Test Orcid Plugin", function () {

/*	it('Configure Plugin', function () {

		cy.login('admin', 'admin', 'publicknowledge');
		cy.get('ul[id="navigationPrimary"] a:contains("Settings")').click();
		cy.get('ul[id="navigationPrimary"] a:contains("Website")').click();
		cy.get('button[id="plugins-button"]').click();
		cy.get('input[id^="select-cell-orcidprofileplugin-enabled"]').check();
		cy.get('#component-grid-settings-plugins-settingsplugingrid-category-generic-row-orcidprofileplugin > .first_column > .show_extras').click();
		cy.waitJQuery();
		cy.get('a[id^="component-grid-settings-plugins-settingsplugingrid-category-generic-row-orcidprofileplugin-settings-button-"]').click();
		cy.waitJQuery();
		cy.get('#orcidProfileAPIPath').select(Cypress.env('orcid')['apiPath']);
		cy.get('input[id^="orcidClientId-"]').clear().type(Cypress.env('orcid')['clientId']);
		cy.get('input[id^="orcidClientSecret-"]').clear().type(Cypress.env('orcid')['clientSecret']);
		cy.get('#sendMailToAuthorsOnPublication').check('on');
		cy.get('#orcidProfileSettingsForm > #orcidProfileSettings > .section > div > #logLevel').select('ALL');
		cy.screenshot('01-Orcid-Settings')
		cy.get('.content > #orcidProfileSettingsForm > #orcidProfileSettings > .section ').find('button').contains('OK').click();
		cy.logout()
	})*/

	it ('Authorize user', function (){
		let orcidOp = "/activities/update";
		let redirectUrl = "http://localhost/ojs/index.php/publicknowledge/orcidapi/orcidAuthorize?targetOp=profile";
		cy.visit(Cypress.env('orcid')['url'] + "/signin?oauth&client_id=" + Cypress.env('orcid')['clientId'] + "&response_type=code&scope=" + orcidOp + "&redirect_uri=" + redirectUrl);
		cy.get('input[id="userId"]').clear().type(Cypress.env('orcid')['email']);
		cy.get('input[id="password"]').clear().type(Cypress.env('orcid')['emailPassword']);
	})

	it('User Profile check', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		cy.get('ul[id="navigationUser"]  a:contains("admin")').focus();
		cy.get('ul[id="navigationUser"] a:contains("View Profile")').click({force:true});
		cy.get('div[id="profileTabs"] a:contains("Public")').click();
		cy.get('button[id="connect-orcid-button"]').click();
	})


})
