/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal, once) {
  function refreshStatusBlock(url) {
    $.get(url, function (data) {
      $(".content-release-status-block").html(data);
    });
  }
  Drupal.behaviors.contentReleaseStatusBlock = {
    attach: function attach(context, settings) {
      $(once("contentReleaseStatusBlock", "body", context)).on("load", function () {
        window.setInterval(function () {
          refreshStatusBlock(settings.contentReleaseStatusBlock.blockRefreshPath);
        }, 10000);
      });
    }
  };
})(jQuery, window.Drupal, window.once);
