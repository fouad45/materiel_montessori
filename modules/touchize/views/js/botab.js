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

/**
 * Once a page is loaded, the new "Wizard" object is created.
 * The events for "Wizard" object elements are initialized.
 */

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

$(document).ready(function () {
  var wizard = new Wizard({
    wrapper: '#wizardWrap',
    loader: '#loadScreen',
    templateWrappers: '[data-template]',
    templateRadio: '[name="template"]',
    templateSave: '#template-save',
    stylingWrapper: '#styling-wrapper',
    styling: 'input[data-picker]',
    stylingSave: '#styling-save',
    stylingReset: '#styling-reset',
    logoFileInput: '#logo-file-input',
    logoNameInput: '#logo-name-input',
    logoPreviewImage: '#preview-img-logo',
    logoSave: '#save-logo',
    logoRemove: '#remove-logo',
    logoSync: '#logoSync',
    previewModal: '#previewModal',
    previewChangesModal: '#previewChangesModal',
    applyChanges: '#apply',
    demoModal: '#demoModal',
  });

  // To edit a template clicking on the preview image.
  wizard.templateImages.on('click', function (event) {
    wizard.updateTemplate(event.target.getAttribute('data-template'));
  });

  // To edit a template changing the value of radio button.
  wizard.templateRadio.on('change', function (event) {
    wizard.updateTemplate(event.target.value);
  });

  // To save styling variables
  wizard.templateSave.on('click', function (event) {
      wizard.saveTemplate();
  });

  // To edit a logo changing the value of file input.
  wizard.logoFileInput.on('change', function (event) {
    var files = event.target.files;

    if (files.length) {
      wizard.updateLogo(files[0]);
    }
  });

  // To save logo image clicking on the `save` button.
  wizard.logoSave.on('click', function (event) {
      wizard.saveLogo();
  });

  // To remove logo image clicking on the `remove` button.
  wizard.logoRemove.on('click', function (event) {
    wizard.removeLogo();
  });

  // To sync shop and module logos
  wizard.logoSync.on('click', function (event) {
    wizard.syncLogo();
  });

  // To check isColor value for each styling variable.
  // If isColor === true, we create colorpicker for the variable.
  $.each(wizard.styling, function (key, el) {
    var input = $(el);

    // set colorpicker
    if (input.attr('data-is-color') === 'true') {
      wizard.setColorPicker(input);
    }

    // update styling variable value by ajax
    input.on('blur', function (event) {
      var data = {
        id: event.target.id,
        value: event.target.value,
      };

      wizard.updateStylingVariable(data);
    });
  });

  // To save styling variables
  wizard.stylingSave.on('click', function (event) {
      wizard.saveStylingVariable();
  });

  // To restore styling variables
  wizard.stylingReset.on('click', function (event) {
      var input = wizard.templateRadio.filter(':checked');
      wizard.resetStylingVariable(input);
  });

  // To add iframe in the modal window when it is initiated.
  wizard.previewModal.on('show.bs.modal', function () {
    var iframe = document.createElement('iframe');
    iframe.src = botabPath;
    wizard.previewModal.find('.phone-template').empty().append(iframe);
  });

  // To add iframe in the modal window when it is initiated, changes only.
  wizard.previewChangesModal.on('show.bs.modal', function () {
    var iframe = document.createElement('iframe');
    iframe.src = previewPath;
    wizard.previewChangesModal.find('.phone-template').empty().append(iframe);
  });
  
    // To add iframe in the modal window when it is initiated, changes only.
  wizard.demoModal.on('show.bs.modal', function () {
    var iframe = document.createElement('iframe');
    iframe.src = demoPath;
    wizard.demoModal.find('.phone-template').empty().append(iframe);
  });

  // Apply changes
  wizard.applyChanges.on('click', function (event) {
    wizard.applyPreviewChanges();
  });
});

/**
 * Wizard class.
 *
 * 'Wizard' object is used to save and send data to the server.
 * 'Wizard' object includes methods to edit template, logo, and styling variables.
 */

var Wizard = (function () {
  /**
   * To set the values for the object attributes.
   *
   * @param {Object} args
   */
  function Wizard() {
    var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    _classCallCheck(this, Wizard);

    this.wrapper = $(args.wrapper) || null;
    this.loader = $(args.loader) || null;
    this.templateImages = $(args.templateWrappers) || null;
    this.templateRadio = $(args.templateRadio) || null;
    this.templateSave = $(args.templateSave) || null;
    this.stylingWrapper = $(args.stylingWrapper) || null;
    this.styling = $(args.styling) || null;
    this.stylingSave = $(args.stylingSave) || null;
    this.stylingReset = $(args.stylingReset) || null;
    this.logoFileInput = $(args.logoFileInput) || null;
    this.logoNameInput = $(args.logoNameInput) || null;
    this.logoPreviewImage = $(args.logoPreviewImage) || null;
    this.logoSave = $(args.logoSave) || null;
    this.logoRemove = $(args.logoRemove) || null;
    this.logoSync = $(args.logoSync) || null;
    this.previewModal = $(args.previewModal) || null;
    this.previewChangesModal = $(args.previewChangesModal) || null;
    this.applyChanges = $(args.applyChanges) || null;
    this.demoModal = $(args.demoModal) || null;
  }

  /**
   * Preparing and sending data to the server to edit a template.
   *
   * @param  {String} templateName
   *
   * @return {Boolean}
   */

  _createClass(Wizard, [{
    key: 'updateTemplate',
    value: function updateTemplate(templateName) {
      var data = {
        name: templateName,
        action: 'updateTemplate',
      };

      var request = this.sendRequest(
        location.href.replace(/#/g, '') + '&ajax',
        'POST',
        data,
        'json'
      );

      request.then(
        this.updateTemplateResolve.bind(this),
        this.updateTemplateReject.bind(this)
      );

      return true;
    },

    /**
     * `Resolve` for updateTemplate.
     *
     * @param  {Object} data
     * @param  {String} statusText
     * @param  {Object} jqXHR
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateTemplateResolve',
    value: function updateTemplateResolve(data, statusText, jqXHR) {
      var _this = this;

      if (!data.error) {
        var radio = $('input[type="radio"][value="' + data.template + '"]');

        if (radio.length) {
          radio.attr('checked', true);
        }

        _this.stylingWrapper.empty();

        if (data.styling.length) {
          $.each(data.styling, function (key, el) {
            _this.addStylingVariable(el);
          });
        } else {
          _this.addStylingAlert('There are no styling variables.');
        }
      }

      _this.loader.hide();

      return true;
    },

    /**
     * `Reject` for updateTemplate.
     *
     * @param  {Object} error
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateTemplateReject',
    value: function updateTemplateReject(error) {
      console.error(error);
      this.loader.hide();

      return true;
    },

    /**
     * Save template.
     *
     * @param  {Object} file
     *
     * @return {Boolean}
     */

  }, {
      key: 'saveTemplate',
      value: function saveTemplate() {
          var data = {
              action: 'saveTemplate'
          };

          var request = this.sendRequest(
              location.href.replace(/#/g, '') + '&ajax',
              'POST',
              data,
              'json'
          );

          request.then(
              this.saveTemplateResolve.bind(this),
              this.saveTemplateReject.bind(this)
          );

          return true;
      },

      /**
       * `Resolve` for saveTemplate.
       *
       * @return {Boolean}
       */

  }, {
      key: 'saveTemplateResolve',
      value: function saveTemplateResolve(data, statusText, jqXHR) {
          this.loader.hide();

          return true;
      },

      /**
       * `Reject` for saveTemplate.
       *
       * @param {Object} error
       *
       * @return {Boolean}
       */

  }, {
      key: 'saveTemplateReject',
      value: function saveTemplateReject(error) {
          console.error(error);
          this.loader.hide();
      },

      /**
       * Preparing and sending data to the server to upload a logo.
       *
       * @param  {Object} file
       *
       * @return {Boolean}
       */

  }, {
    key: 'updateLogo',
    value: function updateLogo(file) {

      this.logoNameInput.val(file.name);

      var formData = new FormData();

      formData.append('action', 'updateLogo');
      formData.append('logo', file);

      var request = this.sendRequest(
        location.href.replace(/#/g, '') + '&ajax',
        'POST',
        formData,
        null,
        true
      );

      request.then(
        this.updateLogoResolve.bind(this),
        this.updateLogoReject.bind(this)
      ).then(
        this.logoPreviewImage.attr('src', URL.createObjectURL(file))
      );

      return true;
    },

    /**
     * `Resolve` for updateLogo.
     *
     * @param  {Object} data
     * @param  {String} statusText
     * @param  {Object} jqXHR
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateLogoResolve',
    value: function updateLogoResolve(data, statusText, jqXHR) {
      this.logoSave.show();
      this.logoRemove.show();
      this.loader.hide();

      return true;
    },

    /**
     * `Reject` for updateLogo.
     *
     * @param  {Object} error
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateLogoReject',
    value: function updateLogoReject(error) {
      console.log(error);
      this.loader.hide();

      return true;
    },

    /**
     * Preparing and sending data to the server to save a logo.
     *
     * @return {Boolean}
     */

  }, {
      key: 'saveLogo',
      value: function saveLogo() {

          var data = {
              action: 'saveLogo',
          };

          var request = this.sendRequest(
              location.href.replace(/#/g, '') + '&ajax',
              'POST',
              data,
              'json'
          );

          request.then(
              this.saveLogoResolve.bind(this),
              this.saveLogoReject.bind(this)
          );

          return true;
      },

      /**
       * `Resolve` for saveLogo.
       *
       * @param  {Object} data
       * @param  {String} statusText
       * @param  {Object} jqXHR
       *
       * @return {Boolean}
       */

  }, {
      key: 'saveLogoResolve',
      value: function saveLogoResolve(data, statusText, jqXHR) {
          this.logoSave.show();
          this.logoRemove.show();
          this.loader.hide();

          return true;
      },

      /**
       * `Reject` for saveLogo.
       *
       * @param  {Object} error
       *
       * @return {Boolean}
       */

  }, {
      key: 'saveLogoReject',
      value: function saveLogoReject(error) {
          console.log(error);
          this.loader.hide();

          return true;
      },

      /**
       * Preparing and sending data to the server to remove a logo.
       *
       * @return {Boolean}
       */

  }, {
    key: 'removeLogo',
    value: function removeLogo() {
      var data = {
        action: 'removeLogo',
      };

      var request = this.sendRequest(
        location.href.replace(/#/g, '') + '&ajax',
        'POST',
        data,
        'json'
      );

      request.then(
        this.removeLogoResolve.bind(this),
        this.removeLogoReject.bind(this)
      );

      return true;
    },

    /**
     * `Resolve` for removeLogo.
     *
     * @param  {Object} data
     * @param  {String} statusText
     * @param  {Object} jqXHR
     *
     * @return {Boolean}
     */

  }, {
    key: 'removeLogoResolve',
    value: function removeLogoResolve(data, statusText, jqXHR) {
      var defaultLogoSrc = location.protocol + '//' + location.hostname + '/img/' + data.defaultLogo;

      this.logoNameInput.val('');
      this.logoFileInput.val('');
      this.logoPreviewImage.attr('src', defaultLogoSrc);
      this.logoSave.hide();
      this.logoRemove.hide();
      this.loader.hide();

      return true;
    },

    /**
     * `Reject` for removeLogo.
     *
     * @param  {Object} error
     *
     * @return {Boolean}
     */

  }, {
    key: 'removeLogoReject',
    value: function removeLogoReject(error) {
      console.error(error);
      this.loader.hide();

      return true;
    },

    /**
     * Sync shop logo.
     *
     * @return {Boolean}
     */

  }, {
    key: 'syncLogo',
    value: function syncLogo() {
      var data = {
        action: 'syncLogo',
      };

      var request = this.sendRequest(
        location.href.replace(/#/g, '') + '&ajax',
        'POST',
        data,
        'json'
      );

      request.then(
        this.syncLogoResolve.bind(this),
        this.syncLogoReject.bind(this)
      );

      return true;
    },

    /**
     * `Resolve` for syncLogo.
     *
     * @return {Boolean}
     */

  }, {
    key: 'syncLogoResolve',
    value: function syncLogoResolve(data, statusText, jqXHR) {
      if (!data.error) {
        this.logoSync.closest('.row').hide();
      }

      this.loader.hide();
    },

    /**
     * `Reject` for syncLogo.
     *
     * @param {Object} error
     *
     * @return {Boolean}
     */

  }, {
    key: 'syncLogoReject',
    value: function syncLogoReject(error) {
      console.error(error);
      this.loader.hide();
    },

    /**
     * Preparing and sending data to the server to edit styling variable.
     *
     * @param  {Object} variable
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateStylingVariable',
    value: function updateStylingVariable(variable) {
      var data = {
        id: variable.id.replace(/picker_/g, ''),
        value: variable.value,
        action: 'updateStylingVariable',
      };

      var request = this.sendRequest(
        location.href.replace(/#/g, '') + '&ajax',
        'POST',
        data,
        'json'
      );

      request.then(
        this.updateStylingVariableResolve.bind(this),
        this.updateStylingVariableReject.bind(this)
      );

      return true;
    },

    /**
     * `Resolve` for updateStylingVariable.
     *
     * @param  {Object} data
     * @param  {String} statusText
     * @param  {Object} jqXHR
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateStylingVariableResolve',
    value: function updateStylingVariableResolve(data, statusText, jqXHR) {
      this.loader.hide();

      return true;
    },

    /**
     * `Reject` for updateStylingVariable.
     *
     * @param  {Object} error
     *
     * @return {Boolean}
     */

  }, {
    key: 'updateStylingVariableReject',
    value: function updateStylingVariableReject(error) {
      console.error(error);
      this.loader.hide();

      return true;
    },

    /**
     * Save styling variables.
     *
     * @return {Boolean}
     */

  }, {
      key: 'saveStylingVariable',
      value: function saveStylingVariable() {
          var data = {
              action: 'saveStylingVariable'
          };

          var request = this.sendRequest(
              location.href.replace(/#/g, '') + '&ajax',
              'POST',
              data,
              'json'
          );

          request.then(
              this.saveStylingVariableResolve.bind(this),
              this.saveStylingVariableReject.bind(this)
          );

          return true;
      },

      /**
       * `Resolve` for saveStylingVariable.
       *
       * @return {Boolean}
       */

  }, {
      key: 'saveStylingVariableResolve',
      value: function saveStylingVariableResolve(data, statusText, jqXHR) {
          this.loader.hide();

          return true;
      },

      /**
       * `Reject` for saveStylingVariable.
       *
       * @param {Object} error
       *
       * @return {Boolean}
       */

  }, {
      key: 'saveStylingVariableReject',
      value: function saveStylingVariableReject(error) {
          console.error(error);
          this.loader.hide();
      },

      /**
       * Reset to defaults for styling variables.
       *
       * @return {Boolean}
       */

  }, {
      key: 'resetStylingVariable',
      value: function resetStylingVariable(template) {
          var data = {
              action: 'resetStylingVariable',
              template: template.val()
          };

          var request = this.sendRequest(
              location.href.replace(/#/g, '') + '&ajax',
              'POST',
              data,
              'json'
          );

          request.then(
              this.resetStylingVariableResolve.bind(this),
              this.resetStylingVariableReject.bind(this)
          );

          return true;
      },

      /**
       * `Resolve` for resetStylingVariable.
       *
       * @return {Boolean}
       */

  }, {
      key: 'resetStylingVariableResolve',
      value: function resetStylingVariableResolve(data, statusText, jqXHR) {
          if (!data.error) {
              wizzard = this;
              $.each(data.settings, function () {
                  wizzard.styling.filter('#picker_' + this.id_variable).val(this.value);
                  $('#picker_preview_' + this.id_variable).css('background-color', this.value);
              });
          }

          this.loader.hide();
      },

      /**
       * `Reject` for resetStylingVariable.
       *
       * @param {Object} error
       *
       * @return {Boolean}
       */

  }, {
      key: 'resetStylingVariableReject',
      value: function resetStylingVariableReject(error) {
          console.error(error);
          this.loader.hide();
      },

      /**
       * Applying of all changes.
       *
       * @return {Boolean}
       */

  }, {
    key: 'applyPreviewChanges',
    value: function applyPreviewChanges() {
      var data = {
        action: 'applyChanges',
      };

      var request = this.sendRequest(
        location.href.replace(/#/g, '') + '&ajax',
        'POST',
        data,
        'json'
      );

      request.then(
        this.applyPreviewChangesResolve.bind(this),
        this.applyPreviewChangesReject.bind(this)
      );

      return true;
    },

    /**
     * `Resolve` for applyPreviewChanges.
     *
     * @param  {Object} data
     * @param  {String} statusText
     * @param  {Object} jqXHR
     *
     * @return {Boolean}
     */

  }, {
    key: 'applyPreviewChangesResolve',
    value: function applyPreviewChangesResolve(data, statusText, jqXHR) {
      this.loader.hide();

      return true;
    },

    /**
     * `Reject` for applyPreviewChanges.
     *
     * @param  {Object} error
     *
     * @return {Boolean}
     */

  }, {
    key: 'applyPreviewChangesReject',
    value: function applyPreviewChangesReject(error) {
      console.error(error);
      this.loader.hide();

      return true;
    },

    /**
     * To generate html code of the notification about the absence of
     * styling variables for the selected template and show it in
     * the styling block.
     *
     * @param {String} text
     *
     * @return {Boolean}
     */

  }, {
    key: 'addStylingAlert',
    value: function addStylingAlert(text) {
      var alert = document.createElement('div');
      alert.className = 'alert alert-info';
      alert.setAttribute('role', 'alert');
      alert.innerHTML = text;

      this.stylingWrapper.append(alert);

      return true;
    },

    /**
     * To generate html code for styling variable and represent it in styling block on the webpage.
     * If the variable is `isColor`, we will initialize colorPicker.
     * For now, we show only `isColor` variables.
     *
     * @param {Object} variable
     *
     * @return {Boolean}
     */

  }, {
    key: 'addStylingVariable',
    value: function addStylingVariable(variable) {
      var _this = this;

      var rowWrapper = document.createElement('div');
      rowWrapper.className = 'row';
      rowWrapper.style.marginBottom = '10px';

      var colWrapper = document.createElement('div');
      colWrapper.className = 'col-xs-12';

      var colForLabel = document.createElement('div');
      colForLabel.className = 'col-xs-6 text-right';

      var colForInput = document.createElement('div');
      colForInput.className = 'col-xs-6';

      var label = document.createElement('label');
      label.className = 'control-label';
      label.htmlFor = variable.id_variable;

      var spanForLabel = document.createElement('span');
      spanForLabel.className = 'label-tooltip';
      spanForLabel.setAttribute('data-toogle', 'tooltip');
      spanForLabel.setAttribute('data-original-title', variable.description);
      spanForLabel.setAttribute('title', '');
      spanForLabel.innerHTML = variable.name;

      var rowForColorPicker = document.createElement('div');
      rowForColorPicker.className = 'row';

      var colForColorPickerInput = document.createElement('div');
      colForColorPickerInput.className = 'col-xs-4';

      var input = document.createElement('input');
      input.className = 'form-control';
      input.id = 'picker_' + variable.id_variable;
      input.setAttribute('data-picker', 'true');
      input.setAttribute('data-is-color', 'true');
      input.type = 'text';
      input.value = variable.value;

      $(input).on('blur', function (event) {
        var data = {
          id: event.target.id,
          value: event.target.value,
        };

        _this.updateStylingVariable(data);
      });

      var colForColorPreview = document.createElement('div');
      colForColorPreview.className = 'col-xs-2';
      colForColorPreview.style.height = '31px';

      // Add only `color` variables.
      if (variable.is_color === '1') {
        var colorPreview = document.createElement('div');
        colorPreview.className = 'color-preview';
        colorPreview.id = 'picker_preview_' + variable.id_variable;
        colorPreview.style.backgroundColor = variable.value;

        colForColorPreview.appendChild(colorPreview);
        colForColorPickerInput.appendChild(input);
        rowForColorPicker.appendChild(colForColorPickerInput);
        rowForColorPicker.appendChild(colForColorPreview);
        colForInput.appendChild(rowForColorPicker);

        label.appendChild(spanForLabel);
        colForLabel.appendChild(label);
        colWrapper.appendChild(colForLabel);
        colWrapper.appendChild(colForInput);

        rowWrapper.appendChild(colWrapper);

        _this.stylingWrapper.append(rowWrapper);
        _this.setColorPicker($(input));
      }

      return true;
    },

    /**
     * To initialize colorPicker for the received field.
     *
     * @param {Object} input
     *
     * @return {Boolean}
     */

  }, {
    key: 'setColorPicker',
    value: function setColorPicker(input) {
      var id = input.attr('id').replace(/picker_/g, '');
      var colorPreview = $('#picker_preview_' + id);

      input.colorpicker();

      input.on('changeColor', function (event) {
        if (event.value) {
          colorPreview.css('background-color', event.value);
        }
      });

      colorPreview.click(function (event) {
        input.focus();
      });

      return true;
    },

    /**
     * It returns Promise the send the request to the server.
     *
     * @param  {String} url
     * @param  {String} type
     * @param  {Object} data
     * @param  {String} dataType
     * @param  {Boolean} file
     *
     * @return {Object}
     */

  }, {
    key: 'sendRequest',
    value: function sendRequest(url, type, data, dataType, file) {
      this.loader.show();

      var params = {
        type: type,
        url: url,
        data: data,
      };

      if (file) {
        params.cache = false;
        params.contentType = false;
        params.processData = false;
      } else {
        params.dataType = dataType;
      }

      return $.when($.ajax(params));
    },
  },
  ]);

  return Wizard;
})();
