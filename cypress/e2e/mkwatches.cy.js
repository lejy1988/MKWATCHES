// ===== Homepage Test =====
describe('MK Watches - Homepage', () => {
  it('should load the homepage and show MK Watches text', () => {
    cy.visit('http://localhost/mkwatches');
    cy.contains('MK Watches'); // checks that the homepage contains this text
  });
});

// ===== Navbar Dropdown Tests =====
describe('Navbar Dropdown', () => {
  it('should open The Collection dropdown and go to Mens category', () => {
    cy.visit('http://localhost/mkwatches');
    cy.contains('The Collection').click(); // open dropdown
    cy.contains('a', 'Mens').click(); // click Mens
    cy.url().should('include', 'category=men'); // check URL
  });

  it('should open The Collection dropdown and go to Ladies category', () => {
    cy.visit('http://localhost/mkwatches');
    cy.contains('The Collection').click(); 
    cy.contains('a', 'Ladies').click(); 
    cy.url().should('include', 'category=women');
  });

  it('should open The Collection dropdown and go to Children category', () => {
    cy.visit('http://localhost/mkwatches');
    cy.contains('The Collection').click(); 
    cy.contains('a', 'Children').click(); 
    cy.url().should('include', 'category=kids');
  });
});

// ===== Product Modal Tests =====
describe('Product Modal', () => {
  it('should open the product modal when a product is clicked', () => {
    cy.visit('http://localhost/mkwatches');
    cy.get('.card .btn').first().click(); // clicks "View Details" of first product
    cy.url().should('include', 'category=men'); // navigates to product page
  });
});

/// ===== Shopping Cart Tests =====
describe('Shopping Cart', () => {

  beforeEach(() => {
    // Visit the landing page fresh before each test
    cy.visit('http://localhost/mkwatches');

    // Clear localStorage to start with empty cart
    cy.clearLocalStorage();
    cy.get('#cart-count').should('contain', '0');
  });

  it('should increase cart count when adding a visible product', () => {
    // Click first visible Add to Cart button
    cy.get('.product:visible').first().find('.add-to-cart').click();

    // Check cart counter
    cy.get('#cart-count').should('contain', '1');

    // Check cart items list
    cy.get('#cart-items').should('not.contain', 'No items in cart');

    // Check that toast notification appears
    cy.get('#cartToast').should('be.visible');
  });

  it('should add multiple products to the cart', () => {
    // Add first two visible products
    cy.get('.product:visible').eq(0).find('.add-to-cart').click();
    cy.get('.product:visible').eq(1).find('.add-to-cart').click();

    // Cart count should update
    cy.get('#cart-count').should('contain', '2');

    // Cart items should list both products
    cy.get('#cart-items div').should('have.length', 2);
  });

  it('should remove a product from the cart', () => {
    // Add first product
    cy.get('.product:visible').first().find('.add-to-cart').click();

    // Open cart dropdown
    cy.get('#cartDropdown').click();

    // Remove first item
    cy.get('.remove-item').first().click();

    // Cart count should go back to 0
    cy.get('#cart-count').should('contain', '0');

    // Cart items should show "No items in cart"
    cy.get('#cart-items').should('contain', 'No items in cart');
  });

  it('should update total price correctly', () => {
    // Add first two visible products
    cy.get('.product:visible').eq(0).find('.add-to-cart').click();
    cy.get('.product:visible').eq(1).find('.add-to-cart').click();

    // Calculate expected total from data-price attributes
    let total = 0;
    cy.get('.product:visible').each(($el, index) => {
      if (index < 2) {
        const price = parseFloat($el.find('.add-to-cart').data('price'));
        total += price;
      }
    }).then(() => {
      // Check total in cart UI
      cy.get('#cart-total').invoke('text').then((text) => {
        expect(parseFloat(text)).to.eq(total);
      });
    });
  });
});

