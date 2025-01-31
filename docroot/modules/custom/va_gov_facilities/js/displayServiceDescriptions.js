/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal) {
  Drupal.behaviors.vaGovDisplayServiceDescriptions = {
    attach: function attach(context) {
      var loadItems = function loadItems(service) {
        if (context.getElementById(service.id + "-health_service_text_container")) {
          context.getElementById(service.id + "-health_service_text_container").remove();
        }
        if (context.getElementById(service.id + "-services-general-description")) {
          context.getElementById(service.id + "-services-general-description").remove();
        }
        var tricareSystem = Drupal.isTricareSystem(context);
        var serviceSelector = context.querySelector(".field--name-field-service-name-and-descripti select");
        var serviceSelectorSelectionClass = "empty-display-none";
        if (serviceSelector !== undefined && serviceSelector.options !== undefined && serviceSelector.options[serviceSelector.selectedIndex].value !== "_none") {
          serviceSelectorSelectionClass = "not-empty-display-block";
        }
        var div = context.createElement("div");
        var button = context.createElement("button");
        button.className = "tooltip-toggle css-tooltip-toggle";
        button.value = "Why can't I edit this? National editors keep these descriptions standardized to help Veterans identify the services they need.";
        button.type = "button";
        button.ariaLabel = "tooltip";
        button.setAttribute("data-tippy", "Why can't I edit this?\nNational editors keep these descriptions standardized to help Veterans identify the services they need.");
        button.setAttribute("data-tippy-pos", "right");
        button.setAttribute("data-tippy-animate", "fade");
        button.setAttribute("data-tippy-size", "large");
        button.id = "service-tooltip css-tooltip";
        div.id = service.id + "-health_service_text_container";
        div.className = "no-content health_service_text_container field-group-tooltip tooltip-layout centralized css-tooltip " + serviceSelectorSelectionClass;
        div.appendChild(button);
        if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].type !== "") {
          var p1 = context.createElement("p");
          var s1 = context.createElement("strong");
          var typeString = drupalSettings.availableHealthServices[service.value].type;
          p1.textContent = typeString;
          s1.textContent = "Type of care: ";
          div.classList.remove("no-content");
          div.appendChild(p1);
          p1.prepend(s1);
        }
        if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].name !== "") {
          var p2 = context.createElement("p");
          var s2 = context.createElement("strong");
          p2.textContent = drupalSettings.availableHealthServices[service.value].name;
          s2.textContent = "Patient friendly name: ";
          div.classList.remove("no-content");
          div.appendChild(p2);
          p2.prepend(s2);
        }
        if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].conditions !== "") {
          var p3 = context.createElement("p");
          var s3 = context.createElement("strong");
          p3.textContent = drupalSettings.availableHealthServices[service.value].conditions.replace(/&nbsp;/g, " ");
          s3.textContent = "Common conditions: ";
          div.classList.remove("no-content");
          div.appendChild(p3);
          p3.prepend(s3);
        }
        if (drupalSettings.availableHealthServices[service.value] !== undefined && (drupalSettings.availableHealthServices[service.value].description !== "" || drupalSettings.availableHealthServices[service.value].tricare_description !== "")) {
          var p4 = context.createElement("p");
          var s4 = context.createElement("strong");
          if (tricareSystem === true && drupalSettings.availableHealthServices[service.value].tricare_description !== "") {
            p4.textContent = drupalSettings.availableHealthServices[service.value].tricare_description.replace(/&nbsp;/g, " ");
          }
          if (!p4.textContent && drupalSettings.availableHealthServices[service.value].description !== "") {
            p4.textContent = drupalSettings.availableHealthServices[service.value].description.replace(/&nbsp;/g, " ");
          }
          if (p4.textContent) {
            s4.textContent = drupalSettings.availableHealthServices[service.value].vc_vocabulary_service_description_label + ": ";
            div.classList.remove("no-content");
            div.appendChild(p4);
            p4.prepend(s4);
          }
        }
        var vbaForm = document.querySelector('[data-drupal-selector="node-vba-facility-service-form"]');
        var vbaFormEdit = document.querySelector('[data-drupal-selector="node-vba-facility-service-edit-form"]');
        if (vbaForm || vbaFormEdit) {
          if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].vba_regional_service_header !== "") {
            var p5 = context.createElement("p");
            var s5 = context.createElement("strong");
            p5.textContent = drupalSettings.availableHealthServices[service.value].vba_regional_service_header.replace(/&nbsp;/g, " ");
            s5.textContent = "Regional service header: ";
            div.classList.remove("no-content");
            div.appendChild(p5);
            p5.prepend(s5);
          }
          if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].vba_regional_service_description !== "") {
            var p6 = context.createElement("p");
            var s6 = context.createElement("strong");
            p6.textContent = drupalSettings.availableHealthServices[service.value].vba_regional_service_description.replace(/&nbsp;/g, " ");
            s6.textContent = "Regional service description: ";
            div.classList.remove("no-content");
            div.appendChild(p6);
            p6.prepend(s6);
          }
          if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].vba_facility_service_header !== "") {
            var p7 = context.createElement("p");
            var s7 = context.createElement("strong");
            p7.textContent = drupalSettings.availableHealthServices[service.value].vba_facility_service_header.replace(/&nbsp;/g, " ");
            s7.textContent = "Facility service header: ";
            div.classList.remove("no-content");
            div.appendChild(p7);
            p7.prepend(s7);
          }
          if (drupalSettings.availableHealthServices[service.value] !== undefined && drupalSettings.availableHealthServices[service.value].vba_facility_service_description !== "") {
            var p8 = context.createElement("p");
            var s8 = context.createElement("strong");
            p8.textContent = drupalSettings.availableHealthServices[service.value].vba_facility_service_description.replace(/&nbsp;/g, " ");
            s8.textContent = "Facility service description: ";
            div.classList.remove("no-content");
            div.appendChild(p8);
            p8.prepend(s8);
          }
        }
        service.after(div);
        if (div.textContent.length > 0) {
          var p = context.createElement("p");
          var d = context.createElement("div");
          p.id = service.id + "-services-general-description";
          p.className = "services-general-description";
          p.textContent = "General service description";
          d.className = "description ief-service-type";
          d.textContent = drupalSettings.availableHealthServices[service.value].vc_vocabulary_description_help_text;
          p.appendChild(d);
          service.after(p);
        }
      };
      var descriptionFill = function descriptionFill(ss) {
        if (ss && ss.length > 0) {
          ss.forEach(function (service) {
            loadItems(service);
            service.addEventListener("change", function () {
              loadItems(service);
            });
          });
        }
      };
      $(context).ajaxComplete(function () {
        descriptionFill(context.querySelectorAll(".field--name-field-service-name-and-descripti select"));
      });
      window.addEventListener("DOMContentLoaded", function () {
        descriptionFill(context.querySelectorAll(".field--name-field-service-name-and-descripti select"));
        var systemSelect = context.querySelector("#edit-field-region-page");
        if (systemSelect !== null) {
          systemSelect.addEventListener("change", function () {
            descriptionFill(context.querySelectorAll(".field--name-field-service-name-and-descripti select"));
          });
        }
      });
    }
  };
})(jQuery, Drupal);