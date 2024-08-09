/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal, once, Tippy) {
  Drupal.behaviors.vaGovToolbar = {
    attach: function attach(context) {
      var loadingText = "<div style='height: 20px; width: 40px;' class='claro-spinner'></div>";
      $(once("content-release-status-icon", "body", context)).each(function () {
        Tippy("#content-release-status-icon", {
          content: function content(reference) {
            reference.removeAttribute("title");
            return loadingText;
          },
          flipOnUpdate: true,
          onCreate: function onCreate(instance) {
            instance._isFetching = false;
            instance._src = null;
            instance._error = null;
          },
          onHidden: function onHidden(instance) {
            instance.setContent(loadingText);
          },
          onShow: function onShow(instance) {
            if (instance._isFetching || instance._src || instance._error) {
              return;
            }
            instance._isFetching = true;
            $.get("/admin/content/deploy/status", function (data) {
              instance.setContent(data);
            }).fail(function (jqXHR, textStatus, errorThrown) {
              instance.setContent("Request failed. " + errorThrown);
            }).always(function () {
              instance._isFetching = false;
            });
          },
          theme: "tippy_popover tippy_popover_center",
          allowHTML: true
        });
      });
    }
  };
})(jQuery, window.Drupal, window.once, window.tippy);
