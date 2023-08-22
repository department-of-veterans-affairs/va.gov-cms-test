/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal, once) {
  Drupal.behaviors.vaGovMediaLibraryReusableSaveAndSelect = {
    attach: function attach() {
      $(document).ajaxComplete(function () {
        $("input.field_media_in_library[type=checkbox]:not(:checked)").each(function () {
          $(".ui-dialog-buttonpane button").first().hide();
        });
        $(once("vaGovMediaLibraryReusableSaveAndSelect", "input.field_media_in_library", context)).change(function (object) {
          if (object.checked) {
            $(".ui-dialog-buttonpane button").first().show();
          } else {
            $(".ui-dialog-buttonpane button").first().hide();
          }
        });
      });
    }
  };
})(jQuery, window.Drupal, window.once);