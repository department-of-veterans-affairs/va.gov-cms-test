/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal) {
  Drupal.behaviors.vagovmodalAccessibility = {
    attach: function attach() {
      $(".ui-dialog.entity-browser-modal").attr("tabindex", "0");
      $(".ui-dialog-titlebar-close").focus();
    }
  };
})(jQuery, Drupal);