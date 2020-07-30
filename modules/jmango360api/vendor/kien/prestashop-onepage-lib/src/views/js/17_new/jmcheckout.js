var PersonalInformation = function(url) {
    var self = this;
    self.url = url.replace(/\/$/, '');
    self.form = $('#billing-address-customer-info');
    self.addressForm = $('#billing-address-form');
    self.save = function(default_country_id) {
        // var validator = new Validation(this.form);
        self.addressForm.isValid();
        if (self.form.isValid() && self.addressForm.isValid()) {
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
                    if (!json.errors || !json.errors.length) {
                        $('#invoice-token').val(json[1]['token']).triggerHandler('change');
                        var billing = new Billing(self.url);
                        billing.save(true, default_country_id, '');
                    } else {
                        $('#checkout-error-content').html(json.errors);
                        $('#checkout-error-dialog').show();
                    }
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

var Billing = function(url) {
    var self = this;
    var address;
    self.url = url.replace(/\/$/, '');
    self.edit = function(address, changeCountry, default_country_id, hide_address_list){
        var selectedCountry;
        self.form = $('#billing-address-form');
        if (!hide_address_list) {
            $("#parent-div").addClass("showAddressForm");
            $("body").addClass("showAddressForm");
        }
        $('input[name=use_for_shipping][value=\'1\']').prop('checked', true).change();
        if (address) {
            this.address = address;
            selectedCountry = address.id_country;
        } else {
            address = new Object();
            address.id_address = 0;
            address.id_country = default_country_id;
            selectedCountry = default_country_id;
        }
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + '?method=changeCountry&ajax=true' + '&id_country=' + selectedCountry + '&id_address=' + address.id_address + '&rand=' + new Date().getTime(),
            async: false,
            cache: false,
            dataType: "json",
            beforeSend: function() {

            },
            complete: function() {
                // self.resetLoadWaiting(false);
            },
            success: function(json) {
                if (changeCountry){
                    address=objectifyForm(self.form.serializeArray());
                }
                $('#billing-address-details').show();
                $('.overlay-window').animate({
                    scrollTop: 1
                }, 500);
                if (json.updated_section && ('billing' in json.updated_section)) {
                    $('#billing-address').html(json.updated_section.billing);
                }
                $('#invoice-id_country-'+address.id_country).addClass("optionSelected");
                $('#invoice-id_country').change(function(){
                    address.id_country = $('#invoice-id_country').val();
                    self.edit(address, true, default_country_id, hide_address_list);
                });
                if (address) {
                    Object.entries(address).forEach(function (field) {
                        $('#invoice-' + field[0]).val(field[1]);
                        if ((field[0] === 'invoice-display-id_state' || field[0] === 'delivery-display-id_state') || field[0] === 'state') {
                            $('#invoice-display-id_state').val(field[1]);
                        }
                        if (field[0] === 'id_state') {
                            $('#invoice-id_state-'+field[1]).addClass("optionSelected");
                        }
                    });
                }
                if (!window.location.pathname.includes('jmango360api') && !window.location.search.includes('jmango360api')) {
                    if (!hide_address_list) {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $("#billing-address-details").css("padding-bottom", padding);
                        $('#fullscreen-select-invoice-id_country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-invoice-id_state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            if (padding < 100) {
                                $("#billing-address-details").css("padding-bottom", padding);
                            }
                            $('#fullscreen-select-invoice-id_country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-invoice-id_state > .overlay-window').css("padding-bottom", padding);
                        };
                    } else {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $('#fullscreen-select-invoice-id_country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-invoice-id_state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            $('#fullscreen-select-invoice-id_country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-invoice-id_state > .overlay-window').css("padding-bottom", padding);
                        };
                    }
                }
                if (changeCountry){
                    $('#invoice-display-id_state').val("");
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
        // } else {
        //     $('#billing-address-details').show();
        // }
    };

    self.save = function(isGuest, default_country_id, editAddressLabel){
        bodyScrollLock.clearAllBodyScrollLocks();
        self.form = $('#billing-address-form');
        self.form2 = $('#billing-address-customer-info');
        if (!isGuest) {
            $('#billing-address-details').addClass('overlay-window');
        }
        if (self.form.isValid()) {
            var formData = $(self.form).serialize();
            self.formData = $('#billing-address-form :visible').serializeArray();
            self.fullFormData = $('#billing-address-form').serializeArray();
            var fulladdr = objectifyForm(self.fullFormData);
            var id_address = fulladdr.id_address;
            $('#same-as-billing-address').val(id_address);
            if(id_address){
                formData=formData+"&billing_address_id="+id_address;
                formData += '&billing[edit]=1';
            }
            self.formData = $('#billing-address-form :visible').serializeArray();
            self.fullFormData = $('#billing-address-form').serializeArray();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?ajax=true' + '&rand=' + new Date().getTime(),
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
                    // self.nextStep(json);
                    //no error
                    if (!json.hasError){
                        $("#parent-div").removeClass("showAddressForm");$("body").removeClass("showAddressForm");

                        var useForShipping = Number($('input[name=use_for_shipping]:checked').val());
                        var address = objectifyForm(self.formData);
                        var fullAddress = objectifyForm(self.fullFormData);
                        address.id_address = json.id_address_invoice;
                        fullAddress.id_address = json.id_address_invoice;
                        $('input[name=selected-billing-address][value='+json.id_address_invoice+']').prop('checked', true).change();
                        if (!useForShipping) {
                            if (!isGuest) {
                                $('#billing-address-details').hide();
                                $('#billing-address-form-list').show();
                            } else {
                                var shipping = new Shipping(self.url);
                                shipping.edit(null, false, default_country_id, true);
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
                                var shipping = new Shipping(self.url);
                                shipping.edit(fullAddress, false, default_country_id, true);
                            }
                            $('#selected-shipping-address-'+json.id_address_delivery).prop("checked", true);
                            var navbar = new NavBar(3);
                            navbar.setStep();
                        }
                        if (json.updated_section && ('shipping_method' in json.updated_section)) {
                            $('#step3').html(json.updated_section.shipping_method);
                        }
                        var billinghtml;
                        var shippinghtml;
                        for(var i = 0; i < json.addresses.length; i++) {
                            if (parseInt(json.id_address_invoice) === parseInt(json.addresses[i].id_address) ){
                                for (const [key, value] of Object.entries(json.addresses[i])) {
                                    if (!address[key]) {
                                        address[key] = json.addresses[i][key];
                                    }
                                }
                            }
                        }
                        if ($('#invoice-address-'+json.id_address_invoice).length > 0) {
                            billinghtml = buildBillingAddressHtml(address, json.id_address_invoice, true, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, json.id_address_invoice, true, fullAddress, editAddressLabel);
                            $('#invoice-address-'+json.id_address_invoice).html(billinghtml);
                            $('#delivery-address-'+json.id_address_invoice).html(shippinghtml);
                        } else {
                            billinghtml = buildBillingAddressHtml(address, json.id_address_invoice, false, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, json.id_address_invoice, false, fullAddress, editAddressLabel);
                            $('#invoice-address-list').append(billinghtml);
                            $('#delivery-address-list').append(shippinghtml);
                        }
                        $('[name="selected-billing-address"]').removeAttr('checked');
                        $('input[name="selected-billing-address"][value="'+json.id_address_invoice+'"]').prop('checked', true);
                        if (useForShipping) {
                            $('input[name="selected-shipping-address"][value="'+json.id_address_invoice+'"]').prop('checked', true);
                        } else{
                            if(!$('input[name=selected-shipping-address]:checked').val()){
                                $('#selected-shipping-address-'+json.id_address_delivery).prop("checked", true);
                            }
                        }
                        $('#same-as-billing-address').val(json.id_address_invoice);

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
        formData += "&use_for_shipping=0";
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + '?ajax=true' + '&rand=' + new Date().getTime(),
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
                    window.scrollTo(0, 0);
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

    self.removeTick = function (countries, fieldName, selected) {
        Object.entries(countries).forEach(function (option) {
            if (Number(option[0]) !== Number(selected)){
                $('#invoice-'+fieldName+'-'+option[0]).removeClass("optionSelected");
            }
            else {
                $('#invoice-'+fieldName+'-'+option[0]).addClass("optionSelected");
            }
        });
    };

    function objectifyForm(formArray) {//serialize data function

        var returnArray = {};
        for (var i = 0; i < formArray.length; i++){
            returnArray[formArray[i]['name']] = formArray[i]['value']?formArray[i]['value']:"";
        }
        return returnArray;
    }
};

var Shipping = function(url) {
    var self = this;
    var address;
    self.url = url.replace(/\/$/, '');
    self.edit = function(address, changeCountry, default_country_id, isGuest){
        if(!isGuest) {
            $("#parent-div").addClass("showAddressForm");
            $("body").addClass("showAddressForm");
        }
        var selectedCountry;
        if (address) {
            this.address = address;
            selectedCountry = address.id_country;
            self.form = $('#shipping-address-form');
        } else {
            address = new Object();
            address.id_address = 0;
            selectedCountry = default_country_id;
        }
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + '?method=changeDeliveryCountry&ajax=true' + '&id_country=' + selectedCountry + '&id_address=' + address.id_address + '&rand=' + new Date().getTime(),
            async: false,
            cache: false,
            dataType: "json",
            beforeSend: function() {

            },
            complete: function() {
                // self.resetLoadWaiting(false);
            },
            success: function(json) {
                if (changeCountry){
                    address=objectifyForm(self.form.serializeArray());
                }
                $('#shipping-address-details').show();
                if (json.updated_section && ('shipping' in json.updated_section)) {
                    $('#shipping-address').html(json.updated_section.shipping);
                }
                $('#delivery-id_country-'+address.id_country).addClass("optionSelected");
                $('#delivery-id_country').change(function(){
                    address.id_country = $('#delivery-id_country').val();
                    self.edit(address, url, default_country_id, isGuest);
                });
                if (address) {
                    Object.entries(address).forEach(function (field) {
                        $('#delivery-' + field[0]).val(field[1]);
                        // }
                        if (field[0] === 'invoice-display-id_state' || field[0] === 'delivery-display-id_state') {
                            $('#delivery-display-id_state').val(field[1]);
                        }
                        if (field[0] === 'id_state') {
                            $('#delivery-id_state-'+field[1]).addClass("optionSelected");
                        }
                    });
                }
                if (!window.location.pathname.includes('jmango360api') && !window.location.search.includes('jmango360api')) {
                    if (!isGuest) {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $("#shipping-address-details").css("padding-bottom", padding);
                        $('#fullscreen-select-delivery-id_country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-delivery-id_state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            if (padding < 100) {
                                $("#shipping-address-details").css("padding-bottom", padding);
                            }
                            $('#fullscreen-select-delivery-id_country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-delivery-id_state > .overlay-window').css("padding-bottom", padding);
                        };
                    } else {
                        var padding = screen.availHeight - window.innerHeight - 20;
                        $('#fullscreen-select-delivery-id_country > .overlay-window').css("padding-bottom", padding);
                        $('#fullscreen-select-delivery-id_state > .overlay-window').css("padding-bottom", padding);
                        window.onresize = function (event) {
                            var padding = screen.availHeight - window.innerHeight - 20;
                            $('#fullscreen-select-delivery-id_country > .overlay-window').css("padding-bottom", padding);
                            $('#fullscreen-select-delivery-id_state > .overlay-window').css("padding-bottom", padding);
                        };
                    }
                }
                if (changeCountry){
                    $('#delivery-display-id_state').val("");
                }
                $('#delivery-token').val($('#invoice-token').val());
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
        bodyScrollLock.clearAllBodyScrollLocks();
        self.form = $('#shipping-address-form');
        if (self.form.isValid()) {
            var formData = $(self.form).serialize();
            formData=formData+"&shipping[edit]=1";
            self.formData = $('#shipping-address-form :visible').serializeArray();
            self.fullFormData = $('#shipping-address-form').serializeArray();
            if (isGuest) {
                formData=formData+"&shipping_address_id=0";
            } else {
                var fulladdr = objectifyForm(self.fullFormData);
                var id_address = fulladdr.id_address;
                formData=formData+"&shipping_address_id="+id_address;
            }
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + "?ajax=true" + "&saveAddress=delivery" + '&rand=' + new Date().getTime(),
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
                    // self.nextStep(json);
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
                        if (json.updated_section && ('shipping_method' in json.updated_section)) {
                            $('#step3').html(json.updated_section.shipping_method);
                        }
                        var address = objectifyForm(self.formData);
                        var fullAddress = objectifyForm(self.fullFormData);
                        address.id_address = json.id_address_delivery;
                        fullAddress.id_address = json.id_address_delivery;
                        var billinghtml;
                        var shippinghtml;
                        var billingAddrId = $('input[name=selected-billing-address]:checked').val();
                        for(var i = 0; i < json.addresses.length; i++) {
                            if (parseInt(json.id_address_invoice) === parseInt(json.addresses[i].id_address) ){
                                for (const [key, value] of Object.entries(json.addresses[i])) {
                                    if (!address[key]) {
                                        address[key] = json.addresses[i][key];
                                    }
                                }
                            }
                        }
                        if ($('#delivery-address-'+json.id_address_delivery).length > 0) {
                            billinghtml = buildBillingAddressHtml(address, json.id_address_delivery, true, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, json.id_address_delivery, true, fullAddress, editAddressLabel);
                            $('#invoice-address-'+json.id_address_delivery).html(billinghtml);
                            $('#delivery-address-'+json.id_address_delivery).html(shippinghtml);
                        } else {
                            billinghtml = buildBillingAddressHtml(address, json.id_address_delivery, false, fullAddress, editAddressLabel);
                            shippinghtml = buildShippingAddressHtml(address, json.id_address_delivery, false, fullAddress, editAddressLabel);
                            $('#invoice-address-list').append(billinghtml);
                            $('#delivery-address-list').append(shippinghtml);
                        }
                        if(Number(billingAddrId) === Number(json.id_address_delivery)){
                            $('input[name="selected-billing-address"][value="'+json.id_address_delivery+'"]').prop('checked', true);
                        }
                        $('input[name="selected-shipping-address"][value="'+json.id_address_delivery+'"]').prop('checked', true);
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
        formData += "&isSubmit=1";
        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + "?ajax=true" + "&saveAddress=delivery" + '&rand=' + new Date().getTime(),
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
                $('#step3').show();
                $('#step2').hide();
                var navbar = new NavBar(3);
                navbar.setStep();
                var selectedDeliveryAddress = $('#delivery-address-'+id_address);
                selectedDeliveryAddress.parent().prepend(selectedDeliveryAddress);
                if (json.updated_section && ('shipping_method' in json.updated_section)) {
                    $('#step3').html(json.updated_section.shipping_method);
                }
                window.scrollTo(0, 0);
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
        $('#step1').show();
        $('#step2').hide();
        var navbar = new NavBar(1);
        navbar.setStep();
        window.scrollTo(0, 0);
    };

    self.removeTick = function (countries, fieldName, selected) {
        Object.entries(countries).forEach(function (option) {
            if (Number(option[0]) !== Number(selected)){
                $('#delivery-'+fieldName+'-'+option[0]).removeClass("optionSelected");
            }
            else {
                $('#delivery-'+fieldName+'-'+option[0]).addClass("optionSelected");
            }
        });
    };

    self.closeEditShippingAddressForm = function(isLogged){
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
            returnArray[formArray[i]['name']] = formArray[i]['value']?formArray[i]['value']:"";
        }
        return returnArray;
    }
};

var ShippingMethod = function (url) {
    var self = this;
    self.url = url;
    self.form = $('#js-delivery');

    self.next = function (isUpdateAddress) {
        if (self.form.isValid()){
            var formData = self.form.serialize();
            // var isPaymentMethodSelected = false;
            if($('input[name=payment-option]:checked').val()){
                var selectedPayment = $('input[name=payment-option]:checked').val();
                formData += "&selectedPayment=" + selectedPayment;
                // isPaymentMethodSelected = true;
            }
            // if (!isPaymentMethodSelected){
            //     $('#btn-payment-method-next').addClass("disabled");
            // }

            self.isUpdateAddress = isUpdateAddress;
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?ajax=true' + '&method=updateCarrier' + '&rand=' + new Date().getTime(),
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
                    if (json.updated_section && ('payment' in json.updated_section)) {
                        $('#step4').html(json.updated_section.payment);
                        $('input:radio[name="payment-option"]').change(
                            function(){
                                if ($(this).is(':checked')) {
                                    $('#btn-payment-method-next').removeClass("disabled no-arrow");
                                    if($('#btn-payment-method-save').length > 0) {
                                        $('#btn-payment-method-save').removeClass("disabled");
                                    }
                                }
                            });
                        $('input:radio[name="payment-option"]').prop('checked', false);
                    }
                    if (!self.isUpdateAddress) {
                        $('#step4').show();
                        var navbar = new NavBar(4);
                        navbar.setStep();
                        var payment_div_list = $("[id^=payment-option-]");
                        if (payment_div_list.length === 1) {
                            $("[id^=payment-option-]").first().trigger('click');
                        }
                    }
                    $('#step3').hide();
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

    self.triggerChronopost = function() {
        if (typeof CHRONORELAIS_ID === 'undefined') {
            return false;
        }

        $( "body" ).on( "click", ".gm-style img", function(event) {
            event.preventDefault();

            if(!$("#checkout-delivery-step").hasClass("-current")){
                $("#checkout-delivery-step").addClass("-current");
            }

            if(!$("#checkout-delivery-step").hasClass("js-current-step")){
                $("#checkout-delivery-step").addClass("js-current-step");
            }

        });

        // Listener for selection of the ChronoRelais carrier radio button
        $('#js-delivery span.custom-radio > input[type=radio], input[name=id_carrier]').click(function(e) {
            toggleRelaisMap($("#cust_address_clean").val(), $("#cust_codepostal").val(), $("#cust_city").val(), e);

            if (typeof CHRONORELAIS_ID != 'undefined' && parseInt($(this).val()) == CHRONORELAIS_ID) {
                $('html, body').animate({
                    scrollTop: $('#hook-display-after-carrier').offset().top
                }, 1500);
            }
        });


        // move in DOM to prevent compatibility issues with Common Services' modules
        if($("#chronorelais_container").length>0)
        {
            $('#chronorelais_dummy_container').remove();
        } else {
            $('#chronorelais_dummy_container').insertAfter($('#extra_carrier'));
            $('#chronorelais_dummy_container').attr('id', 'chronorelais_container');
        }

        // toggle on load
        toggleRelaisMap($("#cust_address_clean").val(), $("#cust_codepostal").val(), $("#cust_city").val());

        // Listener for CP change
        $('#changeCustCP').on('click', postcodeChangeEvent);
        $("#relais_codePostal").on('keypress keydown keyup', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                e.stopPropagation();
                postcodeChangeEvent();
                return false;
            }
        });


        // Listener for BT select in InfoWindow
        $( "body" ).on( "click", ".btselect", function(e) {
            console.log("je passe");
            btSelect.call(e.target,e);
        });
        /*$('#chronorelais_map').click(function(e) {
            console.log("je clique sur chronorelais_map");
            if( $(e.target).is('.btselect') ){
                console.log("je passe");
                btSelect.call(e.target,e);
            }

        });*/

        // Listener for cart navigation to next step
        $('input[name=processCarrier]').click(function() {
            if ($('input[name=id_carrier]:checked').val()==carrierID && !$("input[name=chronorelaisSelect]:checked").val()) {
                alert($("#errormessage").val());
                $("html, body").animate({scrollTop: $('#relais_txt_cont').offset().top}, "slow");
                //$.scrollTo($('#relais_txt_cont'), 800);
                return false;
            }
        });

        //
        //});
    };

};

var PaymentMethod = function(url) {
    var self = this;
    self.url = url;

    self.next = function (isUpdateAddress) {
        // var validator = new Validation(this.form);
        var formData = $('#payment-method-form').serialize();

        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + "?ajax=true" + "&method=updatePayment" + '&rand=' + new Date().getTime(),
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
                if (json.updated_section && ('review' in json.updated_section)) {
                    $('#step5').html(json.updated_section.review);
                }
                var navbar = new NavBar(5);
                navbar.setStep();
                $('#order-review-edit-header').hide();
                $('#checkout-navbar').show();
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

    $('#term-and-condition-checkbox').click(function() {
        if(this.checked){
            $('#submit-order').removeClass("disabled");
        } else {
            $('#submit-order').addClass("disabled");
        }
    });

    self.setPaymentOption = function (paymentOptions) {
        var selectedPayment = $('input[name=payment-option]:checked').val();
        if(selectedPayment){
            $('#review-payment-method-title').text(paymentOptions[selectedPayment][0]['call_to_action_text']);
        }
    };

    self.submitOrder = function (e) {
        if (!$('#review-delivery-method-title').text() || !$('#review-payment-method-title').text()){
            return;
        }
        var $term_and_condition = $('.term-and-condition:checkbox');
        if ($term_and_condition.length && !$term_and_condition.prop('checked')) {
            var errMessage = "You must agree to the terms of service before continuing.";
            $('#checkout-error-content').html(errMessage);
            $('#checkout-error-dialog').show();
        } else {
            var payment_module_name = $('input:radio[name="payment-option"]:checked').attr('id');
            var form_id = 'payment-form-submit-' + payment_module_name;
            $('#' + form_id).submit();
        }
    };

    self.editBillingAddress = function () {
        $('#step1').show();
        $('#step5').hide();
        window.scrollTo(0, 0);
        var navbar = new NavBar(1);
        navbar.setStep();
    };

    self.editShippingAddress = function () {
        $('#step2').show();
        $('#step5').hide();
        window.scrollTo(0, 0);
        var navbar = new NavBar(2);
        navbar.setStep();
    };

    self.editShippingMethod = function () {
        $('#step3').show();
        $('#step5').hide();
        window.scrollTo(0, 0);
        var navbar = new NavBar(3);
        navbar.setStep();
    };

    self.editPaymentMethod = function (){
        $('#step4').show();
        $('#step5').hide();
        window.scrollTo(0, 0);
        var navbar = new NavBar(4);
        navbar.setStep();
    }
};

var Coupon = function (url) {
    var self = this;
    var vars = {};
    self.url = url;

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
            url: self.url + '?ajax=true' + '&submitDiscount=true' + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            data: formData,
            dataType: 'json',
            beforeSend: function () {
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
                    if (json.updated_section && ('review' in json.updated_section)) {
                        $('#step5').html(json.updated_section.review);
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
            url: self.url + '?ajax=true' + '&deleteDiscount=' + discountId + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (json) {
                if (json.updated_section && ('review' in json.updated_section)) {
                    $('#step5').html(json.updated_section.review);
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
        html = "<div class=\"row\" id=\"invoice-address-{0}\">\n".format(id_address);
    }
    html += "<div class=\"col-10\">\n";
    html += "<h4 class=\"address-name\">{0} {1}</h4>".format(address.firstname, address.lastname);
    html += "<span class=\"address-location\">{0}</span>\n".format(address.address1);
    if (address.address2) {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.address2);
    }
    var country = address['invoice-display-id_country'] ? address['invoice-display-id_country'] : address['delivery-display-id_country'] ? address['delivery-display-id_country'] : "";
    if(["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode?address.postcode:'', address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "", address.city);
    } else if (["United Kingdom", "Vietnam"].includes(country)){
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "", address.postcode?address.postcode:'');
    } else if (["United States", "Australia"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.city, address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "", address.postcode?address.postcode:'');
    } else if (["China", "France", "Israel"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode?address.postcode:'', address.city, address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "");
    } else {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address.postcode?address.postcode:'', address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "");
    }
    html += "<span class=\"address-country\">{0}</span>\n".format(address['invoice-display-id_country'] ? address['invoice-display-id_country'] : address['delivery-display-id_country'] ? address['delivery-display-id_country'] : "");
    if (address.phone) {
        html += "<a href=\"tel:{1}\" class=\"address-mobile\">{0}</a>\n".format(address.phone, address.phone);
    }
    if (address.phone_mobile){
        html += "<div><a href=\"tel:{1}\" class=\"address-mobile\">{0}</a></div>".format(address.phone_mobile, address.phone_mobile);
    }
    html += "<div><a class=\"blue address-edit\" onclick=\"billing.edit({0}, {1})\">".format(escapeHtml(JSON.stringify(fullAddress)), 'false')+editAddressLabel+"</a></div>";
    html += "</div>";
    html += ("<div class=\"col-2 text-center\">\n" +
        "  <label class=\"jm-radio-check mb-0\">\n" +
        "    <input type=\"radio\" name=\"selected-billing-address\" value=\"{0}\">\n" +
        "    <span class=\"radio-check\"></span>\n" +
        "  </label>\n" +
        "</div>").format(id_address);
    if(!isExist){
        html += "</div>";
    }
    return html;
}

function buildShippingAddressHtml(address, id_address, isExist, fullAddress, editAddressLabel){
    var html = "";
    if(!isExist){
        html = "<div class=\"row\" id=\"delivery-address-{0}\">\n".format(id_address);
    }
    html += "<div class=\"col-10\">\n";
    html += "<h4 class=\"address-name\">{0} {1}</h4>".format(address.firstname, address.lastname);
    html += "<span class=\"address-location\">{0}</span>\n".format(address.address1);
    if (address.address2) {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.address2);
    }
    var country = address['invoice-display-id_country'] ? address['invoice-display-id_country'] : address['delivery-display-id_country'] ? address['delivery-display-id_country'] : "";
    if(["Netherlands", "Spain", "Italy", "Germany", "Portugal", "Sweden", "Denmark"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode?address.postcode:'', address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "", address.city);
    } else if (["United Kingdom", "Vietnam"].includes(country)){
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "", address.postcode?address.postcode:'');
    } else if (["United States", "Australia"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.city, address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "", address.postcode?address.postcode:'');
    } else if (["China", "France", "Israel"].includes(country)){
        html += "<span class=\"address-location\">{0} {1} {2}</span>\n".format(address.postcode?address.postcode:'', address.city, address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "");
    } else {
        html += "<span class=\"address-location\">{0}</span>\n".format(address.city);
        html += "<span class=\"address-location\">{0} {1}</span>\n".format(address.postcode?address.postcode:'', address['invoice-display-id_state'] ? address['invoice-display-id_state'] : address['delivery-display-id_state'] ? address['delivery-display-id_state'] : "");
    }    html += "<span class=\"address-country\">{0}</span>\n".format(address['invoice-display-id_country'] ? address['invoice-display-id_country'] : address['delivery-display-id_country'] ? address['delivery-display-id_country'] : "");
    if (address.phone) {
        html += "<a href=\"tel:{1}\" class=\"address-mobile\">{0}</a>\n".format(address.phone, address.phone);
    }
    if (address.phone_mobile){
        html += "<div><a href=\"tel:{1}\" class=\"address-mobile\">{0}</a></div>".format(address.phone_mobile,address.phone_mobile);
    }
    html += "<div><a class=\"blue address-edit\" onclick=\"shipping.edit({0}, {1})\">".format(escapeHtml(JSON.stringify(fullAddress)), 'false')+editAddressLabel+"</a></div>";
    html += "</div>";
    html += ("<div class=\"col-2 text-center\">\n" +
        "  <label class=\"jm-radio-check mb-0\">\n" +
        "    <input type=\"radio\" name=\"selected-shipping-address\" value=\"{0}\" id=\"selected-shipping-address-{0}\">\n".format(id_address) +
        "    <span class=\"radio-check\"></span>\n" +
        "  </label>\n" +
        "</div>").format(id_address);
    if(!isExist){
        html += "</div>";
    }
    return html;
}

var HandelBackButton = function () {
    var self = this;
    self.catchBackButtonEvent = function() {
        jQuery(document).ready(function($) {
            if (window.history && window.history.pushState) {

                window.history.pushState('forward', null, '');

                $(window).on('popstate', function() {
                    $('#cancelConfirmDialog').show();
                    self.catchBackButtonEvent();
                });
            }
        });
    };
};

var scrollFixedElement = function () {
    var isAndroid = /(android)/i.test(navigator.userAgent);

    $('.overlay-window').on('focus', 'input', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var inputTarget = e.target,
            position = $(inputTarget).offset().top,
            position3 = $(inputTarget).parents('.overlay-window').find('.overlay-header').offset().top;
        var positionScroll = position - position3 - 80;

        if (isAndroid){
            setTimeout(function () {
                $('.overlay-window').animate({
                    scrollTop: position - position3 - 80
                }, 500);
            },400);

        }

    });
}

$(document).ready(function () {
    scrollFixedElement();
    $(window).on('resize',function(){
        scrollFixedElement();
    });

});