/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (Drupal) {
  var servicesFieldset = document.getElementById("inline-entity-form-field_health_services-form");

  var winnower = function winnower(context) {
    var selectedValues = context.querySelectorAll("#inline-entity-form-field_health_services-form td.inline-entity-form-node-label");

    var newServiceValueSelector = context.querySelectorAll("#inline-entity-form-field_health_services-form .field--name-field-service-name-and-descripti select option");

    var selectedValuesText = new Set();
    if (newServiceValueSelector) {
      selectedValues.forEach(function (i) {
        selectedValuesText.add(i.textContent);

        var nameSortString = i.nextElementSibling.textContent.replace("Optional", "z").replace(/ /gi, "-").toLowerCase() + "-" + i.textContent.toLowerCase();
        i.parentElement.setAttribute("aria-vc-name", nameSortString);

        i.parentElement.setAttribute("aria-required-status", i.nextElementSibling.textContent);
      });

      newServiceValueSelector.forEach(function (i) {
        i.classList.remove("hidden-option");

        if (selectedValuesText.has(i.text)) {
          i.classList.add("hidden-option");
        }
      });
    }
  };

  var sortByAriaVcName = function sortByAriaVcName(a, b) {
    if (a.getAttribute("aria-vc-name") < b.getAttribute("aria-vc-name")) return -1;
    if (a.getAttribute("aria-vc-name") > b.getAttribute("aria-vc-name")) return 1;
    return 0;
  };

  var alphaSortRows = function alphaSortRows(context) {
    var tableRowsArray = Array.from(context.querySelectorAll("tr[aria-vc-name]"));

    var sorted = tableRowsArray.sort(sortByAriaVcName);
    var servicesTable = context.querySelector("#inline-entity-form-field_health_services-form table tbody");

    if (servicesTable !== null) {
      sorted.forEach(function (e) {
        e.parentElement.appendChild(e);
      });
    }
  };

  Drupal.behaviors.vaGovPreventSelectDuplicates = {
    attach: function attach(context) {
      if (servicesFieldset) {
        winnower(context);
        alphaSortRows(context);
      }
    }
  };
})(Drupal);