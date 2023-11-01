import { Then } from "@badeball/cypress-cucumber-preprocessor";

Then("an element with the selector {string} should exist", (selector) =>
  cy.get(selector).should("exist")
);

Then("I wait for an element with the selector {string} to exist", (selector) =>
  cy
    .get(selector, {
      timeout: 30000,
    })
    .should("exist")
);

Then(
  "I wait for an element with the selector {string} to not exist",
  (selector) =>
    cy
      .get(selector, {
        timeout: 30000,
      })
      .should("not.exist")
);

Then("an element with the selector {string} should not exist", (selector) =>
  cy.get(selector).should("not.exist")
);

Then(`{string} should exist`, (text) => cy.contains(text).should("exist"));
Then(`{string} should not exist`, (text) =>
  cy.contains(text).should("not.exist")
);

Then("an element with the xpath {string} should exist", (expression) =>
  cy.xpath(expression).should("exist")
);
Then("an element with the xpath {string} should not exist", (expression) =>
  cy.xpath(expression).should("not.exist")
);

Then("xpath {string} should exist", (expression) =>
  cy.xpath(expression).should("exist")
);
Then("xpath {string} should not exist", (expression) =>
  cy.xpath(expression).should("not.exist")
);

Then("an image with the selector {string} should exist", (selector) => {
  cy.get(selector)
    .should("exist")
    .and(($img) => {
      expect($img[0].naturalWidth).to.be.greaterThan(0);
      expect($img[0].naturalHeight).to.be.greaterThan(0);
    });
});

Then("the primary tab {string} should exist", (text) =>
  cy.get(".tabs__tab a").contains(text).should("exist")
);

Then("the primary tab {string} should not exist", (text) =>
  cy.get(".tabs__tab a").contains(text).should("not.exist")
);
