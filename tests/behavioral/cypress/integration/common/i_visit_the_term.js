import { Given } from "cypress-cucumber-preprocessor/steps";

Given(`I visit the term`, () => {
  cy.get('@termId').then((tid) => cy.visit(`/term/${tid}`));
});

Given(`I edit the term`, () => {
  cy.get('@termId').then((tid) => cy.visit(`/term/${tid}/edit`));
});
