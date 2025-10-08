describe('MK Watches Authentication Flow', () => {
  const email = `testuser${Date.now()}@example.com`;
  const password = 'Password123!';
  const name = 'Cypress User';
  const postcode = 'AB12 3CD';

  it('Registers a new user', () => {
    cy.visit('http://localhost/mkwatches/register.php');

    cy.get('input[name="name"]').type(name);
    cy.get('input[name="email"]').type(email);
    cy.get('input[name="postcode"]').type(postcode);
    cy.get('input[name="password"]').type(password);
    cy.get('input[name="confirm_password"]').type(password);
    cy.get('input[type="submit"]').click();

    cy.contains('Registration successful').should('exist');
  });

  it('Logs in the new user', () => {
    cy.visit('http://localhost/mkwatches/Loginpage.php');

    cy.get('input[name="email"]').type(email);
    cy.get('input[name="password"]').type(password);
    cy.get('input[type="submit"]').click();

    cy.url().should('include', 'dashboard.php');
    cy.contains(`Welcome, ${name}`).should('exist');
  });

  it('Logs out the user', () => {
    cy.get('a[href="logout_user.php"]').click();
    cy.url().should('include', 'index.php');
    cy.contains('You have been logged out successfully').should('exist');

    // Attempt to access protected page
    cy.visit('http://localhost/mkwatches/products.php');
    cy.url().should('include', 'Loginpage.php');
  });
});
