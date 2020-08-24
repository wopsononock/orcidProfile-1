/**
 * @file cypress/tests/functional/CustomLocale.spec.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*/

describe("Test Orcid Plugin",function () {

	it("Contributor",function () {
		cy.login(Cypress.env('systemAdminUser'), Cypress.env('systemAdminPassword'));
		cy.get('ul[id=navigationUser]').find('a').contains('Dashboard').click({ force: true })
		cy.get('ul[id=navigationPrimary]').find('a').contains('Submission').click({ force: true })
		cy.get('div[id^=dashboard]').find('button').contains('Archive').click()
		cy.get('.pkpListPanel__content').find('button').get('.pkpListPanelItem__expander').click()
		cy.get('.pkpListPanel__content').find('a').contains('View Submission').click()
		cy.get('button[id=publication-button]').click()
		cy.waitJQuery();
		cy.get('div[id=publication]').find('button').contains('Create New Version').click()
		cy.waitJQuery()
		cy.get('.pkpModalConfirmButton').click()
		cy.waitJQuery()
		cy.get('div[id=publication]').find('span').contains('Unpublished').should('be.visible')
		cy.get('button[id="contributors-button"]').click()
		cy.get('div[id=publication]').find('button').contains('Publish').click()
		cy.waitJQuery()
		cy.get('.pkpFormPages').get('.pkpButton').contains('Publish').click()







	})
})
