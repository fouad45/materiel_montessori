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
jQuery(function ($) {
    $("#change_logo_btn").on('click', function () {
        $(this).hide();
        $("#upload_logo").show();
    });
    $(".confirm-box").change(function () {
        if ($('.confirm-box:checked').length == $('.confirm-box').length) {
            $('#create-account').removeAttr('disabled');
        } else {
            $('#create-account').prop('disabled', true);
        }
    });
});
jQuery(function ($) {
    $('#confirmation-form input:required').on('change', function () {
        let requiredCheckboxes = $('#confirmation-form input:required');
        let checkedCheckboxes = requiredCheckboxes.filter(':checked');
        if (requiredCheckboxes.length === checkedCheckboxes.length) {
            $('#next-step').removeAttr('disabled');
        } else {
            $('#next-step').prop('disabled', true);
        }
    });
});
(function () {
    'use strict';
})();
/**
 * Once a page is loaded, the new "SetupWizard" object is created.
 * The events for "SetupWizard" object elements are initialized.
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

function validate(form) {
    var c = [],
        btn = [],
        r,
        invalidInput = false;
    for (var i = 0; i < form.elements.length; i++) {
        if (form.elements[i].name === '') {
            continue;
        }
        form.elements[i].parentNode.parentNode.classList.remove('has-error');
        switch (form.elements[i].nodeName) {
            case 'INPUT':
                switch (form.elements[i].type) {
                    case 'text':
                        if (!form.elements[i].value || form.elements[i].value == 'undefined') {
                            invalidInput = true;
                        }
                        break;
                    case 'email':
                        r = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        if (!r.test(form.elements[i].value)) {
                            invalidInput = true;
                        }
                        break;
                }
                break;
        }
        if (invalidInput) {
            form.elements[i].parentNode.parentNode.classList.add('has-error');
            break;
        } else {
            invalidInput = false;
        }
    }
    if (!invalidInput) {
        return true;
    }
}

jQuery(function ($) {
    var setupwizard = new SetupWizard({
        loader: '#loadScreen',
        nextStep: '#next-step',
        createAccount: '#create-account',
        signIn: '#touchize-sign-in',
        isReturningUser: false,
        logoFileInput: '#logo-file-input',
        logoNameInput: '#logo-name-input',
        logoPreviewImage: '#preview-img-logo',
    });
    // To edit a logo changing the value of file input.
    setupwizard.logoFileInput.on('change', function (event) {
        var files = event.target.files;
        if (files.length) {
            setupwizard.updateLogo(files[0]);
        }
    });
    setupwizard.nextStep.on('click', function (event) {
        event.preventDefault();
        var current_step = $(this).data('step');
        setupwizard.processStep(current_step);
    });
    setupwizard.createAccount.on('click', function (event) {
        var form = $('#confirmation-form');
        event.preventDefault();
        if (validate(form[0])) {
            var current_step = $(this).data('step');
            setupwizard.isReturningUser = false;
            setupwizard.processStep(current_step);
        }
    });
    setupwizard.signIn.on('click', function (event) {
        var form = $('#signin-form');
        event.preventDefault();
        if (validate(form[0])) {
            var current_step = $(this).data('step');
            setupwizard.isReturningUser = true;
            setupwizard.processStep(current_step);
        }
    });
    $(".colorpicker-element").each(function () {
        var id = $(this).attr('id').replace(/picker_/g, '');
        var colorPreview = $('#picker_preview_' + id);
        $(this).colorpicker();
        $(this).on('changeColor', function (event) {
            if (event.value) {
                colorPreview.css('background-color', event.value);
            }
        });
        colorPreview.click(function (event) {
            $(".colorpicker-element").focus();
        });
    });
});
/**
 * SetupWizard class.
 *
 * 'SetupWizard' object is used to save and send data to the server.
 * 'SetupWizard' object includes methods to edit template, logo, and styling variables.
 */
var SetupWizard = (function () {
    /**
     * To set the values for the object attributes.
     *
     * @param {Object} args
     */
    function SetupWizard() {
        var args = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
        _classCallCheck(this, SetupWizard);
        this.loader = $(args.loader) || null;
        this.nextStep = $(args.nextStep) || null;
        this.createAccount = $(args.createAccount) || null;
        this.signIn = $(args.signIn) || null;
        this.isReturningUser = args.isReturningUser || false;
        this.logoFileInput = $(args.logoFileInput) || null;
        this.logoNameInput = $(args.logoNameInput) || null;
        this.logoPreviewImage = $(args.logoPreviewImage) || null;
    }

    /**
     * Preparing and sending data to the server to edit a template.
     *
     * @param  {String} templateName
     *
     * @return {Boolean}
     */
    _createClass(SetupWizard, [
        /**
         * Handling current step data. Redirecting to next step
         *
         * @param  {Object} file
         *
         * @return {Boolean}
         */
        {
            key: 'processStep',
            value: function processStep(step) {
                const user_create_url = 'https://themecreator.touchize.com/?rest_route=/tzcb/v1/create_user';
                const user_login_url = 'https://themecreator.touchize.com/?rest_route=/tzcb/v1/login';
                const client_create_url = 'https://themecreator.touchize.com/?rest_route=/tzcb/v1/create_client';
                const ajax_url = location.href.replace(/#/g, '') + '&ajax';
                const settings = {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: ''
                };
                var data = {
                    action: 'processStep',
                    step: step
                };
                switch (step) {
                    case 0:
                        if (this.isReturningUser) {
                            let username = $('#touchize_ps_signin_shop_email').val();
                            let password = $('#touchize_ps_sigin_password').val();
                            settings.body = JSON.stringify({
                                username: username,
                                password: password
                            });
                            fetch(user_login_url, settings)
                                .then(r => r.json())
                                .then(r => {
                                    console.log(r);
                                    if (!r.success) {
                                        throw r.errors.join(', ');
                                    }
                                })
                                .then(() => {
                                    this.sendRequest(ajax_url, 'POST', data, 'json')
                                        .then(function (data) {
                                            if (data && data.url) {
                                                location.href = data.url;
                                            } else {
                                                location.reload();
                                            }
                                        });
                                })
                                .catch(e => {
                                    console.log(e);
                                    let notif = $('.tz_sign_in_notification');
                                    notif.html(e);
                                    notif.show();
                                });
                        } else {
                            let user_settings = {
                                email: $('#touchize_ps_shop_email').val(),
                                password: $('#touchize_ps_password').val(),
                                first_name: $('#touchize_ps_first_name').val(),
                                last_name: $('#touchize_ps_last_name').val(),
                                domain: $('#domain').val(),
                                tzcb_user_preview_url: $('#tzcb_user_preview_url').val()
                            };
                            settings.body = JSON.stringify(user_settings);

                            fetch(user_create_url, settings)
                                .then(resp => resp.json())
                                .then(resp => {
                                    console.log(resp);
                                    if (!resp.success) {
                                        throw resp.errors.join(', ');
                                    }
                                })
                                .then(() => {
                                    this.sendRequest(ajax_url, 'POST', data, 'json')
                                        .then(function (data) {
                                            if (data && data.url) {
                                                location.href = data.url;
                                            } else {
                                                location.reload();
                                            }
                                        });
                                })
                                .catch(e => {
                                    console.log(e);
                                    let notif = $('.tz_sign_up_notification');
                                    notif.html(e);
                                    notif.show();
                                });
                        }
                        return;
                    case 1:
                        break;
                    case 2:
                        data.touchize_cols = $("input[name='touchize_cols']:checked").val();
                        data.main_color = $("input[name='main_color']").val();
                        break;
                    case 3:
                        var menu_preselection = [];
                        $("input[name='menu_item']:checked").each(function () {
                            if (this.checked) {
                                menu_preselection.push(this.value);
                            }
                        });
                        data.menu_preselection = menu_preselection;
                        break;
                    case 4:
                        var category_preselection = [];
                        $("input[name='categoryBox[]']:checked").each(function () {
                            if (this.checked) {
                                category_preselection.push(this.value);
                            }
                        });
                        data.category_preselection = category_preselection;
                        break;
                    case 5:
                        data.landingpage_preselection = $("input[name='landingpage_item']:checked").val();
                        break;
                    case 6:
                        data.banner_preselection = $("input[name='banner_item']:checked").val();
                        break;
                    case 7:
                        this.loader.show();
                        data.touchize_ps_shop_email = $("input[name='touchize_ps_shop_email']").val();
                        data.touchize_ps_shop_name = $("input[name='touchize_ps_shop_name']").val();
                        data.touchize_domain_name = $("input[name='touchize_domain_name']").val();

                    $.ajax({
                    url: tz_start_trial_url,
                            data: {
                        is_ajax: true,
                        is_setup_wizard: true,
                    id: "start-trial"
                    },
                    type: "POST",
                        dataType: 'json'

                            });

                        settings.body = tz_client_settings;
                        fetch(client_create_url, settings)
                            .then(resp => resp.json())
                            .then(resp => {
                                if (!resp.success) {
                                    throw resp.errors.join(', ');
                                }
                                data.touchize_cdn_code = resp.client_url;
                            })
                            .catch(err => {
                                console.log(err);
                                data.error = true;
                                data.response = err;
                            })
                            .then(() => {
                                this.sendRequest(ajax_url, 'POST', data, 'json')
                                    .then(function (data) {
                                        if (data && data.url) {
                                            location.href = data.url;
                                        } else {
                                            location.reload();
                                        }
                                    });
                            });
                        return;
                }

                var request = this.sendRequest(location.href.replace(/#/g, '') + '&ajax', 'POST', data, 'json');
                request.then(function (data) {
                    if (data && data.url) {
                        location.href = data.url;
                    } else {
                        location.reload();
                    }
                });
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
                var request = this.sendRequest(location.href.replace(/#/g, '') + '&ajax', 'POST', formData, null, true);
                request.then(this.updateLogoResolve.bind(this), this.updateLogoReject.bind(this)).then(this.logoPreviewImage.attr('src', URL.createObjectURL(file)));
                this.loader.hide();
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
             * Preparing and sending data to the server.
             *
             * @return {Boolean}
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
    return SetupWizard;
})();
