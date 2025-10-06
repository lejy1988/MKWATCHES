// cypress/e2e/mkwatches.cy.js

// ===== Homepage Test =====
describe('MK Watches - Homepage', () => {
  it('should load the homepage and show MK Watches text', () => {
    cy.visit('http://localhost/mkwatches');
    cy.contains('MK Watches'); // checks that the homepage contains this text
  });
});

// ===== Navbar Dropdown Tests =====
describe('Navbar Dropdown', () => {
  beforeEach(() => {
    cy.visit('http://localhost/mkwatches');
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
    cy.get('.card .view-details-btn').first().click();
    cy.get('#detailsModal').should('be.visible');
  });
});

// ===== Shopping Cart Tests =====
describe('Shopping Cart', () => {

  beforeEach(() => {
    // Visit product page with all products visible
    cy.visit('http://localhost/mkwatches/admin/adminproduct.php?category=all');

    // Clear localStorage to start fresh
    cy.clearLocalStorage();
    cy.get('#cart-count').should('contain', '0');
  });

  it('should increase cart count when adding a visible product', () => {
    cy.get('.product:visible').should('have.length.greaterThan', 0);
    cy.get('.product:visible').first().find('.add-to-cart').click();

    cy.get('#cart-count').should('contain', '1');
    cy.get('#cart-items').should('not.contain', 'No items in cart');
    cy.get('#cartToast').should('be.visible');
  });

  it('should remove a product from the cart', () => {
    cy.get('.product:visible').first().find('.add-to-cart').click();
    cy.get('#cartDropdown').click();
    cy.get('#cart-items button').first().click();

    cy.get('#cart-count').should('contain', '0');
    cy.get('#cart-items').should('contain', 'No items in cart');
  });

  it('should update total price correctly', () => {
    cy.get('.product:visible').eq(0).find('.add-to-cart').click();
    cy.get('.product:visible').eq(1).find('.add-to-cart').click();

    let total = 0;
    cy.get('.product:visible').each(($el, index) => {
      if (index < 2) {
        const price = parseFloat($el.find('.add-to-cart').attr('data-price'));
        total += price;
      }
    }).then(() => {
      cy.get('#cart-total').invoke('text').then((text) => {
        expect(parseFloat(text)).to.eq(total);
      });
    });
  });

});
