/**
 * @file cypress/tests/functional/02-Orcid-API.spec.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 */

describe("Authorization Redirect: "+Cypress.env('orcid')['url'], function () {

	var redirectUrl = "http://localhost/ojs/index.php/publicknowledge/orcidapi/orcidAuthorize?targetOp=profile";
	var orcidUrl = Cypress.env('orcid')['url'] + "/signin?oauth&client_id=" + Cypress.env('orcid')['clientId']
		+ "&response_type=code&scope=/activities/update&redirect_uri=" + redirectUrl;


	it('Check '+ Cypress.env('orcid')['url'], () => {
		cy.request(orcidUrl)
			.should((response) => {
				expect(response.status).to.eq(200)
			})
	});

	it('Authoritazation:  User Credentials',  ()=> {
		cy.visit(orcidUrl)
		cy.get('input[id="userId"]').clear().type(Cypress.env('orcid')['email']);
		cy.get('input[id="password"]').clear().type(Cypress.env('orcid')['emailPassword']);
		cy.get('button[id="form-sign-in-button"]').click()

	});

})

