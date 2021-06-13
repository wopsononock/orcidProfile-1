describe('Create co-author Orcid entry', function() {
	it('Navigate to co authors', function() {
		cy.login('dbarnes');
		cy.visit('/index.php/publicknowledge/submissions');
		cy.log('Find submission');
		cy.contains('Signalling Theory Dividends').parent().parent().click();

		cy.log('Click Publications tab');
		cy.get('#publication-button').click();

		cy.log('Click Contributors tab');
		cy.get('#contributors-button').click();

		cy.log('Click Downwards Arrow')
		cy.get('[id^=component-grid-users-author-authorgrid]').get('tbody > #component-grid-users-author-authorgrid-row-1 > .first_column > .show_extras').click()

		cy.log('Click Edit button')
		cy.get('[id^=component-grid-users-author-authorgrid]').get('tbody > #component-grid-users-author-authorgrid-row-1-control-row > td').get('[id^=component-grid-users-author-authorgrid-row-1-editAuthor-button]').click()

		cy.log('Check Orcid request')
		cy.get('.section > .checkbox_and_radiobutton > li > label > #requestOrcidAuthorization').check('on')

		cy.log('Click Save button')
		cy.get('.pkp_modal_panel > .content > #editAuthor > .section').get('[name=submitFormButton]').contains('Save').click()

	})

})

