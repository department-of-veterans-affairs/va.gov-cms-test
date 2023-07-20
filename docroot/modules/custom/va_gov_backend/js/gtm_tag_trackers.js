/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/
(function ($, Drupal, once, window) {
  Drupal.behaviors.vaGovTagTracker = {
    attach: function attach(context, settings) {
      function titleResolver(data) {
        var title = null;
        if (data) {
          title = data;
          if (data["#markup"]) {
            title = data["#markup"];
          }
        }
        return title;
      }
      var dataCollection = {
        pagePath: settings.gtm_data.pagePath ? settings.gtm_data.pagePath : null,
        pageTitle: titleResolver(settings.gtm_data.pageTitle),
        nodeID: settings.gtm_data.nodeID ? settings.gtm_data.nodeID : null,
        contentTitle: settings.gtm_data.contentTitle ? settings.gtm_data.contentTitle : null,
        contentType: settings.gtm_data.contentType ? settings.gtm_data.contentType : null,
        contentOwner: settings.gtm_data.contentOwner ? settings.gtm_data.contentOwner : null,
        userRoles: settings.gtm_data.userRoles ? settings.gtm_data.userRoles : null,
        userId: settings.gtm_data.userId ? settings.gtm_data.userId : null,
        userSections: settings.gtm_data.userSections ? settings.gtm_data.userSections : null
      };
      function menuTraverser(item) {
        var parentClasses = item.parentNode.className;
        var level4 = null;
        var level3 = null;
        var level2 = null;
        var level1 = null;
        if (parentClasses.includes("menu-level-3")) {
          level4 = item;
          level3 = item.closest("li.menu-item--expanded.menu-level-2").firstElementChild;
          level2 = level3.closest("li.menu-item--expanded.menu-level-1").firstElementChild;
          level1 = level2.closest("li.menu-item--expanded.menu-level-0").firstElementChild;
        }
        if (parentClasses.includes("menu-level-2")) {
          level3 = item;
          level2 = item.closest("li.menu-item--expanded.menu-level-1").firstElementChild;
          level1 = level2.closest("li.menu-item--expanded.menu-level-0").firstElementChild;
        }
        if (parentClasses.includes("menu-level-1")) {
          level2 = item;
          level1 = item.closest("li.menu-item--expanded.menu-level-0").firstElementChild;
        }
        if (parentClasses.includes("menu-level-0")) {
          level1 = item;
        }
        dataCollection.firstSectionLevel = level1 ? level1.textContent : "";
        dataCollection.secondSectionLevel = level2 ? level2.textContent : "";
        dataCollection.thirdSectionLevel = level3 ? level3.textContent : "";
        dataCollection.fourthSectionLevel = level4 ? level4.textContent : "";
      }
      function pushGTM(selector, event, subtype) {
        var editPageTypes = ["content-page", "bulk-content-page"];
        selector.forEach(function (el) {
          $(once("pushGTM", el, context)).click(function () {
            dataCollection.event = event;
            if (editPageTypes.includes(subtype)) {
              dataCollection.contentTitle = $(el).parent().siblings(".views-field-title").text().trim();
              dataCollection.contentType = $(el).parent().siblings(".views-field-type").text().trim();
              dataCollection.contentOwner = $(el).parent().siblings(".views-field-field-administration").text().trim();
            }
            menuTraverser(el);
            dataCollection["gtm.uniqueEventId"] = "";
            window.dataLayer.push(dataCollection);
          });
        });
      }
      var targets = [{
        selector: document.querySelectorAll("ul.toolbar-menu.top-level-nav > li > a"),
        event: "top-level-nav",
        subtype: false
      }, {
        selector: document.querySelectorAll("ul.toolbar-menu.lower-level-nav li a"),
        event: "lower-level-nav",
        subtype: false
      }, {
        selector: document.querySelectorAll('li.tabs__tab a[rel="edit-form"]'),
        event: "content-edit",
        subtype: "node-page"
      }, {
        selector: document.querySelectorAll(".views-field-edit-node a.button"),
        event: "content-edit",
        subtype: "content-page"
      }, {
        selector: document.querySelectorAll(".views-field-operations li.edit > a"),
        event: "content-edit",
        subtype: "bulk-content-page"
      }, {
        selector: document.querySelectorAll('#edit-actions.form-actions [value="Save"]'),
        event: "content-save",
        subtype: false
      }, {
        selector: document.querySelectorAll(".node-preview-button"),
        event: "content-preview",
        subtype: false
      }, {
        selector: document.querySelectorAll('#edit-actions.form-actions [value="Save and continue editing"]'),
        event: "content-save-and-continue",
        subtype: false
      }, {
        selector: document.querySelectorAll("#edit-actions.form-actions a.button:last-child"),
        event: "content-unlock",
        subtype: false
      }];
      targets.forEach(function (e) {
        pushGTM(e.selector, e.event, e.subtype);
      });
      function gtmPageLoadPush() {
        dataCollection.event = "pageLoad";
        window.dataLayer.push(dataCollection);
      }
      $(once("vaGovTagTracker", "body", context)).on("load", function () {
        gtmPageLoadPush();
      });
    }
  };
})(jQuery, Drupal, once, window);