/* eslint-disable valid-jsdoc */
/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable no-console */
/// <reference types="cypress" />
// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

const cucumber = require("cypress-cucumber-preprocessor").default;
const getCompareSnapshotsPlugin = require("cypress-visual-regression/dist/plugin");
const { cloudPlugin } = require("cypress-cloud/plugin");

// This function is called when a project is opened or re-opened (e.g. due to
// the project's config changing)

/**
 * @type {Cypress.PluginConfig}
 */
module.exports = (on, config) => {
  on("file:preprocessor", cucumber());
  getCompareSnapshotsPlugin(on, config);
  cloudPlugin(on, config);
  on("task", {
    log(message) {
      console.log(message);
      return null;
    },
    table(message) {
      console.table(message);
      return null;
    },
  });
};
