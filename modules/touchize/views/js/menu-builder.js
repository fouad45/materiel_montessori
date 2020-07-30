/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */

(function () {
  'use strict';
})();

var _createClass = (function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];

      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;

      if ('value' in descriptor) {
        descriptor.writable = true;
      }

      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) {
      defineProperties(Constructor.prototype, protoProps);
    }

    if (staticProps) {
      defineProperties(Constructor, staticProps);
    }

    return Constructor;
  };
})();

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError('Cannot call a class as a function');
  }
}

/**
 * The includes() method determines whether an array\string
 * includes a certain element\char, returning true or false as appropriate.
 * TODO:
 * 1. Apply coding standards to polyfills.
 */

if (!String.prototype.includes) {
  String.prototype.includes = function () {
    return String.prototype.indexOf.apply(this, arguments) !== -1;
  };
}

if (!Array.includes) {
  Array.prototype.includes = function (searchElement /*, fromIndex*/) {
    var O = Object(this);
    var len = parseInt(O.length) || 0;
    if (len === 0) {
      return false;
    }

    var n = parseInt(arguments[1]) || 0;
    var k;
    if (n >= 0) {
      k = n;
    } else {
      k = len + n;
      if (k < 0) {
        k = 0;
      }
    }

    while (k < len) {
      var currentElement = O[k];
      if (searchElement === currentElement ||
        searchElement !== searchElement &&
        currentElement !== currentElement
      ) {
        return true;
      }

      k++;
    }

    return false;
  };
}

/**
 * Front-end logic of menu builder.
 * New object 'Menu' is created once page loading is completed.
 * The 'scenario' property is set for this object.
 * This property identifies the logic to create new menu element and
 * edit a current menu element.
 * The events for 'Menu' object properties are initialized.
 */

$(document).ready(function () {
  var menu = new Menu({
    form: '#touchize_main_menu_form',
    type: '#type',
    action: '#action',
    title: '#title',
    page: '#page',
    cmsPage: '#cms_page',
    url: '#url',
    externalOn: '#active_on',
    externalOff: '#active_off',
    event: '#event',
    eventInput: '#event_input',
    pageUrl: '#page_url',
    scenario: 'create',
  });

  menu.setScenario();

  menu.setType(menu.type.val());
  if (menu.type.val() == 'menu-item') {
    menu.setAction(menu.action.val());
  }

  menu.type.on('change', function (event) {
    menu.setType(event.target.value);

    if (menu.scenario == 'update' && event.target.value == 'menu-item') {
      menu.setAction(menu.action.val());
    }
  });

  menu.action.on('change', function (event) {
    menu.setAction(event.target.value);
  });
});

/**
 * Menu class.
 */

var Menu = (function () {

  /**
   * To set the value for 'Menu' object properties during its initialization.
   *
   * @param {Object} args
   */

  function Menu() {
    var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    _classCallCheck(this, Menu);

    this.form = $(args.form) || null;
    this.type = $(args.type) || null;
    this.action = $(args.action) || null;
    this.title = $(args.title) || null;
    this.page = $(args.page) || null;
    this.cmsPage = $(args.cmsPage) || null;
    this.url = $(args.url) || null;
    this.activeOn = $(args.externalOn) || null;
    this.activeOff = $(args.externalOff) || null;
    this.event = $(args.event) || null;
    this.eventInput = $(args.eventInput) || null;
    this.pageUrl = $(args.pageUrl) || null;
    this.scenario = 'create';
  };

  /**
   * To identify the scenario for 'Menu' object.
   *
   * This scenario may have the following values
   * 1. To create
   * 2. To edit
   *
   * Once a new element is created in the menu, all fields in the form are empty.
   * The values of this form fields are updated while changing 'type' and 'action' fields.
   * While editing a current element, all values of the form fields are filled with information from the database.
   *
   * @return {Boolean}
   */

  _createClass(Menu, [{
    key: 'setScenario',
    value: function setScenario() {
      var urlVariables = window.location.search.substring(1).split('&');
      var parameterName = void 0;
      var i = void 0;

      for (i = 0; i < urlVariables.length; i++) {
        parameterName = urlVariables[i].split('=');
        if (parameterName[0] === 'addtouchize_main_menu') {
          this.scenario = 'create';
          return true;
        } else if (parameterName[0] === 'updatetouchize_main_menu') {
          this.scenario = 'update';
          return true;
        }
      }

      return false;
    },

    /**
     * To show necessary fields for each 'type' in the form:
     * 1. menu-item;
     * 2. menu-header;
     * 3. menu-divider.
     *
     * The standard 'type' is a 'menu-item' while rendering the form.
     *
     * @param {String} type
     *
     * @return {Boolean}
     */

  }, {
    key: 'setType',
    value: function setType() {
      var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

      var result = true;

      switch (type) {
        case 'menu-item': {
          result = this.showFields('type_item');
          break;
        }

        case 'menu-header': {
          result = this.showFields('type_header');
          break;
        }

        case 'menu-divider': {
          result = this.showFields('type_divider');
          break;
        }

        default: {
          result = this.showFields('type_item');
        }

      }

      return result;
    },

    /**
     * To show necessary fields for each 'action' in the form:
     * 1. page;
     * 2. cms_page;
     * 3. url;
     * 4. event.
     *
     * @param {String} action
     *
     * @return {Boolean}
     */

  }, {
    key: 'setAction',
    value: function setAction() {
      var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

      var result = true;

      switch (action) {
        case 'page': {
          result = this.showFields('action_page');
          break;
        }

        case 'cms_page': {
          result = this.showFields('action_cms_page');
          break;
        }

        case 'url': {
          result = this.showFields('action_url');
          break;
        }

        case 'event': {
          result = this.showFields('action_event');
          break;
        }

        default: {
          result = this.showFields('action_page');
        }

      }

      return result;
    },

    /**
     * To show the fields in the form in accordance to the received scenario.
     *
     * @param  {String} scenario
     *
     * @return {Boolean}
     */

  }, {
    key: 'showFields',
    value: function showFields() {
      var scenario = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

      var _this = this;

      var fieldList = [];

      switch (scenario) {
        case 'type_item': {
          fieldList = ['type', 'action', 'title', 'page'];
          break;
        }

        case 'type_header': {
          fieldList = ['type', 'title'];
          break;
        }

        case 'type_divider': {
          fieldList = ['type'];
          break;
        }

        case 'action_page': {
          fieldList = ['type', 'action', 'title', 'page'];
          break;
        }

        case 'action_cms_page': {
          fieldList = ['type', 'action', 'title', 'cms_page'];
          break;
        }

        case 'action_url': {
          fieldList = ['type', 'action', 'title', 'url', 'active_on', 'active_off'];
          break;
        }

        case 'action_event': {
          fieldList = ['type', 'action', 'title', 'event', 'event_input', 'page_url'];
          break;
        }

        default: {
          fieldList = ['type', 'action', 'title', 'page'];
          break;
        }

      }

      $.each(this.form.find('input, select'), function (key, domElement) {
        // Get `form-group` block.
        if (domElement.id.includes('title_')) {
          var wrap = $(domElement).closest('.form-group').parentsUntil('.form-wrapper');
        } else {
          var wrap = $(domElement).closest('.form-group');
        }

        // Set `pass` as a default to 'false'.
        var pass = false;

        // Set `pass` to 'true' if current element has one of these types:
        // 1. text;
        // 2. radio;
        // 3.select-one.
        switch (domElement.type) {
          case 'text': {
            pass = true;
            break;
          }

          case 'radio': {
            pass = true;
            break;
          }

          case 'select-one': {
            pass = true;
            break;
          }

        }

        if (pass) {
          // If fieldsList includes curren element
          if (!fieldList.includes(domElement.id)) {

            // Set default values for dropdown lists
            // or empty value for text fields
            // if menu scenario is equal to `create`
            if (domElement.nodeName.toLowerCase() == 'input' && domElement.type == 'text' && _this.scenario == 'create') {
              $(domElement).val('');
            } else if (domElement.nodeName.toLowerCase() == 'select' && _this.scenario == 'create') {
              $(domElement).val(domElement.options[0].value);
            }

            if (wrap.length) {

              // filter for title fields with multilanguage
              if (domElement.id.includes('title_') && scenario != 'type_divider') {
                wrap.show();
              } else {
                wrap.hide();
              }
            }
          } else {
            if (wrap.length) {
              wrap.show();
            }
          }
        }
      });

      return true;
    },
  },
  ]);

  return Menu;
})();
