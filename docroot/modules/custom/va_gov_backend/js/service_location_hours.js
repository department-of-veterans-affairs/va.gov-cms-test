/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal, once) {
  var displayHours = function displayHours(value, toggle, table) {
    if (toggle.checked && table) {
      if (toggle.value === value) {
        table.style.display = "block";
        if (value === "0") {
          $(once("button-build", table)).each(function makeToolTip() {
            var button = document.createElement("button");
            button.className = "tooltip-toggle";
            button.value = "Why can't I edit this? VHA keeps these descriptions standardized to help Veterans identify the services they need.";
            button.type = "button";
            button.ariaLabel = "tooltip";
            button.setAttribute("data-tippy", "Why can't I edit this?\nVHA keeps these descriptions standardized to help Veterans identify the services they need.");
            button.setAttribute("data-tippy-pos", "right");
            button.setAttribute("data-tippy-animate", "fade");
            button.setAttribute("data-tippy-size", "large");
            table.className = "no-content health_service_text_container field-group-tooltip tooltip-layout centralized css-tooltip";
            table.appendChild(button);
            window.tippy(button, {
              content: function content() {
                return button.value;
              },
              theme: "tippy_popover",
              placement: "right",
              arrow: true,
              offset: [15, 0]
            });
          });
        }
      } else {
        table.style.display = "none";
      }
    }
  };
  Drupal.behaviors.vaGovServiceLocationHours = {
    attach: function attach(context) {
      var hourSelects = document.querySelectorAll(".field--name-field-hours input");
      context.addEventListener("load", function () {
        hourSelects.forEach(function (hourSelect) {
          var hours = hourSelect.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
          var facilityHours = hourSelect.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
          displayHours("2", hourSelect, hours);
          displayHours("0", hourSelect, facilityHours);
        });
      });
      context.addEventListener("click", function () {
        hourSelects.forEach(function (hourSelect) {
          var hours = hourSelect.parentElement.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
          var facilityHours = hourSelect.parentElement.parentElement.parentElement.parentElement.nextElementSibling;
          displayHours("2", hourSelect, hours);
          displayHours("0", hourSelect, facilityHours);
        });
      });
    }
  };
})(jQuery, window.Drupal, window.once);