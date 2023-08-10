/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function (Drupal) {
  function isValidSwap() {
    return true;
  }
  function onDrop() {
    var dragObject = this;
    var rowElement = dragObject.rowObject.element;
    rowElement.classList.add("error");
    var newElement = document.createElement("div");
    newElement.textContent = "Max Depth Exceeded! 😡";
    newElement.classList.add("form-item__error-message");
    rowElement.insertAdjacentElement("afterend", newElement);
    return null;
  }
  Drupal.behaviors.vaGovMagicheadMagichead = {
    attach: function attach() {
      if (typeof Drupal.tableDrag === "undefined") {
        return;
      }
      var tables = document.querySelectorAll("div.field--type-magichead table.field-multiple-table.draggable-table") || [];
      tables.forEach(function (element) {
        var id = element.getAttribute("id");
        if (Drupal.tableDrag[id]) {
          var tableDrag = Drupal.tableDrag[id];
          tableDrag.row.prototype.isValidSwap = isValidSwap;
          tableDrag.onDrop = onDrop;
        }
      });
    }
  };
})(Drupal);