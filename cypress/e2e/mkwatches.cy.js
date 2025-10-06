// ===== MK Watches Cypress Tests =====

// ===== Homepage Test =====
describe('MK Watches - Homepage', () => {
  it('should load the homepage and show MK Watches text', () => {
    cy.visit('http://localhost/mkwatches/index.html');
    cy.contains('MK Watches'); // homepage contains text
  });
});

// ===== Navbar Dropdown Tests =====
describe('Navbar Dropdown', () => {
  beforeEach(() => {
    cy.visit('http://localhost/mkwatches/index.html');
  });

  it('should open The Collection dropdown and go to Mens category', () => {
    cy.contains('The Collection').click();
    cy.contains('a', 'Mens').click();
    cy.url().should('include', 'category=men');
  });

  it('should open The Collection dropdown and go to Ladies category', () => {
    cy.contains('The Collection').click();
    cy.contains('a', 'Ladies').click();
    cy.url().should('include', 'category=women');
  });

  it('should open The Collection dropdown and go to Children category', () => {
    cy.contains('The Collection').click();
    cy.contains('a', 'Children').click();
    cy.url().should('include', 'category=kids');
  });
});

// ===== Product Modal Tests =====
describe('Product Modal', () => {
  it('should open the product modal when a product is clicked', () => {
    cy.visit('http://localhost/mkwatches/admin/adminproduct.php?category=all');
    cy.get('.product').should('have.length.greaterThan', 0); // wait for products
    cy.get('.product').first().find('.view-details-btn').click();
    cy.get('#detailsModal').should('be.visible'); // modal shows up
  });
});

// ===== Shopping Cart Tests =====
describe('Shopping Cart', () => {

  beforeEach(() => {
    cy.visit('http://localhost/mkwatches/admin/adminproduct.php?category=all');
    cy.clearLocalStorage();
    cy.get('#cart-count').should('contain', '0');
    cy.get('.product').should('have.length.greaterThan', 0); // products loaded
  });

  it('should increase cart count when adding a product', () => {
    cy.get('.product').first().find('.add-to-cart').click({ force: true });
    cy.get('#cart-count').should('contain', '1');
    cy.get('#cart-items').should('not.contain', 'No items in cart');
    cy.get('#cartToast').should('be.visible');
  });

  it('should add multiple products to the cart', () => {
    cy.get('.product').eq(0).find('.add-to-cart').click({ force: true });
    cy.get('.product').eq(1).find('.add-to-cart').click({ force: true });

    cy.get('#cart-count').should('contain', '2');
    cy.get('#cart-items div').should('have.length', 2);
  });

  it('should remove a product from the cart', () => {
    cy.get('.product').first().find('.add-to-cart').click({ force: true });
    cy.get('#cartDropdown').click();
    cy.get('#cart-items button').first().click();
    cy.get('#cart-count').should('contain', '0');
    cy.get('#cart-items').should('contain', 'No items in cart');
  });

  it('should update total price correctly', () => {
    let total = 0;

    cy.get('.product').eq(0).then($el => {
      total += parseFloat($el.find('.add-to-cart').attr('data-price'));
      cy.wrap($el).find('.add-to-cart').click({ force: true });
    });

    cy.get('.product').eq(1).then($el => {
      total += parseFloat($el.find('.add-to-cart').attr('data-price'));
      cy.wrap($el).find('.add-to-cart').click({ force: true });
    });

    cy.get('#cart-total').invoke('text').then(text => {
      expect(parseFloat(text)).to.eq(total);
    });
  });
});
