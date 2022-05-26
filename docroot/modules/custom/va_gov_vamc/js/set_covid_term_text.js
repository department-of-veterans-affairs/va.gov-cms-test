/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function (Drupal) {
  var statusId = void 0;
  var textSetter = function textSetter() {
    var fieldset = document.getElementById("covid-safety-guidelines-status-text");

    if (document.getElementById("covid-safety-guidelines-status-text-target")) {
      document.getElementById("covid-safety-guidelines-status-text-target").remove();
      document.getElementById("covid-safety-guidelines-status-text-prefix").remove();
    }

    var covidStatusValue = document.querySelectorAll(".form-item--field-supplemental-status input");

    covidStatusValue.forEach(function (element) {
      if (element.checked) {
        statusId = element.value;
      }
    });

    fieldset.style.display = "none";

    if (statusId) {
      fieldset.style.display = "block";

      var covidStatusTextDiv = document.createElement("div");
      covidStatusTextDiv.id = "covid-safety-guidelines-status-text-target";
      covidStatusTextDiv.innerHTML = drupalSettings.vamcCovidStatusTermText[statusId].name + drupalSettings.vamcCovidStatusTermText[statusId].description;
      fieldset.append(covidStatusTextDiv);

      var covidStatusTextDivPrefix = document.createElement("div");
      covidStatusTextDivPrefix.id = "covid-safety-guidelines-status-text-prefix";
      covidStatusTextDivPrefix.innerHTML = '<h5>Guidelines</h5><div class="fieldset__description">Site visitors will see the following message for the level you selected.</div>';
      fieldset.before(covidStatusTextDivPrefix);
    }
  };

  Drupal.behaviors.vaGovSetCovidTermText = {
    attach: function attach() {
      window.addEventListener("DOMContentLoaded", textSetter);
      document.getElementById("group-covid-19-safety-guidelines").addEventListener("click", textSetter);
    }
  };
})(Drupal);