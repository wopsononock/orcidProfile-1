/**
 * @file cypress/tests/functional/02-Orcid-API.spec.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

describe("Test Orcid Plugin", function () {

	var redirectUrl = "http://localhost/ojs/index.php/publicknowledge/orcidapi/orcidAuthorize?targetOp=profile";
	var orcidUrl = Cypress.env('orcid')['url'] + "/signin?oauth&client_id=" + Cypress.env('orcid')['clientId'] + "&response_type=code&scope=/activities/update&redirect_uri=" + redirectUrl;

	it('Authorize user', function () {
		cy.visit(orcidUrl)
		cy.get('input[id="userId"]').clear().type(Cypress.env('orcid')['email']);
		cy.get('input[id="password"]').clear().type(Cypress.env('orcid')['emailPassword']);
		cy.get('button[id="form-sign-in-button"]').click()
		cy.wait(3000)
		cy.login('admin', 'admin', 'publicknowledge');

	});

})

