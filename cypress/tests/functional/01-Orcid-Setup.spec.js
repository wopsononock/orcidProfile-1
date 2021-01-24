/**
 * @file cypress/tests/functional/01-Orcid-Setup.spec.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */


describe("Test Orcid Plugin", function () {
	it('Configure Plugin', function () {
		cy.login('admin', 'admin', 'publicknowledge');
		//cy.get('li a:contains("Settings")').click();
		cy.get('li a:contains("Website")').click({force: true});
		cy.get('button[id="plugins-button"]').click();
		cy.get('input[id^="select-cell-orcidprofileplugin-enabled"]').check();
		cy.get('#component-grid-settings-plugins-settingsplugingrid-category-generic-row-orcidprofileplugin > .first_column > .show_extras').click();
		cy.waitJQuery();
		cy.get('a[id^="component-grid-settings-plugins-settingsplugingrid-category-generic-row-orcidprofileplugin-settings-button"]').click();
		cy.waitJQuery();
		cy.get('#orcidProfileAPIPath').select(Cypress.env('orcid_apiType'));
		cy.get('input[id^="orcidClientId-"]').clear().type(Cypress.env('orcid_clientId'));
		cy.get('input[id^="orcidClientSecret-"]').clear().type(Cypress.env('orcid_clientSecret'));
		cy.get('#sendMailToAuthorsOnPublication').check('on');
		cy.get('#orcidProfileSettingsForm > #orcidProfileSettings > .section > div > #logLevel').select('ALL');
		cy.get('.content > #orcidProfileSettingsForm > #orcidProfileSettings > .section ').find('button').contains('OK').click();

	});

})

