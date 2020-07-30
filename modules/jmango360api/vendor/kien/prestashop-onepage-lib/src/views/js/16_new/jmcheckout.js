$(document).on('change', 'select[name="currency_payment"]', function(event){
    event.stopImmediatePropagation();
});

var PersonalInformation = function(url) {
    var self = this;
    self.url = url.replace(/\/$/, '');
    self.form = $('#billing-address-customer-info');
    self.save = function() {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?ajax=true&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                },
                success: function(json) {
                    $('#invoice-token').val(json[1]['token']);
                    var billing = new Billing(self.url);
                    billing.save(true);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        $('#checkout-error-content').html(request.responseText);
                        $('#checkout-error-dialog').show();
                    } else if (request.readyState == 0) {
                        $('#checkout-error-content').html(networkErrorMsg);
                        $('#checkout-error-dialog').show();

                    } else {
                        $('#checkout-error-content').html(unknownErrorMsg);
                        $('#checkout-error-dialog').show();
                    }
                }
            });
        }

    };
};

var Billing = function(url, countries, addresses, customer) {
    var self = this;
    var address;
    self.countries = countries;
    self.addresses = addresses;
    self.customer = customer;
    self.url = url.replace(/\/$/, '');
    self.addressService = new AddressService(url);

    self.renderCheckbox = function(addresses, selected_address) {
        $('#invoice-address-list').prepend($('#invoice-address-'+selected_address));

        for (var i = 0; i < addresses.length; i++){
            if (selected_address === parseInt(addresses[i].id_address)) {
                var html = "<input type=\"radio\" checked name=\"selected-billing-address\" value=\""+addresses[i].id_address+"\">\n<span class=\"radio-check\"></span>\n";
            } else {
                var html = "<input type=\"radio\"  name=\"selected-billing-address\" value=\""+addresses[i].id_address+"\">\n<span class=\"radio-check\"></span>\n";
            }
            $("#billing-radio-"+addresses[i].id_address).append(html);
        }
    };

    self.edit = function(address, changeCountry, default_country_id, hide_address_list){
        self.form = $('#billing-address-form');
        if (!hide_address_list) {
            $("#parent-div").addClass("showAddressForm");
            $("body").addClass("showAddressForm");
        }
        $('input[name=billing\\[use_for_shipping\\]][value=\'1\']').prop('checked', true).change();
        if (address) {
            self.address = address;
            self.selectedCountry = address.id_country;
            // $('input[name=selected-billing-address][value='+address.id_address+']').prop('checked', true).change();
        } else {
            address = new Object();
            address.id_address = 0;
            self.selectedCountry = default_country_id;
        }
        $.ajax({
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&ReloadFormField=true'+'&step=billing&rand=' + new Date().getTime(),
            async: true,
            data :{ id_country : self.selectedCountry},
            cache: false,
            beforeSend: function() {

            },
            complete: function() {
            },
            success: function(json) {
                json = JSON.parse(json);
                if (changeCountry){
                    address=objectifyForm(self.form.serializeArray());
                }
                $('#billing-address-details').show();
                if (json.output) {
                    $('#billing-address').html(json.output);
                    $('#billing-state-list').html('');
                    self.onCountryChange(null, self.selectedCountry, address.id_state, false, hide_address_list);
                    $('#billing\\:address_id').val(address.id_address);
                }
                $('#invoice-id_country').change(function(){
                    address.id_country = $('#invoice-id_country').val();
                    self.edit(address, true);
                });
                if (address.id_address) {
                    Object.entries(address).forEach(function (field) {
                        if (field[1] !== ' '){
                            $('#invoice-' + field[0]).val(field[1]);
                        } else {
                            $('#invoice-' + field[0]).val("");
                        }
                        if (field[0] === 'invoice-display-state' || field[0] === 'delivery-display-state') {
                            $('#delivery-display-state').val(field[1]);
                        }
                        if (field[0] === 'id_gender') {
                            var titleSelect= "input[name='billing[gender_id]'][value="+field[1]+"]";
                            $(titleSelect).attr("checked", "checked");
                        }
                    });
                } else {
                    $('#invoice-firstname').val(self.customer.firstname);
                    $('#invoice-lastname').val(self.customer.lastname);
                    $('#invoice-company').val(self.customer.company);
                }
                if (changeCountry){
                    $('#billing-display-state').val("");
                }
                if (!window.location.pathname.includes('jmango360api') && !window.location.search.includes('jmango360api')) {
                    if (!hide_address_list) {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $("#billing-address-form").css("padding-bottom", padding);
                        $('#fullscreen-select-billing-country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-billing-state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            if (padding < 100) {
                                $("#billing-address-form").css("padding-bottom", padding);
                            }
                            $('#fullscreen-select-billing-country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-billing-state > .overlay-window').css("padding-bottom", padding);
                        };
                    } else {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $('#fullscreen-select-billing-country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-billing-state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            $('#fullscreen-select-billing-country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-billing-state > .overlay-window').css("padding-bottom", padding);
                        };
                    }
                }
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.save = function(isGuest, default_country_id, editAddressLabel){
        self.form = $('#billing-address-form');
        self.form2 = $('#billing-address-customer-info');
        if (self.form.isValid()) {
            self.formData = $(self.form).serializeArray();
            var formData = $(self.form).serialize();
            var address = objectifyForm(self.formData);
            var id_address = address.id_address;
            $('#same-as-billing-address').val(id_address);
            if(id_address){
                formData=formData+"&billing_address_id="+id_address;
                formData += "&billing[edit]=1"
            } else {
                formData += "&billing[edit]=0"
            }
            self.fullFormData = $('#billing-address-form').serializeArray();
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + "&ajax=true" + "&submitGuestAccount=true&step=billing" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                    // self.resetLoadWaiting(false);
                },
                success: function(json) {
                    console.log(json);
                    // self.nextStep(json);
                    //no error
                    if (!json.hasError){
                        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
                        var useForShipping = Number($('input[name=billing\\[use_for_shipping\\]]:checked').val());
                        var fullAddress = objectifyForm(self.fullFormData);
                        address.id_address = json.id_address_invoice;
                        fullAddress.id_address = json.id_address_invoice;
                        if (!useForShipping) {
                                if (!isGuest) {
                                    $('#billing-address-details').hide();
                                    $('#billing-address-form-list').show();
                                } else {
                                    var shipping = new Shipping(self.url, countries, addresses, customer);
                                    var is_guest_shipping_exist = $('#shipping-display-country').val();
                                    shipping.edit(null, false, default_country_id, true, is_guest_shipping_exist);
                                    $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
                                }
                                $('#step1').hide();
                                $('#step2').show();
                                var navbar = new NavBar(2);
                                navbar.setStep();
                            } else {
                                $('#step1').hide();
                                $('#step3').show();
                                if (!isGuest) {
                                    $('#billing-address-details').hide();
                                    $('#billing-address-form-list').show();
                                }  else {
                                    var shipping = new Shipping(self.url, countries, addresses, customer);
                                    shipping.edit(address);
                                    $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
                                }
                                // $('#selected-shipping-address-'+json.id_address_delivery).prop("checked", true);
                                var navbar = new NavBar(3);
                                navbar.setStep();
                            }
                        // }
                        if (json.updated_section) {
                            $('#step3').html(json.updated_section);
                        }
                        var billinghtml;
                        var shippinghtml;
                        if ($('#invoice-address-'+address.id_address).length > 0) {
                            billinghtml = buildBillingAddressHtml(address, address.id_address, true, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, address.id_address, true, fullAddress, editAddressLabel);
                            $('#invoice-address-'+address.id_address).html(billinghtml);
                            $('#delivery-address-'+address.id_address).html(shippinghtml);
                        } else {
                            billinghtml = buildBillingAddressHtml(address, address.id_address, false,fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, address.id_address, false, fullAddress, editAddressLabel);
                            $('#invoice-address-list').append(billinghtml);
                            $('#delivery-address-list').append(shippinghtml);
                        }
                        $('#billing-radio-'+address.id_address+' > input').prop("checked", true);
                        if (useForShipping) {
                            $('#shipping-radio-'+address.id_address+' > input').prop("checked", true);
                        }
                        var billingAddressDetailsScreen = $('#billing-address-details');
                        var shippingAddressDetailsScreen = $('#shipping-address-details');
                        if (!isGuest && !billingAddressDetailsScreen.hasClass('overlay-window')){
                            billingAddressDetailsScreen.addClass('overlay-window');
                        }
                        if (!isGuest && !shippingAddressDetailsScreen.hasClass('overlay-window')){
                            shippingAddressDetailsScreen.addClass('overlay-window');
                        }
                        var selectedShippingAddress = $('input[name=selected-shipping-address]:checked').val();
                        if (!selectedShippingAddress) {
                            $('#selected-shipping-address-'+address.id_address).prop("checked", true);
                        }
                        //Move selected address to top of address list
                        if (!isGuest) {
                            var selectedInvoiceAddress = $('#invoice-address-' + address.id_address);
                            $('#invoice-address-list').prepend(selectedInvoiceAddress);
                            if (useForShipping) {
                                var selectedDeliveryAddress = $('#delivery-address-' + address.id_address);
                                $('#delivery-address-list').prepend(selectedDeliveryAddress);
                            } else {
                                var shipping_id_address = $('input[name=selected-shipping-address]:checked').val();
                                var selectedDeliveryAddress = $('#delivery-address-'+shipping_id_address);
                                selectedDeliveryAddress.parent().prepend(selectedDeliveryAddress);
                            }
                        }
                        $('input[name=billing\\[use_for_shipping\\]][value=\'1\']').prop('checked', true).change();
                        window.scrollTo(0, 0);
                        $('#same-as-billing-address').val(address.id_address);
                    } else {
                        // handle error
                        var errorHtml="";
                        json.errors.forEach(function(error){
                            console.log(error);
                            errorHtml += error+"<br/>"
                        });
                        $('#checkout-error-content').html(errorHtml);
                        $('#checkout-error-dialog').show();
                    }
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }
    };
    self.formUpdater = new AddressFormUpdater('billing',addresses, countries, customer);

    //Country select change event handler
    self.onCountryChange = function(event, id_country, selected_id_state, changeCountry, isGuest) {
        if (id_country) {
            countryId = id_country;
        } else {
            countryId = $(event.target).val();
        }
        if (changeCountry) {
            if (!self.form) {
                self.form = $('#billing-address-form');
            }
            address=objectifyForm(self.form.serializeArray());
            var id_address = $('input[name=selected-billing-address]:checked').val();
            self.edit(address, changeCountry, id_country, id_address?0:1);
            if (isGuest) {
                $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
            }
        }
        var data = self.addressService.getCountries(function(countries) {
            self.formUpdater.updateState(countries);
            self.formUpdater.updateNeedDNI(countries);
            self.formUpdater.updateZipcode(countries);
            if (countries[countryId].states) {
                $('#billing-state-list').html('');
                $('#billing-display-state').val('');
                countries[countryId].states.forEach(function (state) {
                    if (selected_id_state === state.id_state){
                        $('#billing\\:state_id[name=billing\\[state_id\\]]').val(state.id_state).change();
                        $('#billing-display-state').val(state.name);
                        if (isGuest) {
                            var stateHtml = '<li class="optionSelected" onclick="$(\'body\').removeClass(\'showAddressForm\');$(\'#parent-div\').removeClass(\'showAddressForm\');$(\'#billing-address-details\').removeClass(\'showAddressForm\');  billing.setSelectedState(event); $(\'#billing\\\\:state_id[name=billing\\\\[state_id\\\\]]\').val({0}).change(); $(\'#billing-display-state\').val(\'{1}\'); $(\'#fullscreen-select-billing-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        } else {
                            var stateHtml = '<li class="optionSelected" onclick="$(\'#parent-div\').removeClass(\'showAddressForm\');$(\'#billing-address-details\').removeClass(\'showAddressForm\');  billing.setSelectedState(event); $(\'#billing\\\\:state_id[name=billing\\\\[state_id\\\\]]\').val({0}).change(); $(\'#billing-display-state\').val(\'{1}\'); $(\'#fullscreen-select-billing-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        }
                    } else {
                        if (isGuest) {
                            var stateHtml = '<li onclick="$(\'body\').removeClass(\'showAddressForm\');$(\'#parent-div\').removeClass(\'showAddressForm\');$(\'#billing-address-details\').removeClass(\'showAddressForm\'); billing.setSelectedState(event); $(\'#billing\\\\:state_id[name=billing\\\\[state_id\\\\]]\').val({0}).change(); $(\'#billing-display-state\').val(\'{1}\'); $(\'#fullscreen-select-billing-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        } else {
                            var stateHtml = '<li onclick="$(\'#parent-div\').removeClass(\'showAddressForm\');$(\'#billing-address-details\').removeClass(\'showAddressForm\'); billing.setSelectedState(event); $(\'#billing\\\\:state_id[name=billing\\\\[state_id\\\\]]\').val({0}).change(); $(\'#billing-display-state\').val(\'{1}\'); $(\'#fullscreen-select-billing-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        }
                    }
                    $('#billing-state-list').append(stateHtml).change();
                });
            }
        });

        return false;

    };

    self.closeAddressFrom = function() {
        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
        $('#billing-address-details').hide();
    };

    self.getEditingAddress = function() {
        return this.address;
    };

    self.submit = function () {
        var id_address = $('input[name=selected-billing-address]:checked').val();
        $('#same-as-billing-address').val(id_address);
        var formData = "billing_address_id="+id_address;
        formData += ("&id_address="+id_address);
        formData += "&saveAddress=invoice";
        formData += "&billing[use_for_shipping]=0";
        formData += "&billing[edit]=0";
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: self.url + "&ajax=true" + "&submitGuestAccount=true&step=billing" + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: formData,
            beforeSend: function() {

            },
            complete: function() {
                // self.resetLoadWaiting(false);
            },
            success: function(json) {
                if (!json.hasError){
                    window.scrollTo(0, 0);
                    $('#step1').hide();
                    $('#step2').show();
                    var navbar = new NavBar(2);
                    navbar.setStep();
                    // move selected address to top of the list;
                    var selectedInvoiceAddress = $('#invoice-address-'+id_address);
                    selectedInvoiceAddress.parent().prepend(selectedInvoiceAddress);
                    if (!$('#same-as-billing-address').is(':checked')) {
                        var shipping_id_address = $('input[name=selected-shipping-address]:checked').val();
                        var selectedDeliveryAddress = $('#delivery-address-'+shipping_id_address);
                        selectedDeliveryAddress.parent().prepend(selectedDeliveryAddress);
                    }
                }
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.closeEditBillingAddressForm = function(isLogged){
        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
        $('#checkout-navbar').show();
        if (isLogged) {
            $('#billing-address-details').hide();
        }
        $('#order-review-edit-header').hide();
        $('#step1').hide();
        $('#step5').show();
        var navbar = new NavBar(5);
        navbar.setStep();
    };

    function objectifyForm(formArray) {//serialize data function

        var returnArray = {};
        for (var i = 0; i < formArray.length; i++){
            var name = formArray[i]['name'].replace('billing[', '').replace(']', '');
            if (name.includes('_id')) {
                name = 'id_' + name.replace('_id', '');
            }
            returnArray[name] = formArray[i]['value']?formArray[i]['value']:"";
        }
        return returnArray;
    }

    self.setSelectedCountry = function() {
        $("#billing_country_list li").removeClass("optionSelected");
        var selectedCountry = Number($('#billing\\:country_id[name=billing\\[country_id\\]]').val());
        var id = "#billing_country_"+selectedCountry;
        $(id).addClass("optionSelected");
    };

    self.setSelectedState = function(event) {
        $("#billing-state-list li").removeClass("optionSelected");
        $(event.target).addClass("optionSelected");
    };
};

var Shipping = function(url,countries, addresses, customer) {
    var self = this;
    var address;
    self.countries = countries;
    self.addresses = addresses;
    self.customer = customer;
    self.url = url.replace(/\/$/, '');
    self.addressService = new AddressService(url);
    $('input:radio[name="selected-shipping-address"]').change(
        function(){
            if ($(this).is(':checked')) {
                $('#submit-shipping-address').removeClass("disabled no-arrow");
            }
        });

    self.renderCheckbox = function(addresses, selected_address) {
        for (var i = 0; i < addresses.length; i++){
            if (selected_address === parseInt(addresses[i].id_address)) {
                var html = "<input type=\"radio\" checked name=\"selected-shipping-address\" value=\""+addresses[i].id_address+"\"><span class=\"radio-check\"></span>";
            } else {
                var html = "<input type=\"radio\"  name=\"selected-shipping-address\" value=\""+addresses[i].id_address+"\"><span class=\"radio-check\"></span>";
            }
            $("#shipping-radio-"+addresses[i].id_address).append(html);
        }
    };

    self.edit = function(address, changeCountry, default_country_id, isGuest, is_guest_shipping_exist){
        $("#parent-div").addClass("showAddressForm");
        $("body").addClass("showAddressForm");

        if (address && address.id_address) {
            this.address = address;
            self.selectedCountry = address.id_country;
            self.form = $('#shipping-address-form');
        } else {
            address = new Object();
            address.id_address = 0;
            self.selectedCountry = default_country_id;
        }
        $.ajax({
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&ReloadFormField=true'+'&step=shipping&rand=' + new Date().getTime(),
            async: true,
            data :{ id_country : self.selectedCountry},
            cache: false,
            beforeSend: function() {

            },
            complete: function() {
            },
            success: function(json) {
                json = JSON.parse(json);
                if (changeCountry){
                    address=objectifyForm(self.form.serializeArray());
                }
                $('#shipping-address-details').show();
                if (json.output && !is_guest_shipping_exist) {
                    $('#shipping-address').html(json.output);
                    self.onCountryChange(null, self.selectedCountry, address.id_state, isGuest, false);
                    $('#shipping\\:address_id').val(address.id_address);
                }
                $('#delivery-id_country').change(function(){
                    address.id_country = $('#delivery-id_country').val();
                    self.edit(address, true);
                });
                if (address.id_address) {
                    Object.entries(address).forEach(function (field) {
                        if (field[1] !== ' '){
                            $('#delivery-' + field[0]).val(field[1]);
                        } else {
                            $('#delivery-' + field[0]).val("");
                        }
                        if (field[0] === 'invoice-display-state' || field[0] === 'delivery-display-state') {
                            $('#delivery-display-state').val(field[1]);
                        }
                    });
                } else if (!isGuest) {
                    $('#delivery-firstname').val(self.customer.firstname);
                    $('#delivery-lastname').val(self.customer.lastname);
                    $('#delivery-company').val(self.customer.company);
                }
                if (changeCountry){
                    $('#shipping-display-state').val("");
                }

                if (!window.location.pathname.includes('jmango360api') && !window.location.search.includes('jmango360api')) {
                    if (!isGuest) {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $("#shipping-address-form").css("padding-bottom", padding);
                        $('#fullscreen-select-shipping-country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-shipping-state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            if (padding < 100) {
                                $("#shipping-address-form").css("padding-bottom", padding);
                            }
                            $('#fullscreen-select-shipping-country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-shipping-state > .overlay-window').css("padding-bottom", padding);
                        };
                    } else {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $('#fullscreen-select-shipping-country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-shipping-state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            $('#fullscreen-select-shipping-country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-shipping-state > .overlay-window').css("padding-bottom", padding);
                        };
                    }
                }
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.save = function(isGuest, editAddressLabel){
        self.form = $('#shipping-address-form');
        if (self.form.isValid()) {
            var formData = $(self.form).serialize();
            formData = formData + "&shipping[edit]=1";
            if (isGuest) {
                formData=formData+"&shipping_address_id=0&shipping[same_as_billing]=0";
            } else {
                formData=formData+"&shipping_address_id="+$('#shipping\\:address_id').val();
            }

            self.formData = $(self.form).serializeArray();
            self.fullFormData = $('#shipping-address-form').serializeArray();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + "?ajax=true" + "&submitGuestAccount=true&step=shipping" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                    // self.resetLoadWaiting(false);
                },
                success: function(json) {
                    //no error
                    if (!json.hasError){
                        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");

                        if (!isGuest) {
                            $('#shipping-address-details').hide();
                        }
                        $('#step2').hide();
                        $('#step3').show();

                        var navbar = new NavBar(3);
                        navbar.setStep();
                        $('#step3').html(json.updated_section);
                        var address = objectifyForm(self.formData);
                        var fullAddress = objectifyForm(self.fullFormData);
                        address.id_address = json.id_address_delivery;
                        fullAddress.id_address = json.id_address_delivery;
                        var billinghtml;
                        var shippinghtml;
                        var selected_billing_address = $('input[name=selected-billing-address]:checked').val();

                        if ($('#invoice-address-'+address.id_address).length > 0) {
                            billinghtml = buildBillingAddressHtml(address, address.id_address, true, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, address.id_address, true, fullAddress, editAddressLabel);
                            $('#invoice-address-'+address.id_address).html(billinghtml);
                            $('#delivery-address-'+address.id_address).html(shippinghtml);
                        } else {
                            billinghtml = buildBillingAddressHtml(address, address.id_address, false, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, address.id_address, false, fullAddress, editAddressLabel);
                            $('#invoice-address-list').append(billinghtml);
                            $('#delivery-address-list').append(shippinghtml);
                        }
                        $('#shipping-radio-'+address.id_address+' > input').prop("checked", true);
                        if (parseInt(selected_billing_address) === parseInt(address.id_address)) {
                            $('#billing-radio-'+address.id_address+' > input').prop("checked", true);
                        }
                        var billingAddressDetailsScreen = $('#billing-address-details');
                        var shippingAddressDetailsScreen = $('#shipping-address-details');
                        if (!isGuest && !billingAddressDetailsScreen.hasClass('overlay-window')){
                            billingAddressDetailsScreen.addClass('overlay-window');
                        }
                        if (!isGuest && !shippingAddressDetailsScreen.hasClass('overlay-window')){
                            shippingAddressDetailsScreen.addClass('overlay-window');
                        }
                        if (!isGuest) {
                            var selectedDeliveryAddress = $('#delivery-address-' + address.id_address);
                            $('#delivery-address-list').prepend(selectedDeliveryAddress);
                        }
                        window.scrollTo(0, 0);
                    } else {
                        // handle error
                        var errorHtml="";
                        json.errors.forEach(function(error){
                            console.log(error);
                            errorHtml += error+"<br/>"
                        });
                        $('#checkout-error-content').html(errorHtml);
                        $('#checkout-error-dialog').show();
                    }
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }
    };

    self.closeAddressFrom = function() {
        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
        $('#shipping-address-details').hide();
    };

    self.getEditingAddress = function() {
        return this.address;
    };

    self.submit = function () {
        var id_address = $('input[name=selected-shipping-address]:checked').val();
        var formData = "shipping_address_id="+id_address;
        formData += ("&id_address="+id_address);
        formData += "&shipping[edit]=0";
        formData += "&saveAddress=delivery";
        formData += "&set_to_billing=0";
        formData += "&isUpdating=0";
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: self.url + "&ajax=true" + "&submitGuestAccount=true&step=shipping" + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: formData,
            beforeSend: function() {

            },
            complete: function() {
                // self.resetLoadWaiting(false);
            },
            success: function(json) {
                window.scrollTo(0, 0);
                $('#step3').show();
                $('#step2').hide();
                var navbar = new NavBar(3);
                navbar.setStep();
                var selectedDeliveryAddress = $('#delivery-address-'+id_address);
                selectedDeliveryAddress.parent().prepend(selectedDeliveryAddress);
                if (json.updated_section ) {
                    $('#step3').html(json.updated_section);
                }
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };
    self.back = function () {
        window.scrollTo(0, 0);
        $('#step1').show();
        $('#step2').hide();
        var navbar = new NavBar(1);
        navbar.setStep();
    };

    self.closeEditShippingAddressForm = function(isLogged){
        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
        if (isLogged){
            $('#shipping-address-details').hide();
        }
        $('#order-review-edit-header').hide();
        $('#checkout-navbar').show();
        $('#step2').hide();
        $('#step5').show();
        var navbar = new NavBar(5);
        navbar.setStep();
    };

    function objectifyForm(formArray) {//serialize data function

        var returnArray = {};
        for (var i = 0; i < formArray.length; i++){
            var name = formArray[i]['name'].replace('shipping[', '').replace(']', '');
            if (name.includes('_id')) {
                name = 'id_' + name.replace('_id', '');
            }
            returnArray[name] = formArray[i]['value']?formArray[i]['value']:"";
        }
        return returnArray;
    }


    self.formUpdater = new AddressFormUpdater('shipping',addresses, countries, customer);

    //Country select change event handler
    self.onCountryChange = function(event, id_country, selected_id_state, isGuest, changeCountry) {
        if (id_country) {
            countryId = id_country;
        } else {
            countryId = $(event.target).val();
        }
        if (changeCountry) {
            if (!self.form) {
                self.form = $('#shipping-address-form');
            }
            address=objectifyForm(self.form.serializeArray());
            self.edit(address, changeCountry, id_country, isGuest, false);
            if (isGuest) {
                $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");
            }
        }
        var data = self.addressService.getCountries(function(countries) {
            self.formUpdater.updateState(countries);
            self.formUpdater.updateNeedDNI(countries);
            self.formUpdater.updateZipcode(countries);
            if (countries[countryId].states) {
                $('#shipping-state-list').html('');
                $('#shipping-display-state').val('');
                countries[countryId].states.forEach(function (state) {
                    if (selected_id_state === state.id_state){
                        $('#shipping\\:state_id[name=shipping\\[state_id\\]]').val(state.id_state).change();
                        $('#shipping-display-state').val(state.name);
                        if (isGuest) {
                            var stateHtml = '<li class="optionSelected" onclick="$(\'body\').removeClass(\'showAddressForm\');$(\'#parent-div\').removeClass(\'showAddressForm\');$(\'#shipping-address-details\').removeClass(\'showAddressForm\'); shipping.setSelectedState(event); $(\'#shipping\\\\:state_id[name=shipping\\\\[state_id\\\\]]\').val({0}).change(); $(\'#shipping-display-state\').val(\'{1}\'); $(\'#fullscreen-select-shipping-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        } else {
                            var stateHtml = '<li class="optionSelected" onclick="$(\'#shipping-address-details\').removeClass(\'showAddressForm\'); shipping.setSelectedState(event); $(\'#shipping\\\\:state_id[name=shipping\\\\[state_id\\\\]]\').val({0}).change(); $(\'#shipping-display-state\').val(\'{1}\'); $(\'#fullscreen-select-shipping-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        }
                    } else {
                        if (isGuest){
                            var stateHtml = '<li onclick="$(\'body\').removeClass(\'showAddressForm\');$(\'#parent-div\').removeClass(\'showAddressForm\');$(\'#shipping-address-details\').removeClass(\'showAddressForm\'); shipping.setSelectedState(event); $(\'#shipping\\\\:state_id[name=shipping\\\\[state_id\\\\]]\').val({0}).change(); $(\'#shipping-display-state\').val(\'{1}\'); $(\'#fullscreen-select-shipping-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        } else {
                            var stateHtml = '<li onclick="$(\'#shipping-address-details\').removeClass(\'showAddressForm\'); shipping.setSelectedState(event); $(\'#shipping\\\\:state_id[name=shipping\\\\[state_id\\\\]]\').val({0}).change(); $(\'#shipping-display-state\').val(\'{1}\'); $(\'#fullscreen-select-shipping-state\').hide()">{2}</li>'.format(state.id_state, state.name, state.name);
                        }
                    }
                    $('#shipping-state-list').append(stateHtml).change();
                });
            }
        });
        return false;
    };

    self.setSelectedCountry = function() {
        $("#shipping_country_list li").removeClass("optionSelected");
        var selectedCountry = Number($('#shipping\\:country_id[name=shipping\\[country_id\\]]').val());
        var id = "#shipping_country_"+selectedCountry;
        $(id).addClass("optionSelected");
    };

    self.setSelectedState = function(event) {
        $("#shipping-state-list li").removeClass("optionSelected");
        $(event.target).addClass("optionSelected");
        // var state = Number($('#billing\\:country_id[name=billing\\[country_id\\]]').val());
        // var id = "#billing_country_"+selectedCountry;
        // $(id).addClass("optionSelected");
    };
};

var ShippingMethod = function (url) {
    var self = this;
    self.url = url;
    self.form = $('#shipping-method-form');
    self.next = function (isUpdateAddress) {
        if (self.form.isValid()){
            var formData = self.form.serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '&ajax=true' + '&method=updateCarrier' + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                },
                success: function(json) {
                    // if ($('#checkout-navbar').is(":hidden")){
                    //     var paymentMethod = new PaymentMethod(self.url);
                    //     paymentMethod.next();
                    //     $('#checkout-navbar').show();
                    //     $('#order-review-edit-header').hide();
                    //     $('#step3').hide();
                    //     $('#step5').show();
                    //     var navbar = new NavBar(5);
                    //     navbar.setStep();
                    // } else {
                    window.scrollTo(0, 0);
                        if (json.updated_section) {
                            $('#step4').html(json.updated_section);
                            $('input:radio[name="payment_method"]').change(
                                function(){
                                    if ($(this).is(':checked')) {
                                        $('#btn-payment-method-next').removeClass("disabled no-arrow");
                                    }
                                });
                        }
                        if (!isUpdateAddress) {
                            $('#step4').show();
                            var navbar = new NavBar(4);
                            navbar.setStep();
                        }
                        $('#step3').hide();
                        var payment_div_list = $("[id^=payment-option-]");
                        if (payment_div_list.length === 1) {
                            $("#payment-method-form > .radio-option > .option-select > .jm-radio-check > input[name='payment_method']").trigger('click')
                        }
                    // }
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState === 4) {
                        alert(request.responseText);
                    } else if (request.readyState === 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }
    };

    self.back = function () {
        window.scrollTo(0, 0);
        $('#step2').show();
        $('#step3').hide();
        var navbar = new NavBar(2);
        navbar.setStep();
    };

    self.closeEditShippingMethodForm = function(){
        $('#checkout-navbar').show();
        $('#order-review-edit-header').hide();
        $('#step3').hide();
        $('#step5').show();
        var navbar = new NavBar(5);
        navbar.setStep();
    };
};

var PaymentMethod = function(url) {
    var self = this;
    self.url = url;

    self.next = function () {
        // var validator = new Validation(this.form);
        var formData = $('#payment-method-form').serialize();

        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + "&ajax=true" + "&method=updatePayment" + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: formData,
            beforeSend: function() {

            },
            complete: function() {
            },
            success: function(json) {
                window.scrollTo(0, 0);
                $('#step5').show();
                $('#step4').hide();
                if (json.updated_section) {
                    $('#step5').html(json.updated_section);
                }
                var navbar = new NavBar(5);
                navbar.setStep();
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.back = function () {
        window.scrollTo(0, 0);
        $('#step3').show();
        $('#step4').hide();
        var navbar = new NavBar(3);
        navbar.setStep();
    };

    self.closeEditPaymentMethodForm = function(){
        $('#checkout-navbar').show();
        $('#order-review-edit-header').hide();
        $('#step4').hide();
        $('#step5').show();
        var navbar = new NavBar(5);
        navbar.setStep();
    };
};

var Review = function (url, isLogged) {
    var self = this;
    self.url = url;
    self.isLogged = isLogged;
    self.paymentForm = $('#payment-method-form');

    $('#term-and-condition-checkbox').click(function() {
        if(this.checked){
            $('#submit-order').removeClass("disabled");
        } else {
            $('#submit-order').addClass("disabled");
        }
    });

    self.setPaymentOption = function () {
        var selectedPayment = $('input[name=payment_method]:checked');
        if (selectedPayment){
            $('#review-payment-method').text(selectedPayment.data('label'));
        }
    };

    self.submitOrder = function (e) {
        var checked = $('.term-and-condition:checkbox:checked').length > 0;
        if (!checked) {
            var errMessage = "You must agree to the terms of service before continuing.";
            $('#checkout-error-content').html(errMessage);
            $('#checkout-error-dialog').show();
        } else {
            var payment_module_name = $('input:radio[name="payment-option"]:checked').attr('id');
            console.log('module name => ' + payment_module_name);
            var form_id = 'payment-form-submit-' + payment_module_name;
            $('#' + form_id).submit();
        }
    };

    self.nextStep = function (json) {
        if (json.hasError) {
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        $modal = $('#paymentModal');


        //trigger confirm order after confirming payment in dialog
        $("#paymentModal #payment_dialog_proceed").click(function () {
            self.confirmOrder();
        });

        var payment_module_elm = $('input:radio[name="payment_method"]:checked');
        var payment_module_name = payment_module_elm.length ? payment_module_elm.attr('id') : null;
        var payment_module_id = payment_module_elm.length ? payment_module_elm.val() : null;
        var id = payment_module_id;
        //var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        var url = payment_module_elm.length ? payment_module_elm.data('url') : null;

        if (payment_module_name == 'bankwire'
            || payment_module_name == 'myinvoice'
            || payment_module_name == 'cheque'
            || payment_module_name == 'cashondelivery'
            || payment_module_name == 'pscodfee'
            || payment_module_name == 'codfee'
            || payment_module_name == 'cashondeliveryplus'
            || payment_module_name == 'afterpay'
        ) {
            // Show loader & then get content when modal is shown

            $modal.on('show.bs.modal', function (e) {
                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')
                    .load(url + ' body', function (dataHtml) { // PS-971: Prevent script execution
                        // Use Bootstrap's built-in function to fix scrolling (to no avail)

                        var payment_info_html = $(dataHtml).find('#center_column');
                        $(payment_info_html).find('#order_step').remove();
                        $('h1', payment_info_html).remove();
                        $('#cart_navigation', payment_info_html).remove();
                        $('.cart_navigation', payment_info_html).remove();      // Added for Prestashop 1.5 for removing the buttons in the payment method html
                        $('#amount', payment_info_html).removeClass('price');
                        $(payment_info_html).find('form:first').find('div:first, div.box').find('p:last-child').remove();
                        $(payment_info_html).find('form:first').find('div:first, div.box').find('#currency_payement').parent().hide();
                        if (payment_module_name == 'codfee') {
                            $(payment_info_html).find('form').find('strong').hide();
                        }
                        if (payment_module_name == 'afterpay') {
                            $('p.required', payment_info_html).show();
                            $('.modal-body').css('max-height', 'calc(100vh - 95px)');
                            $('.modal-body').css('overflow-y', 'auto');
                            $('.modal-footer').hide();
                        }
                        $modal.find('.modal-body').html(payment_info_html.html());
                        $modal.modal('handleUpdate');
                    });
            }).modal();
        } else if (payment_module_name === 'paypal') {
            if (url.indexOf('javascript:') !== -1) {
                $('#paypal_process_payment').trigger('click');
                if ($("#paypal_payment_form_payment").length === 1) {
                    $('#paypal_payment_form_payment').submit();
                } else if ($("#paypal_payment_form").length === 1) {
                    $('#paypal_payment_form').submit();
                }
            }
        } else if (payment_module_name == 'paypalusa') {
            if (url == '') {
                $('#paypal-standard-btn').trigger('click');
            }
        } else if (payment_module_name == 'pronesis_bancasella') {
            $('#bancasella_process_payment').trigger('click');
        } else if (payment_module_name == 'redsys') {
            $('#redsys_form').submit();
        } else if (payment_module_name == 'payplug') {
            $('a.payplug')[0].click();
        } else if (payment_module_name == 'systempay') {
            $('#systempay_standard').submit();
            // $('#systempay_standard').trigger('click');

        } else if (payment_module_name == 'sisowideal') { // PS-649 : Support Payment Method - Sisow iDEAL
            var formSisowideal = $('#sisow_ideal_form').serialize();
            var url_ajax_sisowideal = $('#sisow_ideal_form').attr('action');
            var varlue_respose = '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url_ajax_sisowideal,
                // dataType: "json",
                data: formSisowideal,
                complete: function () {

                },
                success: function (response) {
                    payment_info_html = $(response).find("#center_column");
                    payment_info_html.find('#order_step').hide();
                    payment_info_html.find('h2').hide();
                    payment_info_html.find('#cart_navigation').hide();
                    var title = payment_info_html.find('h3').text();
                    payment_info_html.find('h3').remove();

                    $modal.find('#paymentModalLabel').text(title);
                    $modal.find('.modal-body').html(payment_info_html.html());
                    $modal.modal('handleUpdate');
                    console.log(result);
                },
                error: function (request, textStatus, errorThrown) {
                    alert('fail');
                }

            });
            $modal.on('show.bs.modal', function (e) {

                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')

            }).modal();
        } else if (payment_module_name == 'sisowcapayable') { // PS-648 : Support Payment Method - Sisow Capayable in 3 installments (0% interest)
            var formSisowcapayable = $('#sisow_capayable_form').serialize();
            var url_ajax_sisowcapayable = $('#sisow_capayable_form').attr('action');
            var varlue_respose = '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url_ajax_sisowcapayable,
                // dataType: "json",
                data: formSisowcapayable,
                complete: function () {

                },
                success: function (response) {
                    payment_info_html = $(response).find("#center_column");
                    payment_info_html.find('#order_step').hide();
                    payment_info_html.find('h2').hide();
                    payment_info_html.find('#cart_navigation').hide();
                    payment_info_html.find('.breadcrumb').hide();
                    $modal.find('.modal-body').html(payment_info_html.html());
                    $modal.modal('handleUpdate');
                    console.log(result);
                },
                error: function (request, textStatus, errorThrown) {
                    alert('fail');
                }

            });
            $modal.on('show.bs.modal', function (e) {

                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')

            }).modal();
        } else if (payment_module_name == 'sisowafterpay') { //PS-645 : Support Payment Method - Sisow Pay after Receipt
            var formSisowafterpay = $('#sisow_afterpay_form').serialize();
            var url_ajax_sisowafterpay = $('#sisow_afterpay_form').attr('action');
            var varlue_respose = '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url_ajax_sisowafterpay,
                // dataType: "json",
                data: formSisowafterpay,
                complete: function () {

                },
                success: function (response) {
                    payment_info_html = $(response).find("#center_column");
                    payment_info_html.find('#order_step').hide();
                    payment_info_html.find('h2').hide();
                    payment_info_html.find('#cart_navigation').hide();
                    payment_info_html.find('.breadcrumb').hide();
                    $modal.find('.modal-body').html(payment_info_html.html());
                    $modal.modal('handleUpdate');
                    console.log(result);
                },
                error: function (request, textStatus, errorThrown) {
                    alert('fail');
                }

            });
            $modal.on('show.bs.modal', function (e) {

                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')

            }).modal();
        } else if (payment_module_name == 'sisowpp') { //PS-653 : Support Payment Method - Sisow PayPal
            $('#sisow_paypalec_form').submit();
        } else if (payment_module_name == 'sisowbelfius') { //PS-647 : Support Payment Method -Sisow belfius
            $('#sisow_belfius_form').submit();
        } else if (payment_module_name == 'sisowmaestro') { //PS-661 : Support Payment Method - Sisow Maestro
            $('#sisow_maestro_form').submit();
        } else if (payment_module_name == 'sisowmastercard') { //PS-650 : Support Payment Method - Sisow MasterCard
            $('#sisow_mastercard_form').submit();
        } else if (payment_module_name == 'sisowvisa') { //PS-643 : Support Payment Method - Sisow Visa
            $('#sisow_visa_form').submit();
        } else if (payment_module_name == 'sisowmc') { //PS-646 : Support Payment Method - Sisow Bancontact
            $('#sisow_mistercash_form').submit();
        } else if (payment_module_name == 'sisowde') { //PS-644 : Support Payment Method - Sisow SofortBanking
            $('#sisow_sofort_form').submit();
        } else if (payment_module_name === 'stripe_official') {
            if (!this.payment_form_html) {
                var payment_form = $('#payment_method_' + payment_module_id);
                if (payment_form.length) {
                    this.payment_form_html = payment_form.html();
                    payment_form.remove();
                }
            }
            if (this.payment_form_html) {
                $modal.find('.modal-body').html(this.payment_form_html);
                $modal.find('.modal-footer').hide();
                $modal.modal('show');
            }
        } else if (payment_module_name == 'mollie') { //PS-1324: Support Mollie
            if (url && url.indexOf('http') !== -1) {
                window.location.href = url;
            }
        } else if (payment_module_name == 'cmcicpaiement') {
            var $form = $('#payment_method_' + payment_module_id + ' form');
            if ($form.length) {
                $form.submit();
            }
        }
    };

    self.confirmOrder = function() {
        $modal = $('#paymentModal form').submit();
    };

    self.submitOrder = function() {
        var $term_and_condition = $('.term-and-condition:checkbox');
        if ($term_and_condition.length && !$term_and_condition.prop('checked')) {
            var errMessage = "You must agree to the terms of service before continuing.";
            $('#checkout-error-content').html(errMessage);
            $('#checkout-error-dialog').show();
        } else {
            var params = self.paymentForm.serialize();
            if (this.agreementsForm) {
                params += '&' + self.agreementsForm.serialize();
            }

            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + "&ajax=true" + "&PlaceOrder=1" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: params,
                beforeSend: function () {

                },
                complete: function () {
                },
                success: function (json) {
                    self.nextStep(json);
                },
                error: function (request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }
    };

    self.editBillingAddress = function () {
        window.scrollTo(0, 0);
        var navbar = new NavBar(1);
        navbar.setStep();
        $('#step1').show();
        $('#step5').hide();
    };

    self.editShippingAddress = function () {
        window.scrollTo(0, 0);
        var navbar = new NavBar(2);
        navbar.setStep();
        $('#step2').show();
        $('#step5').hide();
    };

    self.editShippingMethod = function () {
        window.scrollTo(0, 0);
        var navbar = new NavBar(3);
        navbar.setStep();
        $('#step3').show();
        $('#step5').hide();
    };

    self.editPaymentMethod = function (){
        window.scrollTo(0, 0);
        var navbar = new NavBar(4);
        navbar.setStep();
        $('#step4').show();
        $('#step5').hide();
    };
};

var Coupon = function (url) {
    var self = this;
    var vars = {};
    self.url = url;

    self.resetLoadWaiting = function () {
        checkout.setLoadWaiting(false);
    };

    $('#addcode-discount').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(parseInt(keycode) === 13){
            event.preventDefault();
            event.stopPropagation();
            self.showLoading();
            document.activeElement.blur();
            $("#addcode-discount").blur();
            self.save($('#addcode-discount').val());
        }
    });

    self.save = function (coupon_code) {
        var formData = "remove=0&coupon_code="+coupon_code;
        $.ajax({
            type: "POST",
            headers: {"cache-control": "no-cache"},
            url: self.url + '&ajax=true' + '&submitDiscount=true' + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                //$('#cart_update_warning .permanent-warning').remove();
                //$('#confirmLoader').show();
            },
            complete: function () {
                // $('#confirmLoader').hide();
            },
            success: function (json) {
                self.hideLoading();
                self.showBtnDiscount();
                if (json.hasError) {
                    var error_msg = json.errors[0];
                    $('#order-review-error-msg').html(error_msg);
                    $('#order-review-error').show();
                    setTimeout(function() { $('#order-review-error').hide(); }, 3000);
                } else {
                    if (json.updated_section) {
                        $('#step5').html(json.updated_section)
                    }
                }
            },
            error: function (request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.remove = function (event, discountId) {
        event.stopPropagation();
        $.ajax({
            type: "POST",
            headers: {"cache-control": "no-cache"},
            url: self.url + '&ajax=true' + '&deleteDiscount=' + discountId + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                //$('#cart_update_warning .permanent-warning').remove();
                //$('#confirmLoader').show();
            },
            complete: function () {
                // $('#confirmLoader').hide();
            },
            success: function (json) {
                if (json.updated_section) {
                    $('#step5').html(json.updated_section)
                }
            },
            error: function (request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.setLoadWaiting = function (show) {

        if (show) {
            if (self.loadWaiting) {
                this.setLoadWaiting(false);
            }

            var container = $('#' + step + '-buttons-container');
            container.attr('disabled');
            container.css('{opacity:.5}');
            self.getLaddaButton().start();
            self._disableEnableAll(container, true);

        } else {
            if (self.loadWaiting) {
                var container = $('#' + this.loadWaiting + '-buttons-container');
                var isDisabled = (keepDisabled ? true : false);
                if (!isDisabled) {
                    container.removeAttr('disabled');
                    container.css('{opacity:1}');
                }
                self._disableEnableAll(container, isDisabled);
                self.getLaddaButton(step).stop();
            }
        }
        self.loadWaiting = show;
    }

    self.getLaddaButton = function () {

    }

    self._disableEnableAll = function () {

    }


    self.nextStep = function (json) {
        if (json.hasError) {
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        if (json.updated_section) {
            $('#checkout-review-load').html(json.updated_section);
        }
    };

    self.showAddCodeDiscount = function () {
        $('#btn-discount').hide();
        $('#discount-field').show();
    };

    self.showBtnDiscount = function () {
        $('#addcode-discount').val('');
        $('#btn-discount').show();
        $('#discount-field').hide();
    };

    self.showLoading = function () {
        $('#addcode-buttons').hide();
        $('#addcode-loading').show();
    };

    self.hideLoading = function () {
        $('#addcode-buttons').show();
        $('#addcode-loading').hide();
    };

    self.applySuggestedCoupon = function (coupon_code) {
        self.save(coupon_code)
    };

    self.toggleShowDiscountCode = function (event) {
        $('#order-review-coupon').slideToggle();
        $(event.target).parent().toggleClass('discount-list-open');
    };

    self.showDeleteButton = function (event, code) {
        event.stopPropagation();
        $('#icon-trash-'+code).hide();
        $('#price-'+code).hide();
        $('#remove-'+code).show();
    };
};

var NavBar = function(step){
    var self = this;
    self.step = step;
    self.setStep = function(){
        switch(self.step){
            case 1:
                $('#navbar-title').html($('#navbar-title-1').html());
                $('#navbar-step1').removeClass("active old");
                $('#navbar-step2').removeClass("active old");
                $('#navbar-step3').removeClass("active old");
                $('#navbar-step4').removeClass("active old");
                $('#navbar-step5').removeClass("active old");
                $('#navbar-step1').addClass("active");
                break;
            case 2:
                $('#navbar-title').html($('#navbar-title-2').html());
                $('#navbar-step1').removeClass("active old");
                $('#navbar-step2').removeClass("active old");
                $('#navbar-step3').removeClass("active old");
                $('#navbar-step4').removeClass("active old");
                $('#navbar-step5').removeClass("active old");
                $('#navbar-step1').addClass("old");
                $('#navbar-step2').addClass("active");
                break;
            case 3:
                $('#navbar-title').html($('#navbar-title-3').html());
                $('#navbar-step1').removeClass("active old");
                $('#navbar-step2').removeClass("active old");
                $('#navbar-step3').removeClass("active old");
                $('#navbar-step4').removeClass("active old");
                $('#navbar-step5').removeClass("active old");
                $('#navbar-step1').addClass("old");
                $('#navbar-step2').addClass("old");
                $('#navbar-step3').addClass("active");
                break;
            case 4:
                $('#navbar-title').html($('#navbar-title-4').html());
                $('#navbar-step1').removeClass("active old");
                $('#navbar-step2').removeClass("active old");
                $('#navbar-step3').removeClass("active old");
                $('#navbar-step4').removeClass("active old");
                $('#navbar-step5').removeClass("active old");
                $('#navbar-step1').addClass("old");
                $('#navbar-step2').addClass("old");
                $('#navbar-step3').addClass("old");
                $('#navbar-step4').addClass("active");
                break;
            case 5:
                $('#navbar-title').html($('#navbar-title-5').html());
                $('#navbar-step1').removeClass("active old");
                $('#navbar-step2').removeClass("active old");
                $('#navbar-step3').removeClass("active old");
                $('#navbar-step4').removeClass("active old");
                $('#navbar-step5').removeClass("active old");
                $('#navbar-step1').addClass("old");
                $('#navbar-step2').addClass("old");
                $('#navbar-step3').addClass("old");
                $('#navbar-step4').addClass("old");
                $('#navbar-step5').addClass("active");
                break;
        }
    };
};

String.prototype.format = function() {
    a = this;
    for (k in arguments) {
        a = a.replace("{" + k + "}", arguments[k])
    }
    return a
};

var entityMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
    '/': '&#x2F;',
    '`': '&#x60;',
    '=': '&#x3D;'
};

function escapeHtml (string) {
    return String(string).replace(/[&<>"'`=\/]/g, function (s) {
        return entityMap[s];
    });
}

function buildBillingAddressHtml(address, id_address, isExist, fullAddress, editAddressLabel){
    var html = "";
    if(!isExist){
        html = "<div class=\"saved-address mb-2 clearfix\" id=\"invoice-address-{0}\">".format(id_address);
    }
    html += "<div class=\"row positionRelative\">\n";
    html += "<div class=\"col-10 col-xs-10\">\n";
    html += "<h4 class=\"address-name\">{0} {1}</h4>".format(address.firstname, address.lastname);
    if (address.company){
        html += "<span class=\"address-company\">{0}</span>".format(address.company);
    }
    if (address.vat_number){
        html += "<span class=\"address-vat\">{0}</span>".format(address.vat_number);
    }
    html += "<span class=\"address-location\">{0}</span>\n".format(address.address1);
    if (address.address2) {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.address2);
    }

    var country = address['billing-display-country'] ? address['billing-display-country'] : address['shipping-display-country'] ? address['shipping-display-country'] : "";
    if(["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "", address.city);
    } else if (["United Kingdom", "Vietnam"].includes(country)){
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "", address.postcode);
    } else if (["United States", "Australia"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.city, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "", address.postcode);
    } else if (["China", "France", "Israel"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode, address.city, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "");
    } else {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address.postcode, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "");
    }
    html += "<span class=\"address-country\">{0}</span>\n".format(address['billing-display-country'] ? address['billing-display-country'] : address['shipping-display-country'] ? address['shipping-display-country'] : "");
    if (address.phone) {
        html += "<a href=\"tel:{1}\" class=\"address-mobile\">{0}</a>\n".format(address.phone, address.phone);
    }
    if (address.phone_mobile){
        html += "<div><a href=\"tel:{1}\" class=\"address-mobile\">{0}</a></div>".format(address.phone_mobile, address.phone_mobile);
    }
    html += "<div><a class=\"blue address-edit\" onclick=\"billing.edit({0}, {1})\">".format(escapeHtml(JSON.stringify(fullAddress)), 'false')+editAddressLabel+"</a></div>";
    html += "</div>";
    html += ("<div class=\"col-2 col-xs-2 positionStatic text-center\">\n" +
        "  <label class=\"jm-radio-check mb-0\" id=\"billing-radio-"+id_address+"\">\n" +
        "    <input type=\"radio\" name=\"selected-billing-address\" value=\"{0}\">\n" +
        "    <span class=\"radio-check\"></span>\n" +
        "  </label>\n" +
        "</div>").format(id_address);
    html += "</div>";
    if(!isExist){
        html += "</div>";
    }
    return html;
}

function buildShippingAddressHtml(address, id_address, isExist, fullAddress, editAddressLabel){
    var html = "";
    if(!isExist){
        html = "<div class=\"saved-address mb-2 clearfix\" id=\"delivery-address-{0}\">".format(id_address);
    }
    html += "<div class=\"row positionRelative\">\n";
    html += "<div class=\"col-10 col-xs-10\">\n";
    html += "<h4 class=\"address-name\">{0} {1}</h4>".format(address.firstname, address.lastname);
    if (address.company){
        html += "<span class=\"address-company\">{0}</span>".format(address.company);
    }
    if (address.vat_number){
        html += "<span class=\"address-vat\">{0}</span>".format(address.vat_number);
    }
    html += "<span class=\"address-location\">{0}</span>\n".format(address.address1);
    if (address.address2) {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.address2);
    }
    var country = address['billing-display-country'] ? address['billing-display-country'] : address['shipping-display-country'] ? address['shipping-display-country'] : "";
    if(["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "", address.city);
    } else if (["United Kingdom", "Vietnam"].includes(country)){
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "", address.postcode);
    } else if (["United States", "Australia"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.city, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "", address.postcode);
    } else if (["China", "France", "Israel"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode, address.city, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "");
    } else {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address.postcode, address['billing-display-state'] ? address['billing-display-state'] : address['shipping-display-state'] ? address['shipping-display-state'] : "");
    }    html += "<span class=\"address-country\">{0}</span>\n".format(address['billing-display-country'] ? address['billing-display-country'] : address['shipping-display-country'] ? address['shipping-display-country'] : "");
    if (address.phone) {
        html += "<a href=\"tel:{1}\" class=\"address-mobile\">{0}</a>\n".format(address.phone, address.phone);
    }
    if (address.phone_mobile){
        html += "<div><a href=\"tel:{1}\" class=\"address-mobile\">{0}</a></div>".format(address.phone_mobile,address.phone_mobile);
    }
    html += "<div><a class=\"blue address-edit\" onclick=\"shipping.edit({0}, {1})\">".format(escapeHtml(JSON.stringify(fullAddress)), 'false')+editAddressLabel+"</a></div>";
    html += "</div>";
    html += ("<div class=\"col-2 col-xs-2 positionStatic text-center\">\n" +
        "  <label class=\"jm-radio-check mb-0\" id=\"shipping-radio-"+id_address+"\">\n" +
        "    <input type=\"radio\" name=\"selected-shipping-address\" value=\"{0}\" id=\"selected-shipping-address-{0}\">\n".format(id_address) +
        "    <span class=\"radio-check\"></span>\n" +
        "  </label>\n" +
        "</div>").format(id_address);
    html += "</div>";
    if(!isExist){
        html += "</div>";
    }
    return html;
}

var AddressService = function (url) {
    var self = this;
    self.url = url;

    self.getAddressForm = function (countryId, callback, step) {

        $.ajax( {
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&ReloadFormField=true'+'&step='+step+'&rand=' + new Date().getTime(),
            async: true,
            data :{ id_country : countryId},
            cache: false,
            beforeSend: function() {
                //TODO Implement this
            },
            complete: function() {
            },
            success: function (json) {
                callback(JSON.parse(json));
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    }

    self.getCountries = function (callback) {

        $.ajax( {
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&get_countries=true'+'&rand=' + new Date().getTime(),
            async: true,
            data :{},
            cache: false,
            beforeSend: function() {
                //TODO Implement this
            },
            complete: function() {
            },
            success: function (json) {
                callback(JSON.parse(json));
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    }
};

var AddressFormUpdater = function(type , addresses, countries, customer) {

    var self = this;

    var contructor = function (type, addresses, countries, customer) {
        self.type = type;
        self.addresses = [];
        if (addresses) {
            self.addresses = addresses;
        }
        // self.addresses = addresses;
        self.customer = customer;
        self.countries = countries;
    };

    contructor(type, addresses, countries, customer);

    self.replaceFormFieldSet = function(fieldset) {
        $("#"+self.type+"-new-address-form").html(fieldset);
    };

    self.reloadForm = function (data) {

        // self.countries = data["countries"];
        var field_show = data["ordered_adress_fields"];
        var field_require = data["requireds_fields"];
        var customer_field_require = data["customer_form_required"];

        // store user-entered data
        formData = $('#co-'+ self.type + '-form').serialize();

        // replace form fields
        if (isLogged) {
            self.replaceFormFieldSet(data['output']);
        }
        // Restore user-entered data
        $('#co-'+self.type + '-form').deserialize(formData);

        //restore validation
        $.validate({lang : langCode});

        self.bindData(data["current_country"]);

        // clear form blank for crete new.
        if (($("#"+self.type+"-address-select :selected").val() == "") && ($("#"+self.type+"\\:country_id :selected").val() == current_country) && billing.addresses.length >=1) {
            self.clearAddress();
        }

        $('#address2_hide').hide();
        $("#"+self.type+"_address2_hide").hide();
        if (field_show && field_require) {
            $('#address_guest_form li').hide();
            $("#Country_zip_code_format").hide();
            $("#dni").hide();
            $("#State_name").hide();

            $('#address_guest_form').show();
            $('#newsletter').show();
            for (i in field_show) {
                var temp = field_show[i].replace(/:/i,'_');
                if (temp == 'vat_number') continue;
                $('#'+temp).show();
            }
            for (i in field_require) {
                var temp = field_require[i].replace(/:/i,'_');
                if (temp == 'vat_number') continue;
                $('#'+temp).show();
            }
        }

        self.updateState(self.countries);
        self.updateZipcode(self.countries);
        self.updateNeedDNI(self.countries);
        self.showVatNumber();



        // set required field for phone number
        if (customer_field_require !== undefined
            && customer_field_require.indexOf("phone") < 0
            && customer_field_require.indexOf("mobile_phone") < 0
            && ones_phone_at_least != "0") {
            $("#phone_mobile_label").addClass('required');
        }

        // self.bindData(data["current_country"]);
        // self.resetLoadWaiting(false);
        // self.reloadCountrySelected();
    };

    self.getSelectedAddress = function() {
        var selectedId = $('select[name="' + self.type + '_address_id"] option:selected').val();
        var selectedAddress;
        var addresses = billing.addresses;

        for (var i = 0; i < addresses.length; i++) {
            if (addresses[i].id_address == selectedId) {
                selectedAddress = addresses[i];
            }
        }
        return selectedAddress;
    };

    // Check if country has no state will not show field State.
    self.updateState = function (countries) {
        self.updateStates(self.countries);
        var selectedAddress = self.getSelectedAddress();
        if (self.countries[$('#'+self.type+'_country_id select').val()] !== undefined
            && self.countries[$('#'+self.type+'_country_id select').val()]["states"] !== undefined
            && self.countries[$('#'+self.type+'_country_id select').val()]["contains_states"] == 1) {
            try {
                if (isLogged) {
                    (selectedAddress !== undefined) ? $("#"+self.type+"\\:state_id").val(selectedAddress.id_state): $("#"+self.type+"\\:state_id").val('');
                }
                if ($("#"+self.type+"\\:state_id").val() === null) {
                    $("#"+self.type+"\\:state_id").val('');
                }
            } catch (e) {}
            // $("li[id = State_name]").show();
            $("#"+self.type+"-display-state-div").show();
        } else {
            // $("li[id = State_name]").hide();
            $("#"+self.type+"-display-state-div").hide();
        }
    };

    self.updateZipcode = function (countries) {
        if (countries[(parseInt($('#'+self.type+'_country_id select').val()))] !== undefined
            && countries[(parseInt($('#'+self.type+'_country_id select').val()))]['id_country'] !== 'undefined'
            && countries[(parseInt($('#'+self.type+'_country_id select').val()))]['need_zip_code'] == 1 ) {
            if (self.type == "billing") {
                $("#Country_zip_code_format").show();
            } else {
                $("#shipping_Country_zip_code_format").show();
            }
        } else {
            if (self.type == "billing") {
                $("#Country_zip_code_format").hide();
            } else {
                $("#shipping_Country_zip_code_format").hide();
            }
        }
    };

    self.updateNeedDNI = function (countries) {
        if (self.countries[parseInt($('#'+self.type+'_country_id select').val())] !== undefined
            && self.countries[
                parseInt($('#'+self.type+'_country_id select').val())
                ]['need_identification_number'] == '1') {
            if (self.type == "billing") {
                $("#dni").show();
            } else {
                $("#shipping_dni").show();
            }
        } else {
            if (self.type == "billing") {
                $("#dni").hide();
            } else {
                $("#shipping_dni").hide();
            }
        }
    };

    self.showVatNumber = function () {
        var id_country = parseInt($('#'+self.type+'_country_id select').val()) ;
        var check = "input[name='"+self.type+"[company]']";
        if ($(check).val() !== "" && $(check).val() !== undefined) {
            $('#vat_number').show();
            $('#shipping_vat_number').show();
            //
            if (!$("#vat_number_label").hasClass('required')) {
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: baseDir + 'modules/vatnumber/ajax.php?id_country=' + id_country + '&rand=' + new Date().getTime(),
                    success: function(isApplicable) {
                        if(isApplicable === undefined)
                        {
                            $('#vat_number').hide();
                            $('#shipping_vat_number').hide();
                        }
                    }
                });
            }
        } else {
            $('#vat_number').hide();
            $('#shipping_vat_number').hide();

        }
    }

    self.convertError = function (err) {
        var label = err;
        var decoded = $('<div/>').html(label).text();
        return decoded;
    }

    self.resetLoadWaiting = function(transport){
        checkout.setLoadWaiting(false);
    };

    self.fillMessRequired = function (message) {
        if (message !== undefined) {

        }
    }


    self.bindData = function(current_country) {
        var selectedAddress = self.getSelectedAddress();

        if (selectedAddress !== undefined) {
            $('input[name="'+self.type+'[firstname]"]').val((selectedAddress.firstname)? selectedAddress.firstname: '');
            $('input[name="'+self.type+'[lastname]"]').val((selectedAddress.lastname) ? selectedAddress.lastname : '');
            if(selectedAddress.email !== null) {
                $('input[name="'+self.type+'[email]"]').val(selectedAddress.email);
            }
            $('input[name="'+self.type+'[company]"]').val((selectedAddress.company != " ")? selectedAddress.company : '');
            $('input[name="'+self.type+'[vat_number]"]').val((selectedAddress.vat_number != "") ? selectedAddress.vat_number : '');
            $('input[name="'+self.type+'[street]"]').val((selectedAddress.address1) ? selectedAddress.address1 : '');
            if(selectedAddress.address2){
                $('input[name="'+self.type+'[street2]"]').val((selectedAddress.address2 != " ") ? selectedAddress.address2 : '');
            } else {
                $('input[name="'+self.type+'[street2]"]').val('');

            }
            if (selectedAddress.phone) {
                $('input[name="'+self.type+'[telephone]"]').val((selectedAddress.phone != "") ? selectedAddress.phone : '');
            } else {
                $('input[name="'+self.type+'[telephone]"]').val('');
            }
            $('input[name="'+self.type+'[postcode]"]').val((selectedAddress.postcode) ? selectedAddress.postcode : '');
            $('input[name="'+self.type+'[city]"]').val((selectedAddress.city != " ") ? selectedAddress.city : '');
            if(selectedAddress.phone_mobile){
                $('input[name="'+self.type+'[phone_mobile]"]').val((selectedAddress.phone_mobile != "") ? selectedAddress.phone_mobile : '');
            } else  {
                $('input[name="'+self.type+'[phone_mobile]"]').val('');
            }
            $('input[name="'+self.type+'[alias]"]').val(selectedAddress.alias);
            $('input[name="'+self.type+'[dni]"]').val((selectedAddress.dni) ? selectedAddress.dni : '');
            $('textarea[name="'+self.type+'[other]"]').val((selectedAddress.other) ? selectedAddress.other : '');

            if (self.type == 'shipping' && isLogged) {
                $('#'+self.type+'\\:country_id').val(current_country);
            } else {
                $('#'+self.type+'\\:country_id').val(selectedAddress.id_country);
            }
            $('#'+self.type+'\\:state_id').val(selectedAddress.id_state);
            $('#'+self.type+'\\:state_id').trigger('change');

        } else if (selectedAddress === undefined) {
            // $('input[name="billing[firstname]"]').val(firstname_create_new);
            // $('input[name="billing[lastname]"]').val(lastname_create_new);
            // $('input[name="billing[company]"]').val(company_create_new);
            $('input[name="'+self.type+'[alias]"]').val(my_address);
            var country = (default_country != current_country) ? default_country : current_country;
            $('#'+self.type+'\\:country_id').val(current_country);
        }

        // Fill data for Guest
        if (isLogged == 0) {
            var country = (default_country != current_country) ? default_country : current_country;
            $('#'+self.type+'\\:country_id').val(current_country);
        }
    }

    self.updateStates = function(countries) {
        $('#' + (self.type) + '\\:state_id' + ' option:not(:first-child)').remove();
        if (typeof countries !== 'undefined' && $('#'+self.type+'_country_id :selected').val() !== undefined)
            var state_list = countries[$('#'+self.type+'_country_id :selected').val()]["states"];
        if (typeof state_list !== 'undefined')
        {
            for (var key in state_list) {
                $('#' + (self.type) + '\\:state_id').addClass('validate-select').append('<option value="' + parseInt(state_list[key].id_state) + '">' + state_list[key].name + '</option>');
            };

            $('#' + (self.type) + '\\:state' + ':hidden').show();
        }
        else
            $('#' + (self.type) + '\\:state').hide();
    }

    self.isValidCustomer = function () {
        return (self.customer && self.customer.id && self.customer.is_guest);
    }

    self.clearAddress = function() {
        if (self.isValidCustomer()) {
            customer_firstname = self.customer.firstname;
            customer_lastname = self.customer.lastname;
            if (self.customer.company) {
                customer_company = self.customer.company;
            }
        }

        //Init these data with customer info
        $('input[name="'+ self.type + '[firstname]"]').val(self.customer.firstname);
        $('input[name="'+ self.type + '[lastname]"]').val(self.customer.lastname);
        $('input[name="'+ self.type + '[company]"]').val(self.customer.company);
        $('input[name="'+ self.type + '[email]"]').val('');
        $('input[name="'+ self.type + '[vat_number]"]').val('');
        $('input[name="'+ self.type + '[street]"]').val('');
        $('input[name="'+ self.type + '[street2]"]').val('');
        $('input[name="'+ self.type + '[telephone]"]').val('');
        $('input[name="'+ self.type + '[postcode]"]').val('');
        $('input[name="'+ self.type + '[city]"]').val('');
        $('input[name="'+ self.type + '[state_id]"]').val('');
        $('input[name="'+ self.type + '[phone_mobile]"]').val('');
        $('textarea[name="'+ self.type + '[other]"]').val('');
        $('input[name="'+ self.type + '[alias]"]').val(my_address);
        $('input[name="'+ self.type + '[dni]"]').val('');


    }


    self.initAddressForm = function() {



        selectedBillingAddress = self.getSelectedAddress();


        if (self.addresses && self.addresses.length > 0) {
            selectedBillingAddress = self.addresses[0];
        }

        if (selectedBillingAddress === undefined) {
            if (!self.customer.is_guest)
                self.clearAddress();
            self.updateState();
            self.updateNeedDNI()
            return;
        };

        //region Fill Addess
        if (selectedBillingAddress.id_customer === '0') return;

        self.newAddress(!selectedBillingAddress, self.customer.is_guest);

        // Fill invoice address
        $('input[name="'+self.type+'[firstname]"]').val((selectedBillingAddress.firstname)? selectedBillingAddress.firstname: '');
        $('input[name="'+self.type+'[lastname]"]').val((selectedBillingAddress.lastname) ? selectedBillingAddress.lastname : '');
        if(customer.email !== null) {
            $('input[name="'+self.type+'[email]"]').val(customer.email);
        }
        $('input[name="'+self.type+'[company]"]').val((selectedBillingAddress.company != " ")? selectedBillingAddress.company : '');
        $('input[name="'+self.type+'[vat_number]"]').val((selectedBillingAddress.vat_number != "") ? selectedBillingAddress.vat_number : '');


        $('input[name="'+self.type+'[street]"]').val((selectedBillingAddress.address1) ? selectedBillingAddress.address1 : '');
        if(selectedBillingAddress.address2){
            $('input[name="'+self.type+'[street2]"]').val((selectedBillingAddress.address2 != " ") ? selectedBillingAddress.address2 : '');
        } else {
            $('input[name="'+self.type+'[street2]"]').val('');

        }
        if (selectedBillingAddress.phone) {
            $('input[name="'+self.type+'[telephone]"]').val((selectedBillingAddress.phone != "") ? selectedBillingAddress.phone : '');
        } else {
            $('input[name="'+self.type+'[telephone]"]').val('');
        }
        $('input[name="'+self.type+'[postcode]"]').val((selectedBillingAddress.postcode) ? selectedBillingAddress.postcode : '');
        $('input[name="'+self.type+'[city]"]').val((selectedBillingAddress.city != " ") ? selectedBillingAddress.city : '');
        if(selectedBillingAddress.phone_mobile){
            $('input[name="'+self.type+'[phone_mobile]"]').val((selectedBillingAddress.phone_mobile != "") ? selectedBillingAddress.phone_mobile : '');
        } else  {
            $('input[name="'+self.type+'[phone_mobile]"]').val('');
        }
        // else {
        //     $('input[name="billing[telephone]"]').val(selectedBillingAddress.phone_mobile);
        // }
        $('input[name="'+self.type+'[alias]"]').val(selectedBillingAddress.alias);
        $('input[name="'+self.type+'[dni]"]').val((selectedBillingAddress.dni) ? selectedBillingAddress.dni : '');
        $('textarea[name="'+self.type+'[other]"]').val((selectedBillingAddress.other) ? selectedBillingAddress.other : '');


        if (self.type == 'billing' && self.customer && self.customer.is_guest) {
            $('input[name="newsletter"]').prop(
                'checked', self.customer.newsletter == '1' ? true : false);
            $('input[name="optin"]').prop(
                'checked', self.customer.optin == '1' ? true : false);

            $('input[name= "billing[gender_id]"][value="'
                + self.customer.id_gender +  '"]').click();

        }

        // Fill customer 's birthday
        if (self.customer &&  self.customer.birthday) {
            birthday = self.customer.birthday.split('-');
            $('select[name="'+self.type+'[days]"]').val(parseInt(birthday[2]));
            $('select[name="'+self.type+'[months]"]').val(parseInt(birthday[1]));
            $('select[name="'+self.type+'[years]"]').val(parseInt(birthday[0]));
        }

        $('#'+self.type+'\\:country_id').val(selectedBillingAddress.id_country);

        // Setup State <Select/> if country has states
        self.updateState();
        self.updateNeedDNI();
        $('#'+self.type+'\\:state_id').val(selectedBillingAddress.id_state);
        self.showVatNumber();
        //endregion
    };

    self.newAddress = function(isNew, isGuest){
        if (isNew) {
            self.resetSelectedAddress();
            $('#'+self.type+'-new-address-form').show();
        } else {
            if(isGuest == 1) {
                $('#'+self.type+'-new-address-form').show();
            } else {
                if($('#'+self.type+'\\:edit').val() == '0') {
                    $('#'+self.type+'-new-address-form').hide();
                } else {
                    $('#'+self.type+'-new-address-form').show();
                }
            }
        }
    };

    self.resetSelectedAddress =  function(){
        var selectElement = $('#'+self.type+'-address-select');
        if (selectElement) {
            selectElement.value='';
        }
    };

    self.updateAddresses = function() {
        addresses;
    };

    self.hideForm = function () {
        // Hide Form after all success.
        if (isLogged) {
            $("#shipping-new-address-form").hide();
            $("#billing-new-address-form").hide();
        }
    }
};

var HandelBackButton = function () {
    var self = this;
    self.catchBackButtonEvent = function() {
        jQuery(document).ready(function($) {
            if (window.history && window.history.pushState) {

                window.history.pushState('forward', null, '');

                $(window).on('popstate', function() {
                    var backButton = $(".back-btn:visible");
                    if (backButton.length) {
                        backButton.trigger("click");
                    } else {
                        $('#cancelConfirmDialog').show();
                    }
                    self.catchBackButtonEvent();
                });
            }
        });
    };
};

$(document).ready(function () {
    var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
    if (isSafari){
        $('body').addClass('isSafari');
    }
})
