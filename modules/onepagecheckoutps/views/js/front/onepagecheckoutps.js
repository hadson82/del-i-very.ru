/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
 */

if (typeof console === typeof undefined)
    var console = {log: function(msg){alert(msg)}};

$(function(){
    AppOPC.init();
});

var AppOPC = {
    initialized: false,
    load_offer: true,
    is_valid_all_form: false,
    jqOPC: typeof $jqOPC === typeof undefined ? $ : $jqOPC,
    init: function(){
        AppOPC.initialized = true;

        if (typeof OnePageCheckoutPS !== typeof undefined)
        {
            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE)
                .css({
                    margin: 0,
                    paddingRight: 0
                })
                .addClass('opc_center_column')
                .removeClass('col-sm-push-3');

            Address.launch();
            Fronted.launch();
            if (!OnePageCheckoutPS.REGISTER_CUSTOMER)
            {
                $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).css({width: '100%'});

                Carrier.launch();
                Payment.launch();
                Review.launch();
            }

            $('div#onepagecheckoutps #onepagecheckoutps_step_one input[data-validation*="isBirthDate"], div#onepagecheckoutps #onepagecheckoutps_step_one input[data-validation*="isDate"]').datepicker({
                dateFormat: OnePageCheckoutPS.date_format_language,
                //minDate: 0, cuando necesita des-habilitar dias anteriores al actual
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                yearRange: '-100:+0',
                isRTL: OnePageCheckoutPS.IS_RTL
            });

            //launch validate fields
            if (typeof $.formUtils !== typeof undefined && typeof $.validate !== typeof undefined){
                $.formUtils.loadModules('prestashop.js, security.js', OnePageCheckoutPS.ONEPAGECHECKOUTPS_DIR + 'views/js/lib/form-validator/');
                $.validate({
                    form: 'div#onepagecheckoutps #form_login, div#onepagecheckoutps #form_onepagecheckoutps',
                    language : messageValidate,
                    onError: function () {
                        AppOPC.is_valid_all_form = false;
                    },
                    onSuccess: function () {
                        AppOPC.is_valid_all_form = true;

                        return false;
                    }
                });
            }
        }else{//redirect to checkout if have he option OPC_REDIRECT_DIRECTLY_TO_OPC actived
            $('a.standard-checkout, .cart_navigation .btn.pull-right, .cart_navigation .btnsns.pull-right').attr('href', baseDir + 'index.php?controller=order-opc&checkout=1');
            $('form#voucher').attr('action', baseDir + 'index.php?controller=order-opc');

            var href_delete_voucher = $('a.price_discount_delete').attr('href');
            if (typeof(href_delete_voucher) != 'undefined'){
                href_delete_voucher = href_delete_voucher.split('?');
                $('a.price_discount_delete').attr('href', baseDir + 'index.php?controller=order-opc&' + href_delete_voucher[1]);
            }
        }
    }
}

var Fronted = {
    launch: function(){
        $('div#onepagecheckoutps #opc_show_login').click(function(){
            Fronted.showModal({type:'normal', title:$('#opc_login').attr('title'), title_icon:'fa-user', content:$('#opc_login')});
        });

        $('div#onepagecheckoutps #opc_login #btn_login').click(Fronted.loginCustomer);
        $('div#onepagecheckoutps').on('click', '#btn_continue_shopping', function(){
            var link = $('div#onepagecheckoutps #btn_continue_shopping').attr('data-link');
            if (typeof link === typeof undefined) {
                link = baseDir;
            }
            window.location = link;
        });

        $('div#onepagecheckoutps #opc_login #txt_login_password').keypress(function(e){
            var code = (e.keyCode ? e.keyCode : e.which);

            if (code == 13)
                Fronted.loginCustomer();
       });
    },
    openCMS: function(params){
        var param = $.extend({}, {
            id_cms: ''
        }, params);

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType: 'html',
            action: 'loadCMS',
            id_cms: param.id_cms
        };

        var _json = {
            data: data,
            beforeSend: function() {
                $('div#onepagecheckoutps .loading_big').show();
            },
            success: function(html) {
                if (!$.isEmpty(html)){
                    Fronted.showModal({name: 'cms_modal', content: html});
                }
            },
            complete: function(){
                $('div#onepagecheckoutps .loading_big').hide();
            }
        };
        $.makeRequest(_json);
    },
    showModal: function(params){
        var param = $.extend({}, {
            name: 'opc_modal',
            type: 'normal',
            title: '',
            title_icon: '',
            message: '',
            content: '',
            close: true,
            button_close: false,
            size: '',
            callback: '',
            callback_close: ''
        }, params);

        $('#'+param.name).remove();

        var parent_content = '';
        if (typeof param.content === 'object'){
            parent_content = param.content.parent();
        }

        var $modal = $('<div/>').attr({id:param.name, 'class':'modal fade', role:'dialog'});
        var $modal_dialog = $('<div/>').attr({'class':'modal-dialog ' + param.size});
        var $modal_header = $('<div/>').attr({'class':'modal-header'});
        var $modal_content = $('<div/>').attr({'class':'modal-content'});
        var $modal_body = $('<div/>').attr({'class':'modal-body'});
        var $modal_footer = $('<div/>').attr({'class':'modal-footer'});
        var $modal_button_close = $('<button/>')
                .attr({type:'button', 'class':'close'})
                .click(function(){
                    $('#'+param.name).modal('hide');

                    if (!$.isEmpty(parent_content))
                        param.content.appendTo(parent_content).addClass('hidden');

                    $('body').removeClass('modal-open');

                    if (typeof param.callback_close !== typeof undefined && typeof param.callback_close === 'function')
                        param.callback_close();
                })
                .append('<i class="fa fa-close"></i>');
        var $modal_button_close_footer = $('<button/>')
            .attr({type:'button', 'class':'btn btn-default'})
            .click(function(){
                $('#'+param.name).modal('hide');

                if (!$.isEmpty(parent_content))
                    param.content.appendTo(parent_content).addClass('hidden');

                $('body').removeClass('modal-open');

                if (typeof param.callback_close !== typeof undefined && typeof param.callback_close === 'function')
                    param.callback_close();
            })
            .append('OK');
        var $modal_title = '';

        if (typeof param.message === 'array'){
            var message_html = '';
            $.each(param.message, function(i, message){
                message_html += '- ' + message + '<br/>';
            });
            param.message =  message_html;
        }

        if (param.type == 'error'){
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa fa-times-circle fa-2x" style="color:red"></i>')
                .append(param.message);
        }else if (param.type == 'warning'){
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa fa-warning fa-2x" style="color:orange"></i>')
                .append(param.message);
        }
        else{
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa '+param.title_icon+' fa-1x"></i>')
                .append(param.title);
        }

        $modal_header.append($modal_title);
        $modal_content.append($modal_header);

        if (param.type == 'normal'){
            if (typeof param.content === 'object'){
                param.content.removeClass('hidden').appendTo($modal_body);
            }else{
                $modal_body.append(param.content);
            }

            $modal_content.append($modal_body);

            if (param.button_close){
                $modal_footer.append($modal_button_close_footer);
                $modal_content.append($modal_footer);
            }
        }

        $modal_dialog.append($modal_content);
        $modal.append($modal_dialog);

        $modal.on('hide.bs.modal', function(){
            if (!param.close){
                return false;
            } else {
                if (!$.isEmpty(parent_content))
                    param.content.appendTo(parent_content).addClass('hidden');

                if (typeof param.callback_close !== typeof undefined && typeof param.callback_close === 'function')
                    param.callback_close();
            }
        });

        $('div#onepagecheckoutps').prepend($modal);

        $('#'+param.name)
            .modal('show')
            .css({
                top: $('div#onepagecheckoutps').offset().top - 100,
            });

        if (!$('#'+param.name).hasClass('in'))
            $('#'+param.name).addClass('in').css({display : 'block'});

        $('div#onepagecheckoutps .loading_big').hide();

        if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
            param.callback();

        window.scrollTo(0, $('div#onepagecheckoutps').offset().top);
    },
    loginCustomer: function(){
        var email = $('#opc_login #txt_login_email').val();
        var password = $('#opc_login #txt_login_password').val();

        var data = {
            is_ajax: true,
            action: 'loginCustomer',
            email: email,
            password: password
        };

        //no its use makeRequest because dont work.. error weird.
        $.ajax({
            type: 'POST',
            url: orderOpcUrl + '?rand=' + new Date().getTime(),
            cache: false,
            dataType: 'json',
            data: data,
            beforeSend: function() {
                $('#opc_login #btn_login').attr('disabled', 'true');
                $('#opc_login .loading_small').show();
                $('#opc_login .alert').empty().addClass('hidden');
            },
            success: function(json) {
                if(json.success) {
                    if ($('div#onepagecheckoutps #onepagecheckoutps_step_review_container').length > 0) {
                        window.parent.location.reload();
                    } else {
                        if (parseInt($('.shopping_cart .ajax_cart_quantity').text()) > 0){
                            window.parent.location = orderOpcUrl;
                        } else {
                            window.parent.location = baseDir;
                        }
                    }
                } else {
                    if(json.errors){
                        $('#opc_login .alert').html('&bullet; ' + json.errors.join('<br>&bullet; ')).removeClass('hidden');
                    }
                }
            },
			complete: function(){
				$('#opc_login #btn_login').removeAttr('disabled');
				$('#opc_login .loading_small').hide();
			}
        });
    },
    removeUniform : function (params){
        var param = $.extend({}, {
            'parent_control' : 'div#onepagecheckoutps',
            errors : {}
        }, params);

        if (typeof $.uniform !== 'undefined' && typeof $.uniform.restore !== 'undefined') {
            $.uniform.restore(param.parent_control + ' select');
            $.uniform.restore(param.parent_control + ' input');
            $.uniform.restore(param.parent_control + ' a.button');
            $.uniform.restore(param.parent_control + ' button');
            $.uniform.restore(param.parent_control + ' textarea');
        }

        if (typeof $(param.parent_control + ' select').select_unstyle !== 'undefined') {
            $(param.parent_control + ' select').select_unstyle();
        }
    },
	openWindow: function (url){
		var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
		var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
		window.open(url,'','height=500,width=600,top='+(TopPosition-10)+',left='+LeftPosition+',toolbar=no,directories=no,status=no,menubar=no,modal=yes,scrollbars=yes');
	}
}

var Address = {
    id_customer: 0,
    id_address_delivery: 0,
    id_address_invoice: 0,
    delivery_vat_number: false,
    invoice_vat_number: false,
    launch: function(){
        $('div#onepagecheckoutps #field_customer_id').addClass('hidden');

        $('#btn_save_customer').click(Address.createCustomer);

        //just allow lang with weird characters
        if ($.inArray(OnePageCheckoutPS.LANG_ISO, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0)
            $('#customer_firstname, #customer_lastname').validName();

        $('div#onepagecheckoutps').on('blur', '#customer_email', function(e){
            Address.checkEmailCustomer($(e.currentTarget).val());
        });

        $('div#onepagecheckoutps').on("click", "#div_privacy_policy span.read", function(){
            Fronted.openCMS({id_cms : OnePageCheckoutPS.CONFIGS.OPC_ID_CMS_PRIVACY_POLICY});
        });

        if (OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL || $('div#onepagecheckoutps #onepagecheckoutps_step_two #input_virtual_carrier').length <= 0){
            if ($.inArray(OnePageCheckoutPS.LANG_ISO, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0){
                $('div#onepagecheckoutps #delivery_firstname, div#onepagecheckoutps #delivery_lastname').validName();
                $('div#onepagecheckoutps #delivery_address1, div#onepagecheckoutps #delivery_address2, div#onepagecheckoutps #delivery_city').validAddress();
            }

            if (!OnePageCheckoutPS.IS_LOGGED)
                $('div#onepagecheckoutps #field_delivery_id').addClass('hidden');

			//en el caso que el id_country este desactivado
			if ($('div#onepagecheckoutps select#delivery_id_country').length <= 0)
				Address.updateState({object: 'delivery', id_country: OnePageCheckoutPS.id_country_delivery_default});

            $('div#onepagecheckoutps')
                .on('change', 'select#delivery_id_state', Carrier.getByCountry)
                .on('change', 'select#delivery_id_country', function(event){
                    Address.isNeedDniByCountryId({object: 'delivery'});
                    Address.isNeedPostCodeByCountryId({object: 'delivery'});
                    Address.updateState({object: 'delivery', id_country: $(event.currentTarget).val()});
                    Carrier.getByCountry();
                })
                .on('change', 'select#delivery_id', function(e){
                    if (!$.isEmpty($(e.currentTarget).val()))
                        Address.load({object: 'delivery'});
                    else{
                        Address.clearFormByObject('delivery');
                        //Carrier.update({load_carriers: true});
                    }
                })
                .on('click', 'input#checkbox_create_account_guest', Address.checkGuestAccount)
                .on('click', 'input#checkbox_create_account', Address.checkGuestAccount);

            Address.checkGuestAccount();
            Address.isNeedDniByCountryId({object: 'delivery'});
            Address.isNeedPostCodeByCountryId({object: 'delivery'});

            //$("div#onepagecheckoutps select#delivery_id option[value='" + OnePageCheckoutPS.id_address_delivery + "']").attr('selected', 'selected');
        }

        if(OnePageCheckoutPS.ENABLE_INVOICE_ADDRESS){
            if ($.inArray(OnePageCheckoutPS.LANG_ISO, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0){
                $('div#onepagecheckoutps #invoice_firstname, div#onepagecheckoutps #invoice_lastname').validName();
                $('div#onepagecheckoutps #invoice_address1, div#onepagecheckoutps #invoice_address2, div#onepagecheckoutps #invoice_city').validAddress();
            }

            if (!OnePageCheckoutPS.IS_LOGGED)
                $('div#onepagecheckoutps #field_invoice_id').addClass('hidden');

            if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS) {
                Address.checkNeedInvoice();

                $('div#onepagecheckoutps')
                    .on('click', 'input#checkbox_create_invoice_address', Address.checkNeedInvoice);
            }

			//en el caso que el id_country este desactivado
			if ($('div#onepagecheckoutps select#invoice_id_country').length <= 0)
				Address.updateState({object: 'invoice', id_country: OnePageCheckoutPS.id_country_delivery_default});

            $('div#onepagecheckoutps')
                .on('change', 'select#invoice_id_country', function(event){
                    Address.isNeedDniByCountryId({object: 'invoice'});
                    Address.isNeedPostCodeByCountryId({object: 'invoice'});
                    Address.updateState({object: 'invoice', id_country: $(event.currentTarget).val()});
                    Address.updateAddressInvoice();
                })
                .on('change', 'select#invoice_id', function(e){
                    if (!$.isEmpty($(e.currentTarget).val())) {
                        Address.load({object: 'invoice'});
                    } else {
                        Address.clearFormByObject('invoice');
                    }
                });

			Address.isNeedDniByCountryId({object: 'invoice'});
			Address.isNeedPostCodeByCountryId({object: 'invoice'});
            //Address.updateAddressInvoice({load_review: false});

            //$("#invoice_id option[value='" + OnePageCheckoutPS.id_address_invoice + "']").attr('selected', 'selected');
        }

        Address.load();
    },
    loadAddressesCustomer: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            action: 'loadAddressesCustomer'
        };
        var _json = {
            data: data,
            success: function(json) {
                if(typeof json.addresses !== typeof undefined){
                    $delivery_id = $('div#onepagecheckoutps #delivery_id');
                    $invoice_id = $('div#onepagecheckoutps #invoice_id');

                    if ($delivery_id.length > 0){
                        $delivery_id.find('option:not(:first)').remove();

                        $.each(json.addresses, function(i, address){
                            var $option = $('<option/>')
                                .attr({
                                    value: address.id_address,
                                }).append(address.alias);
                            if (json.id_address_delivery == address.id_address)
                                $option.attr('selected', 'true');
                            $option.appendTo($delivery_id);
                        });

                        if (OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.IS_GUEST) {
                            $('#onepagecheckoutps_step_one #field_delivery_id').parent().hide();
                        }
                    }

                    if ($invoice_id.length > 0){
                        $invoice_id.find('option:not(:first)').remove();

                        $.each(json.addresses, function(i, address){
                            var $option = $('<option/>')
                                .attr({
                                    value: address.id_address,
                                }).append(address.alias);
                            if (json.id_address_invoice == address.id_address)
                                $option.attr('selected', 'true');

                            $option.appendTo($invoice_id);
                        });

                        if (OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.IS_GUEST) {
                            $('#onepagecheckoutps_step_one #field_invoice_id').parent().hide();
                        }
                    }
                }

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                    param.callback();
            }
        };
        $.makeRequest(_json);
    },
    createCustomer: function(){
        //validate fields
        $('div#onepagecheckoutps #form_onepagecheckoutps').submit();

        //privacy policy
        if (AppOPC.is_valid_all_form && OnePageCheckoutPS.ENABLE_PRIVACY_POLICY && !OnePageCheckoutPS.IS_LOGGED && !$('div#onepagecheckoutps #onepagecheckoutps_step_one #privacy_policy').is(':checked')){
            $('div#onepagecheckoutps #onepagecheckoutps_step_one #div_privacy_policy').addClass('alert alert-warning');

            Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

            AppOPC.is_valid_all_form = false;
        }

        if (AppOPC.is_valid_all_form){
            var invoice_id = '';
            var fields = Review.getFields();

            if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && $('div#onepagecheckoutps #checkbox_create_invoice_address').length > 0){
                if ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')){
                    invoice_id = $('#invoice_id').val();
                }
            }else{
                invoice_id = $('#invoice_id').val();
            }

            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                dataType: 'json',
                action: 'createCustomerAjax',
                id_customer : (!$.isEmpty($('#customer_id').val()) ? $('#customer_id').val() : ''),
                id_address_delivery : (!$.isEmpty($('#delivery_id').val()) ? $('#delivery_id').val() : ''),
                'id_address_invoice' : invoice_id,
                'fields_opc' : JSON.stringify(fields)
            };

            data = Review.getFieldsExtra(data);

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function(data) {
                    if (data.isSaved && (!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked'))){
                        $('#customer_id').val(data.id_customer);
                        $('#customer_email, #customer_conf_email, #customer_passwd, #customer_conf_passwd').attr({'disabled': 'true', 'data-validation-optional' : 'true'});

                        $('#div_onepagecheckoutps_login, #field_customer_passwd, #field_customer_conf_passwd, #field_customer_email, #field_customer_conf_email, div#onepagecheckoutps #onepagecheckoutps_step_one_container .account_creation, #field_choice_group_customer').addClass('hidden');
                    }

                    if (data.hasError){
                        Fronted.showModal({type:'error', message : '&bullet; ' + data.errors.join('<br>&bullet; ')});
                    }else{
                        if (parseInt($('.shopping_cart .ajax_cart_quantity').text()) > 0){
                            window.parent.location = orderOpcUrl;
                        } else {
                            window.parent.location = baseDir;
                        }
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();
                }
            };
            $.makeRequest(_json);
        }
    },
    load: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        var loaded = false;
        var delivery_id = $.isEmpty($("#delivery_id").val()) ? 0 : $("#delivery_id").val();
        var invoice_id = $.isEmpty($("#invoice_id").val()) ? 0 : $("#invoice_id").val();

        /*if(window.location.hash) {
            var hash = window.location.hash.substring(1);

            if(hash != ''){
                var hash = $.base64.decode(hash);
                var params = hash.split('&');

                $.each(params, function(i, item){
                    if (typeof item == 'string' && item != ''){
                        var _item = item.split('=');

                        if (_item[0] == 'ad')
                            delivery_id = _item[1];
                        if (_item[0] == 'ai')
                            invoice_id = _item[1];
                    }
                });

                window.location.hash = '';
            }
        }*/

        var callback = function(){
            if (OnePageCheckoutPS.IS_VIRTUAL_CART && !loaded){
                if (OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL){
                    $('div#onepagecheckoutps #delivery_id_country').trigger('change');
                }else{
                    Payment.getByCountry();
                    Review.display();
                }
            }else{
                if ($('div#onepagecheckoutps #delivery_id_country').length > 0 && !OnePageCheckoutPS.IS_LOGGED){
                    $('div#onepagecheckoutps #delivery_id_country').trigger('change');
                } else {
                    if (!OnePageCheckoutPS.IS_VIRTUAL_CART)
                        Carrier.getByCountry();
                }
            }

            Address.loadAutocompleteAddress();
        }

//        if(!$.isEmpty(delivery_id) || !$.isEmpty(invoice_id) || (!OnePageCheckoutPS.ENABLE_INVOICE_ADDRESS && !OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL && OnePageCheckoutPS.IS_VIRTUAL_CART)){
        if(OnePageCheckoutPS.IS_LOGGED &&
            (!OnePageCheckoutPS.IS_VIRTUAL_CART || (OnePageCheckoutPS.IS_VIRTUAL_CART && (OnePageCheckoutPS.ENABLE_INVOICE_ADDRESS || OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL)))
        ){
            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'loadAddress',
                delivery_id: delivery_id,
                invoice_id: invoice_id
            };
            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function(json) {
                    if(!json.hasError && (!$.isEmpty(json.customer.id) || !$.isEmpty(json.address_delivery.id) || !$.isEmpty(json.address_invoice.id))){
                        Address.id_address_delivery = $.isEmpty(json.address_delivery.id) ? 0 : json.address_delivery.id;
                        Address.id_address_invoice = $.isEmpty(json.address_invoice.id) ? 0 : json.address_invoice.id;
                        Address.id_customer = $.isEmpty(json.customer.id) ? 0 : json.customer.id;

                        if ($('div#onepagecheckoutps #delivery_id option').length <= 0)
                            Address.loadAddressesCustomer();

                        var object_load = '.customer, '+(param.object == '' ? '.delivery, .invoice' : '.' + param.object);

                        //load customer, delivery or invoice data
                        $('div#onepagecheckoutps #onepagecheckoutps_step_one').find(object_load).each(function(i, field){
                            var $field = $(field);
                            var name = $field.attr('data-field-name');
                            var default_value = $field.attr('data-default-value');
                            var object = '';

                            if ($field.hasClass('custom_field')){
                                return;
                            }

                            if ($field.hasClass('customer')){
                                var value = json.customer[name];
                                object = 'customer';
                            }else if ($field.hasClass('delivery')){
                                var value = json.address_delivery[name];
                                object = 'delivery';
                            }else if ($field.hasClass('invoice')){
                                var value = json.address_invoice[name];
                                object = 'invoice';
                            }

                            $check_invoice = $('div#onepagecheckoutps input#checkbox_create_invoice_address');
                            if (object == 'invoice' && !OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS && !$check_invoice.is(':checked')){
								$('div#onepagecheckoutps #onepagecheckoutps_step_one #invoice_id').val('');
                                return;
							}

							if (value == '0000-00-00')
                                value = '';

                            if ($field.is(':checkbox')){
                                if (parseInt(value))
                                    $field.attr('checked', 'true');
                                else
                                    $field.removeAttr('checked');
                            }else if ($field.is(':radio')){
                                if ($field.val() == value)
                                    $field.attr('checked', 'true');
                            }else{
                                if (name == 'birthday'){
                                    var date_value = value.split('-');
                                    var date_string = OnePageCheckoutPS.date_format_language.replace('dd', date_value[2]);
                                    date_string = date_string.replace('mm', date_value[1]);
                                    date_string = date_string.replace('yy', date_value[0]);

                                    $field.val(date_string);
                                }else{
                                    $field.val(value);
                                }

                                //do not show values by default on input text
                                if ($field.is(':text'))
                                    if (value == default_value)
                                        $field.val('');
                            }

                            if (name == 'id_country')
                                Address.isNeedDniByCountryId({object: object});

                            if (name == 'id_state')
                                Address.updateState({object: object, id_state_default: value});

                            if (name == 'email'){
                                if ((OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.IS_GUEST) || !OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED){
                                    $field.attr('disabled', 'true');
                                }else{
                                    $('div#onepagecheckoutps #onepagecheckoutps_step_one #customer_conf_email').val($field.val());
                                }
                            }
                        });

                        if (OnePageCheckoutPS.IS_VIRTUAL_CART){
                            Payment.getByCountry();
                            Review.display();

                            loaded = true;
                        }
                    }
                    else{
                        if (!json.errors)
                            Fronted.showModal({type:'error', message : json.errors});
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();

                    callback();
                }
            };
            $.makeRequest(_json);
        } else {
            callback();
        }
    },
    loadAutocompleteAddress: function() {
        if (OnePageCheckoutPS.CONFIGS.OPC_AUTOCOMPLETE_GOOGLE_ADDRESS && typeof google.maps.places !== typeof undefined) {
            if ($('#delivery_address1').length > 0)
            {
                Address.autocomplete_delivery = new google.maps.places.Autocomplete(
                    (document.getElementById('delivery_address1')),
                    {types: ['geocode']}
                );
                google.maps.event.addListener(Address.autocomplete_delivery, 'place_changed', function() {
                    Address.fillInAddress('delivery', Address.autocomplete_delivery);
                });

                /*if ($.isEmpty($('#delivery_address1').val())){
                    $('#field_delivery_id_country, #field_delivery_id_state, #field_delivery_postcode, #field_delivery_city').fadeOut();
                }*/
            }

            if ($('#invoice_address1').length > 0)
            {
                Address.autocomplete_invoice = new google.maps.places.Autocomplete(
                    (document.getElementById('invoice_address1')),
                    {types: ['geocode']}
                );

                google.maps.event.addListener(Address.autocomplete_invoice, 'place_changed', function() {
                    Address.fillInAddress('invoice', Address.autocomplete_invoice);
                });
            }
        }
    },
    fillInAddress: function(address, autocomplete) {
        Address.componentForm = {
            locality: {type: 'long_name', field: address + '_city'},
            administrative_area_level_1: {type: 'select', field: address + '_id_state'},
            administrative_area_level_2: {type: 'select', field: address + '_id_state'},
            administrative_area_level_3: {type: 'select', field: address + '_id_state'},
            country: {type: 'select', field: address + '_id_country'},
            postal_code: {type: 'long_name', field: address + '_postcode'}
        };

        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
        //reset
        $.each(Address.componentForm, function(c, component) {
            if (component.type !== 'select') {
                $('#' + component.field).val('');
            }
        });

        var components = {};
        $.each(place.address_components, function(a, component) {
            if (typeof Address.componentForm[component.types[0]] !== typeof undefined) {
                var field = Address.componentForm[component.types[0]].field;
                var type = Address.componentForm[component.types[0]].type;
                components[component.types[0]] = {
                    field: field,
                    type: type,
                    name: component.types[0],
                    value: (typeof component[type] !== typeof undefined) ? component[type] : component.long_name
                };
            }
        });
        $.each(components, function(c, component) {
            if (component.type === 'select') {
                if (component.name === 'country') {
                    $('#' + address + '_id_country option').prop('selected', false);
                    $('#' + address + '_id_country option[data-text="' + component.value + '"]').prop('selected', true);
                    $('#' + address + '_id_country').trigger('change');
                } else if (typeof $('#' + address + '_id_state')[0] !== typeof undefined && component.name === 'administrative_area_level_1') {
                    Address.callBackState = function() {
                        var state_name = '';
                        if (typeof $('#' + address + '_id_state option[data-text="' + component.value + '"]')[0] !== typeof undefined) {
                            state_name = component.value;
                        } else if (typeof components.administrative_area_level_2 !== typeof undefined && typeof $('#' + address + '_id_state option[data-text="' + components.administrative_area_level_2.value + '"]')[0] !== typeof undefined) {
                            state_name = components.administrative_area_level_2.value;
                        } else if (typeof components.administrative_area_level_3 !== typeof undefined && typeof $('#' + address + '_id_state option[data-text="' + components.administrative_area_level_3.value + '"]')[0] !== typeof undefined) {
                            state_name = components.administrative_area_level_3.value;
                        }

                        $('#' + address + '_id_state option').prop('selected', false);
                        $('#' + address + '_id_state option[data-text="' + state_name + '"]').prop('selected', true);
                    };
                }
            } else {
                $('#' + component.field).val(component.value);
            }

            //$('#field_' + component.field).fadeIn();
        });
        //dispatch inputs events
        $('#' + address + '_city').blur();
        $('#' + address + '_postcode').blur();
    },
    updateAddressInvoice: function(params){
        var param = $.extend({}, {
            callback: '',
            load_review: true
        }, params);

        if (OnePageCheckoutPS.PS_TAX_ADDRESS_TYPE == 'id_address_invoice' || (OnePageCheckoutPS.IS_VIRTUAL_CART && ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked') || OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS))){
            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'updateAddressInvoice',
                dataType: 'html'
            };

            if ($('div#onepagecheckoutps #invoice_id_country').length > 0)
                data['id_country'] = $('div#onepagecheckoutps #invoice_id_country').val();

            if ($('div#onepagecheckoutps #invoice_id_state').length > 0)
                data['id_state'] = $('div#onepagecheckoutps #invoice_id_state').val();

            if ($('div#onepagecheckoutps #invoice_postcode').length > 0)
                data['postcode'] = $('div#onepagecheckoutps #invoice_postcode').val();

            if ($('div#onepagecheckoutps #invoice_city').length > 0)
                data['city'] = $('div#onepagecheckoutps #invoice_city').val();

            if ($('div#onepagecheckoutps #invoice_id').length > 0)
                data['id_address_invoice'] = $('div#onepagecheckoutps #invoice_id').val();

            if ($('div#onepagecheckoutps #invoice_vat_number').length > 0)
                data['vat_number'] = $('div#onepagecheckoutps #invoice_vat_number').val();

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function() {
                    Carrier.getByCountry();
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    },
    geolocate: function(event) {
        $(event.currentTarget).off('focus');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = new google.maps.LatLng(
                position.coords.latitude, position.coords.longitude);
                autocomplete.setBounds(new google.maps.LatLngBounds(geolocation,
                    geolocation));
            });
        }
    },
    updateState: function(params) {
        var param = $.extend({}, {
            object: '',
            id_state_default: '',
            id_country: ''
        }, params);

        var states = null;

        if (!$.isEmpty(param.object)) {
            var $id_country = $('div#onepagecheckoutps select#' + param.object + '_id_country');
            var $id_state = $('div#onepagecheckoutps select#' + param.object + '_id_state');
            var id_country = $.isEmpty(param.id_country) ? $id_country.val() : param.id_country;

			if ($.isEmpty(id_country))
				id_country = OnePageCheckoutPS.id_country_delivery_default;

			var states = countries[id_country];

            //delete states
            $id_state.find('option').remove();

            if (!$.isEmpty(states)) {
                //empty option
                var $option = $('<option/>')
                    .attr({
                        value: '',
                    }).append('--');
                 $option.appendTo($id_state);

                $.each(states, function(i, state) {
                    var $option = $('<option/>')
                        .attr({
                            'data-text': state.name,
                            value: state.id,
                        }).append(state.name);
                    if (param.id_state_default == state.id)
                        $option.attr('selected', 'true');

                    $option.appendTo($id_state);
                });

                $id_state.find('sup').html('*');

                if (typeof Address.callBackState === 'function') {
                    Address.callBackState();
                } else {
                    //auto select state.
                    if ($.isEmpty($id_state.find('option:selected').val())){
                        var default_value = $id_state.attr('data-default-value');
                        if (default_value != '0')
                            $id_state.val(default_value);
                        else
                            $id_state.find(':eq(1)').attr('selected', 'true');
                    }
                }

                $('div#onepagecheckoutps #field_' + param.object + '_id_state').show();
            }
            else {
                $id_state.find('sup').html('');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').hide();
            }
        }
    },
    checkNeedInvoice: function(){
        if ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked') || OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS){
            Address.isNeedDniByCountryId({object: 'invoice'});
            Address.updateState({object: 'invoice'});

            $('div#onepagecheckoutps #invoice_address_container .fields_container div.lock_controls').remove();

            $('div#onepagecheckoutps #invoice_address_container .invoice.required').each(function(i, item){
                $(item).removeAttr('data-validation-optional');
            });
        }else{
            $('div#onepagecheckoutps #invoice_address_container .fields_container').prepend('<div class="lock_controls"></div>');

            $('div#onepagecheckoutps #invoice_address_container .invoice.required').each(function(i, item){
                $(item).attr('data-validation-optional', 'true').trigger('reset');
            });
        }
    },
    checkGuestAccount: function(){
        if (OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED){
            if ($('div#onepagecheckoutps #checkbox_create_account_guest').is(':checked')){
                $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                    .fadeIn()
                    .addClass('required');
                $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('*');
                $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').removeAttr('data-validation-optional').val('');
            }else{
                $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                    .fadeOut()
                    .removeClass('required')
                    .trigger('reset');
                $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('');
                $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').attr('data-validation-optional', 'true');
            }
        }else{
            if (OnePageCheckoutPS.CONFIGS.OPC_REQUEST_PASSWORD && OnePageCheckoutPS.CONFIGS.OPC_OPTION_AUTOGENERATE_PASSWORD){
                if ($('div#onepagecheckoutps #checkbox_create_account').is(':checked')){
                    $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                        .fadeIn()
                        .addClass('required');
                    $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('*');
                    $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').removeAttr('data-validation-optional').val('');
                }else{
                    $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                        .fadeOut()
                        .removeClass('required')
                        .trigger('reset');
                    $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('');
                    $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').attr('data-validation-optional', 'true');
                }
            }
        }
    },
    isNeedDniByCountryId: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        if (!$.isEmpty(param.object)){
            var id_country = $('select#' + param.object + '_id_country').val();

            if (!$.isEmpty(id_country) && typeof countries !== typeof undefined && $('#field_' + param.object + '_dni').length > 0){
                if (countriesNeedIDNumber[id_country]){
                    if ((param.object === 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))
                            || param.object === 'delivery') {
                        $('#field_' + param.object + '_dni').addClass('required').show();
                        $('#field_' + param.object + '_dni sup').html('*');
                        $('#' + param.object + '_dni').removeAttr('data-validation-optional').addClass('required');
                    } else {
                        $('#field_' + param.object + '_dni').removeClass('required').hide();
                        $('#field_' + param.object + '_dni sup').html('');
                        $('#' + param.object + '_dni').attr('data-validation-optional', 'true').removeClass('required');
                    }
                } else {
                    if ($('#' + param.object + '_dni').attr('data-required') == '0'){
                        $('#field_' + param.object + '_dni').removeClass('required');
                        $('#field_' + param.object + '_dni sup').html('');
                        $('#' + param.object + '_dni').attr('data-validation-optional', 'true').removeClass('required');
                    }
                }
            }
        }
    },
	isNeedPostCodeByCountryId: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        if (!$.isEmpty(param.object)){
            var id_country = $('select#' + param.object + '_id_country').val();

            if (!$.isEmpty(id_country) && typeof countries !== typeof undefined && $('#field_' + param.object + '_postcode').length > 0){
                if (!$.isEmpty(countriesNeedZipCode[id_country])){
					$('#field_' + param.object + '_postcode').addClass('required').show();
					$('#field_' + param.object + '_postcode sup').html('*');
					if (param.object === 'delivery' || (param.object === 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')))
						$('#' + param.object + '_postcode').removeAttr('data-validation-optional').addClass('required');
                } else {
                    if ($('#' + param.object + '_postcode').attr('data-required') == '0'){
                        $('#field_' + param.object + '_postcode').removeClass('required');
                        $('#field_' + param.object + '_postcode sup').html('');
                        $('#' + param.object + '_postcode').attr('data-validation-optional', 'true').removeClass('required');
                    }
                }
            }
        }
    },
    checkEmailCustomer: function(email){
        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
			dataType: 'html',
            action: 'checkRegisteredCustomerEmail',
            email: email
        };

        if (!$.isEmpty(email) && $.isEmail(email)){
            var _json = {
                data: data,
                success: function(data) {
                    if (data != 0){
                        var callback = function(){
                            $('#email_check_modal .modal-footer').append('<button type="button" class="btn btn-primary" onclick="$(\'div#onepagecheckoutps button.close\').trigger(\'click\');$(\'div#onepagecheckoutps #opc_show_login\').trigger(\'click\')" style="margin-left: 15px;">'+OnePageCheckoutPS.Msg.login_customer+'</button>');
                        }
                        if (OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED){
                            Fronted.showModal({name: 'email_check_modal', type:'normal', content : OnePageCheckoutPS.Msg.error_registered_email_guest, button_close: true, callback: callback});
                        } else {
                            Fronted.showModal({name: 'email_check_modal', type:'normal', content : OnePageCheckoutPS.Msg.error_registered_email, button_close: true, callback: callback});
                        }
                    }
                }
            };
            $.makeRequest(_json);
        }
    },
    clearFormByObject: function(object){
        $('div#onepagecheckoutps #onepagecheckoutps_step_one').find('.'+object).each(function(i, field){
            $field = $(field);

            if ($field.is(':text')){
                $field.val('');
            }

            if ($field.attr('data-field-name') == 'id_country') {
                $field.val($field.attr('data-default-value')).trigger('change');
            }
        });
    }
}

var Carrier = {
    id_delivery_option_selected : 0,
    launch: function(){
        if (!OnePageCheckoutPS.IS_VIRTUAL_CART){
            $('div#onepagecheckoutps #gift_message').empty();

            $('div#onepagecheckoutps #onepagecheckoutps_step_two_container')
                .on('click', '.delivery_option .delivery_option_logo', function(event){
                    $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio').attr('checked', true).trigger('change');
                })
                .on('click', '.delivery_option .carrier_delay', function(event){
                    if ($(event.currentTarget).find('#selulozenka').length <= 0) {//support module 'ulozenka'
                        $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio').attr('checked', true).trigger('change');
                    }
                })
                .on('click', '.delivery_option .carrier_price', function(event){
                    $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio').attr('checked', true).trigger('change');
                })
                .on('change', '.delivery_option_radio', function (event){
                    if ($('#onepagecheckoutps_step_two_container div.delivery_date').length <= 0){//support with deliverydate
                        $('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_option').removeClass('selected alert alert-info');
                        $(this).parent().parent().parent().addClass('selected alert alert-info');

                        Carrier.update({delivery_option_selected: $(event.currentTarget), load_carriers: true, load_payments: false, load_review: false});
                    }
                })
                //support carrier module: packetery
                .on('change.packetery', 'select[name=pobocka]', function(){
                    if ($('#onepagecheckoutps_step_two_container div.packetery_prestashop_branch_list').length > 0){
                        $('div#onepagecheckoutps #onepagecheckoutps_step_two_container select[name=pobocka]').off('change.packetery');
                        Carrier.update({load_carriers: false, load_payments: true, load_review: true, callback: window.updateCarrierSelectionAndGift});
                    }
                })
                .on('change', '#recyclable', Carrier.update)
                .on('blur', '#gift_message', Carrier.update)
                .on('blur', '#id_planning_delivery_slot', Carrier.update)//support module planningdeliverycarrier
                .on('click', '#gift', function (event){
                    Carrier.update({load_payments : true});

                    if ($(event.currentTarget).is(':checked'))
                        $('div#onepagecheckoutps #gift_div_opc').removeClass('hidden');
                    else
                        $('div#onepagecheckoutps #gift_div_opc').addClass('hidden');
                });
        }
    },
    getByCountry: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if (!OnePageCheckoutPS.IS_VIRTUAL_CART){
            var extra_params = '';
            $.each(document.location.search.substr(1).split('&'),function(c,q){
                if (q != undefined && q != ''){
                    var i = q.split('=');
                    if ($.isArray(i)){
                        extra_params += '&' + i[0].toString();
                        if (i[1].toString() != undefined)
                            extra_params += '=' + i[1].toString();
                    }
                }
            });

            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime() + extra_params,
                is_ajax: true,
                action: 'loadCarrier',
                dataType: 'html'
            };

            if ($('div#onepagecheckoutps #delivery_id_country').length > 0)
                data['id_country'] = $('div#onepagecheckoutps #delivery_id_country').val();

            if ($('div#onepagecheckoutps #delivery_id_state').length > 0)
                data['id_state'] = $('div#onepagecheckoutps #delivery_id_state').val();

            if ($('div#onepagecheckoutps #delivery_postcode').length > 0)
                data['postcode'] = $('div#onepagecheckoutps #delivery_postcode').val();

            if ($('div#onepagecheckoutps #delivery_city').length > 0)
                data['city'] = $('div#onepagecheckoutps #delivery_city').val();

            if ($('div#onepagecheckoutps #delivery_id').length > 0)
                data['id_address_delivery'] = $('div#onepagecheckoutps #delivery_id').val();

            if ($('div#onepagecheckoutps #delivery_vat_number').length > 0)
                data['vat_number'] = $('div#onepagecheckoutps #delivery_vat_number').val();

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').show();

                    //support carrier module "packetery"
                    if (typeof window.prestashopPacketeryInitialized !== typeof undefined)
                        window.prestashopPacketeryInitialized = undefined;
                },
                success: function(html) {
                    if (!$.isEmpty(html)){
                        $('div#onepagecheckoutps #onepagecheckoutps_step_two').html(html);

                        if (typeof id_carrier_selected !== typeof undefined)
                            $('div#onepagecheckoutps .delivery_option_radio[value="'+id_carrier_selected+',"]').attr('checked', true);

                        if ($('div#onepagecheckoutps #gift').is(':checked'))
                            $('div#onepagecheckoutps #gift_div_opc').show();

                        //suppot module deliverydays
                        if($('#deliverydays_day option').length > 1){
                            $('#deliverydays_day option:eq(1)').attr('selected', 'true');
                        }

                        if($('div#onepagecheckoutps #onepagecheckoutps_step_two').find('.alert-warning').length <= 0)
                            Carrier.update({load_payments: true});
                        else{
                            Payment.getByCountry();
                            Review.display();
                        }
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').hide();

                    Fronted.removeUniform();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }else{
            Payment.getByCountry();
            Review.display();
        }
    },
    update: function(params){
        var param = $.extend({}, {
            delivery_option_selected: $('div#onepagecheckoutps .delivery_option_radio:checked'),
            load_carriers: false,
            load_payments: false,
            load_review: true,
            callback: ''
        }, params);

        if (!OnePageCheckoutPS.IS_VIRTUAL_CART){
            var data = {
                url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'updateCarrier',
                recyclable: ($('#recyclable').is(':checked') ? $('#recyclable').val() : ''),
                gift: ($('#gift').is(':checked') ? $('#gift').val() : ''),
                gift_message: (!$.isEmpty($('#gift_message').val()) ? $('#gift_message').val() : '')
            };

            if ($(param.delivery_option_selected).length > 0)
                data[$(param.delivery_option_selected).attr('name')] = $(param.delivery_option_selected).val();

            $('#onepagecheckoutps_step_two input[type="text"]:not(.customer, .delivery, .invoice),#onepagecheckoutps_step_two input[type="hidden"]:not(.customer, .delivery, .invoice), #onepagecheckoutps_step_two select:not(.customer, .delivery, .invoice)').each(function(i, input){
                var name = $(input).attr('name');
                var value = $(input).val();

                if (!$.isEmpty(name))
                    data[name] = value;
            });

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').show();
                },
                success: function(json) {
                    if (json.hasError){
                        Fronted.showModal({type:'error', message : json.errors});
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').hide();

                    if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'carrier' && AppOPC.load_offer ) {
                        AppOPC.load_offer = false;
                        mustCheckOffer = undefined;
                        checkOffer(function() {
                            //Fronted.closeDialog();
                        });
                    }

                    if(param.load_carriers)
                        Carrier.getByCountry();
                    if(param.load_payments)
                        Payment.getByCountry();
                    if(param.load_review)
                        Review.display();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    },
//    displayPopupModule_socolissimo: function(){
//        if ($('#onepagecheckoutps_step_one').hasClass('customer_loaded')){
//            if($('#onepagecheckoutps_step_two div.hook_extracarrier > #soFr').length > 0){
//                $('#onepagecheckoutps_step_two div.hook_extracarrier > #soFr').attr('src',  baseDir+'modules/socolissimo/redirect.php' + serialiseInput(soInputs)).show();
//
//                Fronted.createPopup(true, '', $('#onepagecheckoutps_step_two div.hook_extracarrier > #soFr'), false, false, true, 'Carrier.getByCountry();');
//
//                $('#dialog_opc').css({width: '600px'});
//
//                Fronted.centerDialog(true);
//            }
//        }else{
//            if (confirm(OnePageCheckoutPS.Msg.select_pickup_point)){
//                Address.createCustomer('Carrier.displayPopupModule_socolissimo()');
//            }
//        }
//    },
//    displayPopupModule_mondialrelay: function(){
//        $content_mondialrelay = $('#onepagecheckoutps_step_two .delivery_option > div').find('table').removeAttr('width');
//
//        Fronted.createPopup(true, '', $content_mondialrelay, true, true, true, 'Carrier.getByCountry();');
//
//        Fronted.centerDialog(true);
//    },
    displayPopupModule_correos: function(){
        var $content_correos = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #correos_content').length > 0){
            $content_correos = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #correos_content');
            if (!OnePageCheckoutPS.IS_LOGGED)
                $content_correos.find('#correos_email').val('');

            var callback = function(){
                $('div#onepagecheckoutps #correos_content #oficinas_correos_content').show();
                GetcorreosPoint();
            };
            var callback_close = function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .delivery_option_radio:checked').trigger('change');
                update_recoger();
            };

            Fronted.showModal({name:'opc_correos', content: $content_correos, size: 'modal-lg', button_close: true, callback: callback, callback_close: callback_close});
        }else{
            $content_correos = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #message_no_office_error');

            Fronted.showModal({name:'opc_correos', content: $content_correos});
        }
    },
    displayPopupModule_kiala: function(){
        var $content = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #kialapicker').length > 0){
            $content = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #kialapicker');
            var callback = function(){
                $('div#onepagecheckoutps #kialapicker').show();
            };

            Fronted.showModal({name:'opc_kiala', content: $content, size: 'modal-lg', button_close: true, callback: callback});
        }
    },
//    displayPopupModule_kialasmall: function(){
//        $content = $('#onepagecheckoutps_step_two #kiala');
//
//        if ($('#onepagecheckoutps_step_one').hasClass('customer_loaded')){
//            if($content.length > 0){
//                Fronted.createPopup(true, '', $content, false, false, true, 'Carrier.getByCountry();');
//
//                Fronted.centerDialog(true);
//            }
//        }else{
//            if (confirm(OnePageCheckoutPS.Msg.select_pickup_point)){
//                Address.createCustomer('Carrier.displayPopupModule_kialasmall()');
//            }
//        }
//    },
    displayPopupModule_mycollectionplaces: function(){
        var $content = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #myCollectionPlacesContent').length > 0){
            $content = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #myCollectionPlacesContent');
            var callback = function(){
                $('div#onepagecheckoutps #myCollectionPlacesContent').show();
                eval($('#myCollectionPlacesContent a.button_small').attr('href'));
            };
            var callback_close = function(){
                if (typeof mycollpsaveCarrier != typeof undefined){
                    mycollpsaveCarrier();
                    Carrier.getByCountry();
                }
            };

            Fronted.showModal({name:'opc_mycollectionplaces', content: $content, size: 'modal-lg', button_close: true, callback: callback, callback_close: callback_close});
        }
    },
    /*displayPopupModule_yupick: function(){
        var $content = ''
        if($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #oficinas_yupick_content').length > 0){
            $content = $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #oficinas_yupick_content');
            if (!OnePageCheckoutPS.IS_LOGGED)
                $content.find('#yupick_type_alert_email').val('');

            var callback = function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container #oficinas_yupick_content').show();
                GetYupickPoint();
            };
            var callback_close = function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .delivery_option_radio:checked').trigger('change');
                update_recoger();
            };

            Fronted.showModal({name:'opc_yupick', content: $content, size: 'modal-lg', button_close: true, callback: callback, callback_close: callback_close});
        }
    },*/
    displayPopupModule_nacex: function(){
        var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
    	var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
        var url = baseDir + '/modules/nacex/nxShop.php?host=www.nacex.es&cp=' + $('#delivery_postcode').val() + '&clientes=' + nacex_agcli;

        modalWin(url);
    },
    displayPopupModule_chronopost: function(){
        $content = $('#onepagecheckoutps_step_two #chronorelais_container');

        Fronted.showModal({name:'opc_chronopost', content: $content, size: 'modal-lg', button_close: true, callback_close: function(){Carrier.getByCountry();}});

        toggleRelaisMap(cust_address_clean, cust_codePostal, cust_city);
    }
//    displayPopupModule_indabox: function(){
//        $content = $('#onepagecheckoutps_step_two #indabox');
//
//        Fronted.createPopup(true, '', $content, true, true, true, 'Carrier.getByCountry();');
//
//        Fronted.centerDialog(true);
//    }
}

var Payment = {
    id_payment_selected: '',
    launch: function(){
        $("div#onepagecheckoutps #onepagecheckoutps_step_three")
            .on('click', '.module_payment_container', function(event){
                if (!$(event.target).hasClass('payment_radio'))
                    $(event.currentTarget).find('.payment_radio').attr('checked', true).trigger('change');
            })
            .on("change", "input[name=method_payment]", function(event){
                var $payment_module = $(event.currentTarget);
                var name_module = $payment_module.val();

                $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee').addClass('hidden');

                $.each(payment_modules_fee, function(name_module_fee, payment){
                    if (name_module == name_module_fee){
                         $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee').removeClass('hidden');
                         $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_label').text(payment.label_fee);
                         $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_price').text(payment.fee);
                         $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_total_price_label').text(payment.label_total);
                         $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_total_price').text(payment.total_fee);
                    }
                });

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + cod_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .cod_fee').show();
                }else{
                    $('div#onepagecheckoutps .cod_fee').hide();
                }

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + bnkplus_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .bnkplus_discount').show();
                }else{
                    $('div#onepagecheckoutps .bnkplus_discount').hide();
                }

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + paypal_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .paypal_fee').show();
                }else{
                    $('div#onepagecheckoutps .paypal_fee').hide();
                }

				if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + tpv_id_module_payment + ']').is(':checked')){
                    $('div#onepagecheckoutps .tpv_fee').show();
                }else{
                    $('div#onepagecheckoutps .tpv_fee').hide();
                }

                if ($('div#onepagecheckoutps #payment_method_container input[id^=module_payment_' + sequra_id_module_payment + ']').first().is(':checked')){
                    $('div#onepagecheckoutps .sequra_fee').show();
                }else{
                    $('div#onepagecheckoutps .sequra_fee').hide();
                }

                Payment.id_payment_selected = $(this).attr('id');

                $('div#onepagecheckoutps #onepagecheckoutps_step_three .module_payment_container').removeClass('selected alert alert-info');
                $(this).parent().parent().addClass('selected alert alert-info');
            });
    },
    getByCountry: function(params){
        var param = $.extend({}, {
            callback: '',
            show_loading: true
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if ($('div#onepagecheckoutps #onepagecheckoutps_step_two').find('.alert-warning').length > 0){
            $('div#onepagecheckoutps #onepagecheckoutps_step_three').html('<p class="alert alert-warning col-xs-12">'+OnePageCheckoutPS.Msg.shipping_method_required+'</p>');
            return;
        }

        var id_country = $('div#onepagecheckoutps #delivery_id_country').val();

        var extra_params = '';
        $.each(document.location.search.substr(1).split('&'),function(c,q){
            if (q != undefined && q != ''){
                var i = q.split('=');
                if ($.isArray(i)){
                    extra_params += '&' + i[0].toString();
                    if (i[1].toString() != undefined)
                        extra_params += '=' + i[1].toString();
                }
            }
        });

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime() + extra_params,
            is_ajax: true,
            dataType: 'html',
            action: 'loadPayment',
            id_country: id_country
        };

        var _json = {
            data: data,
            beforeSend: function() {
                if(param.show_loading)
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three_container .loading_small').show();
            },
            success: function(html) {
                $('div#onepagecheckoutps #onepagecheckoutps_forms').html('');
                $('div#onepagecheckoutps #onepagecheckoutps_step_three').html(html);

                if (!$.isEmpty(Payment.id_payment_selected)){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container #' + Payment.id_payment_selected).trigger('click');
                } else if (!$.isEmpty(OnePageCheckoutPS.CONFIGS.OPC_DEFAULT_PAYMENT_METHOD)){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container [value="'+ OnePageCheckoutPS.CONFIGS.OPC_DEFAULT_PAYMENT_METHOD + '"]').trigger('click');
                }

                //support module paypalpro
                if(typeof $('#pppro_form') !== typeof undefined && !OnePageCheckoutPS.IS_LOGGED)
                    $('#pppro_form #pppro_cc_fname, #pppro_form #pppro_cc_lname').val('');
            },
            complete: function(){
                if(param.show_loading)
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three_container .loading_small').hide();

                Fronted.removeUniform();

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                    param.callback();
            }
        };
        $.makeRequest(_json);
    },
    change: function(){
        if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'payment_method') ) {
//            Payment.validateSelected();
        } else {
            AppOPC.load_offer = false;
            checkOffer(function() {
//                Payment.validateSelected();
            });
        }
    }
}

var Review = {
    launch: function(){
        $("#onepagecheckoutps_step_review")
            .on("click", "#div_cgv span.read", function(){
                Fronted.openCMS({id_cms : OnePageCheckoutPS.CONFIGS.OPC_ID_CMS_TEMRS_CONDITIONS});
            })
            .on("click", ".voucher_name", Review.addVoucher)
            .on("click", "#submitAddDiscount", Review.processDiscount)
            .on("click", "#btn_place_order", function(){
                if (parseInt(OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO) && $('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length <= 0) {
                    window.scrollTo(0, $('#onepagecheckoutps').offset().top);
                    $('#onepagecheckoutps_step_three').addClass('alert alert-warning');
                    return false;
                }else{
                    Review.placeOrder();
                }
            })
            .on("change", '#cgv', function(e) {
                if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'terms' && AppOPC.load_offer ) {
                    if ( $(e.target).is(':checked') ) {
                        if ( !offerApplied ) {
                            AppOPC.load_offer = false;
                            checkOffer(function() {
                                $(e.target).unbind('change');
                                //Fronted.closeDialog();
                            });
                        }
                    }
                }
            })
            .on("click", "#payment_paypal_express_checkout", function(){
                $('#paypal_payment_form').submit();
            });
    },
    display: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if (OnePageCheckoutPS.ENABLE_TERMS_CONDITIONS)
            var cgv = $('#cgv').is(':checked');

        var id_country = !$.isEmpty($('#delivery_id_country').val()) ? $('#delivery_id_country').val() : '';
        var id_state = !$.isEmpty($('#delivery_id_state').val()) ? $('#delivery_id_state').val() : '';

        var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType: 'html',
            action: 'loadReview',
            id_country: id_country,
            id_state: id_state
        };

        var _json = {
            data: data,
            beforeSend: function() {
                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();
            },
            success: function(html) {
                $("div#onepagecheckoutps #onepagecheckoutps_step_review").html(html);

//                $('div#onepagecheckoutps .cart_quantity_input').typeWatch({
//                    highlight: true,
//                    wait: 600,
//                    captureLength: 0,
//                    callback: function(val) { _updateQty(val, true, this.el); }
//                });

                if (OnePageCheckoutPS.ENABLE_TERMS_CONDITIONS && cgv)
                    $('div#onepagecheckoutps #cgv').attr('checked', 'true');

                $('div#onepagecheckoutps input[name="method_payment"]:checked').trigger('change');
            },
            complete: function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').hide();

                //if no exist carriers, do not show cost shipping
                if ($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container p.alert-warning').length > 0){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_review_container #total_shipping').html(OnePageCheckoutPS.Msg.to_determinate);
                }

                //remove express checkout paypal on review
                $('#container_express_checkout').remove();

                if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT) {
                    //image zoom on product list.
                    $('div#onepagecheckoutps #order-detail-content .cart_item a > img').mouseenter(function(event){
                        $('div#onepagecheckoutps #order-detail-content .image_zoom').hide();
                        $(event.currentTarget).parents('.image_product').find('.image_zoom').show();
                    });
                    $('div#onepagecheckoutps #order-detail-content .image_zoom').click(function(event){
                        $(event.currentTarget).toggle();
                    });
                    $('div#onepagecheckoutps #order-detail-content .image_zoom').hover(function(event){
                        $(event.currentTarget).show();
                    }, function(event){
                        $(event.currentTarget).hide();
                    });
                }

                var intervalLoadJavaScriptReview = setInterval(
                    function() {
                        loadJavaScriptReview();
                        clearInterval(intervalLoadJavaScriptReview);
                    }
                    , (typeof csoc_prefix !== 'undefined' ? 5001 : 0));

                //last minute opc
                if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'init' && AppOPC.load_offer ) {
                    AppOPC.load_offer = false;
                    mustCheckOffer = undefined;

                    setTimeout(checkOffer, time_load_offer * 1000);
                }

                if(typeof ajaxCart !== typeof undefined && typeof ajaxCart.refresh !== typeof undefined){
                    //corrige el problema de productos duplicados en el carrito del top.
                    $('#header #cart_block_list .products dt, #header .cart_block_list .products dt').remove();

                    ajaxCart.refresh();
                }

                if (OnePageCheckoutPS.CONFIGS.OPC_CONFIRMATION_BUTTON_FLOAT && !OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO){
                    var $container_float_review = $("div#onepagecheckoutps div#onepagecheckoutps_step_review #container_float_review");
                    var $container_float_review_point = $("div#onepagecheckoutps div#onepagecheckoutps_step_review #container_float_review_point");

                    $(window).scroll(function() {
                        if (!$container_float_review_point.visible()) {
                            if ($container_float_review_point.offset().top > $(window).scrollTop()){
                                $container_float_review.addClass('stick_buttons_footer').css({width : $('#onepagecheckoutps_step_review').outerWidth()});
                            }
                        } else {
                            $container_float_review.removeClass('stick_buttons_footer');
                        }
                    });

                    $(window).resize(function(){
                        $(window).trigger('scroll');
                    });
                    $(window).trigger('scroll');
                }

                Fronted.removeUniform();

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                    param.callback();
            }
        };
        $.makeRequest(_json);
    },
    addVoucher: function(event) {
        var code = $(event.currentTarget).attr('data-code');
        $('#discount_name').val(code);
        Review.processDiscount();
    },
    processDiscount: function(params) {
        var p = $.extend({}, {
            id_discount: null,
            action: 'add'
        }, params);

        if($.isEmpty(p.action)) return;

        if(p.action != 'delete'){
            if($.isEmpty($('#discount_name').val()))
                return;
        }

		var data = {
            url_call: orderOpcUrl + '?rand=' + new Date().getTime(),
            is_ajax: true,
            action: 'processDiscount',
			action_discount: p.action,
			discount_name: $('#discount_name').val(),
			id_discount: p.id_discount
        };

		var _json = {
            data: data,
            success: function(json) {
				if (json.hasError){
                    Fronted.showModal({type:'error', message : '&bullet; ' + json.errors.join('<br>&bullet; ')});
                }else{
                    if ($('#onepagecheckoutps_step_two #input_virtual_carrier').length > 0){
                        Payment.getByCountry();
                        Review.display();
                    }else{
                        Carrier.getByCountry();
                    }
                }
            }
        };
        $.makeRequest(_json);
    },
    getFields: function(){
        var fields = Array();

        $('div#onepagecheckoutps div#onepagecheckoutps_step_one .customer, \n\
            div#onepagecheckoutps div#onepagecheckoutps_step_one .delivery, \n\
            div#onepagecheckoutps div#onepagecheckoutps_step_one .invoice')
        .each(function(i, field){
            if ($(field).is('span'))
                return true;

            var name = $(field).attr('data-field-name');
            var value = '';
            var object = '';

            if ($.isEmpty(name))
                return true;

            if ($(field).hasClass('customer')){
                object = 'customer';
            }else if ($(field).hasClass('delivery')){
                object = 'delivery';
            }else if ($(field).hasClass('invoice')){
                object = 'invoice';
            }

            if(object == 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').length >= 0)
                if (!$('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))
                    return true;

            if (!$.isEmpty(object)){
                if ($(field).is(':checkbox')){
                    value = $(field).is(':checked') ? 1 : 0;
                }else if ($(field).is(':radio')){
                    var tmp_value = $('input[name="' + name + '"]:checked').val();
                    if (typeof tmp_value !== typeof undefined)
                        value = tmp_value;
                }else{
                    value = $(field).val();

                    if (value === null)
                        value = '';
                }

                if ($.strpos(value, '\\')){
                    value = addslashes(value);
                }

                if (!$.isEmpty(value) && typeof value == 'string'){
                    value = value.replace(/\"/g, '\'');
                }

                fields.push({'object' : object, 'name' : name, 'value' : value});
            }
        });

        return fields;
    },
    getFieldsExtra: function(_data){
        $('div#onepagecheckoutps #form_onepagecheckoutps input[type="text"]:not(.customer, .delivery, .invoice), div#onepagecheckoutps #form_onepagecheckoutps input[type="hidden"]:not(.customer, .delivery, .invoice), div#onepagecheckoutps #form_onepagecheckoutps select:not(.customer, .delivery, .invoice)').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).val();

            //compatibilidad modulo eydatepicker
            if (name == 'shipping_date_raw')
                name = 'shipping_date';

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        $('div#onepagecheckoutps #form_onepagecheckoutps input[type="checkbox"]:not(.customer, .delivery, .invoice)').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).is(':checked') ? $(input).val() : '';

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        $('div#onepagecheckoutps #form_onepagecheckoutps input[type="radio"]:not(.customer, .delivery, .invoice):checked').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).val();

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        return _data;
    },
    placeOrder: function(params){
        var param = $.extend({}, {
            validate_payment: true,
            position_element: null
        }, params);

        Fronted.removeUniform();

        if($('#deliverydays_day option').length > 0){
            if($.isEmpty($('#deliverydays_day').val())){
                alert(OnePageCheckoutPS.Msg.select_date_shipping);

                return false;
            }
        }

        if($('#shipping_date').length > 0){
            if($.isEmpty($('#shipping_date').val())){
                alert(OnePageCheckoutPS.Msg.select_date_shipping);
                return false;
            }
        }

        /* support planningdeliverybycarrier */
        if($('#day_slots #date_delivery').length > 0){
            if($.isEmpty($('#day_slots #date_delivery').val())){
                alert(OnePageCheckoutPS.Msg.select_date_shipping);
                return false;
            }
        }
        if($('#day_slots #id_planning_delivery_slot').length > 0){
            if($('#day_slots #id_planning_delivery_slot').val() == '-'){
                alert(OnePageCheckoutPS.Msg.select_date_shipping);
                return false;
            }
        }

        if ($('#onepagecheckoutps_step_two label[for=' + $('.delivery_option_radio:checked').attr('id') + '] div.extra_info_carrier a.select_pickup_point').length > 0){
            alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

            $('#onepagecheckoutps_step_two label[for=' + $('.delivery_option_radio:checked').attr('id') + '] div.extra_info_carrier a.select_pickup_point').trigger('click');

            return false;
        }

        if ($('#onepagecheckoutps_step_two .delivery_option #correos_popuplinkcontentpaq .selected_paq').length > 0 &&
            $('#onepagecheckoutps_step_two .delivery_option #correos_popuplinkcontentpaq .selected_paq').html() == '')
        {
            alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

            return false;
        }

        if ($('#onepagecheckoutps_step_two .packetery-branch-list select').length > 0 &&
            $('#onepagecheckoutps_step_two .packetery-branch-list select').val() == '' &&
            Boolean($('.delivery_option_radio:checked').attr('packetery-initialized')))
        {
            alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

            return false;
        }

        //support module: deliverydateswizard
        if (typeof ddw !== typeof undefined) {
            var ddw_error = false;

            if ($("input[name='chk_timeslot']").length > 0 &&  $("input[name='chk_timeslot']").is(":checked") == false)
                ddw_error = true;

            if (ddw.$input_ddw_order_date.val() == '0000-00-00 00:00:00' || ddw.$input_ddw_order_date.val() == '')
                ddw_error = true;

            if (ddw_error && ddw.required == 1){
                ddw.showRequiredError();

                return false;
            }
        }

        /*if (typeof checkoutFields !== typeof undefined) {
            var return_checkout_fields = false;

            checkoutFields.init();

            if (typeof($(this).attr('name')) != 'undefined') {
                return_checkout_fields = checkoutFields.checkRequiredFields($(this).attr('name'));
            } else if (typeof($(this).attr('id')) != 'undefined') {
                return_checkout_fields = checkoutFields.checkRequiredFields($(this).attr('id'));
            } else if (typeof($(this).parents('#HOOK_PAYMENT')) != 'undefined') {
                return_checkout_fields = checkoutFields.checkRequiredFields('opc_payment_methods');
            } else {
                return_checkout_fields = checkoutFields.checkRequiredFields('order-detail-content');
            }

            if (!return_checkout_fields) {
                return false;
            }
        }*/

        $('div#onepagecheckoutps #btn_place_order').attr('disabled', 'true');

        //return fields if the validation is ok
        var fields = Review.validateAllForm({validate_payment:param.validate_payment});

        if(fields && AppOPC.is_valid_all_form){
            var invoice_id = '';

            if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && $('div#onepagecheckoutps #checkbox_create_invoice_address').length > 0){
                if ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')){
                    invoice_id = $('#invoice_id').val();
                }
            }else{
                invoice_id = $('#invoice_id').val();
            }

            var _data = {
				'url_call'				: orderOpcUrl + '?rand=' + new Date().getTime(),
                'is_ajax'               : true,
                'action'                : 'placeOrder',
                'id_customer'           : (!$.isEmpty($('#customer_id').val()) ? $('#customer_id').val() : ''),
                'id_address_delivery'   : (!$.isEmpty($('#delivery_id').val()) ? $('#delivery_id').val() : ''),
                'id_address_invoice'    : invoice_id,
                'fields_opc'            : JSON.stringify(fields),
                'message'               : (!$.isEmpty($('#message').val()) ? $('#message').val() : ''),
                'is_new_customer'       : ($('#checkbox_create_account_guest').is(':checked') ? 0 : 1),
                'token'                 : static_token
            };

            _data = Review.getFieldsExtra(_data);

			var _json = {
				data: _data,
				beforeSend: function() {
					$('div#onepagecheckoutps .loading_big').show();
				},
				success: function(data) {
					if (data.isSaved && (!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked'))){
                        $('#customer_id').val(data.id_customer);
                        $('#customer_email, #customer_conf_email, #customer_passwd, #customer_conf_passwd').attr({'disabled': 'true', 'data-validation-optional' : 'true'}).addClass('disabled').trigger('reset');

                        $('#div_onepagecheckoutps_login, #field_customer_passwd, #field_customer_conf_passwd, div#onepagecheckoutps #onepagecheckoutps_step_one_container .account_creation, #field_choice_group_customer, #field_customer_checkbox_create_account').addClass('hidden');
                    }

                    if (data.hasError){
                        Fronted.showModal({type:'error', message : '&bullet; ' + data.errors.join('<br>&bullet; ')});
                    }else{
                        var callback_load_address = function(){
                            if(!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked')){
                                $('div#onepagecheckoutps #field_delivery_id, div#onepagecheckoutps #field_invoice_id').removeClass('hidden');
                                $('div#onepagecheckoutps #field_customer_checkbox_create_account_guest').addClass('hidden');
                            }

                            //plugin last minute offer
                            if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'confirm') ) {
                                window['checkOffer'] = function(callback) {
                                    callback();
                                };
                            }

                            if($('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length > 0){
                                confirmFreeOrder();
                                return;
                            }

                            //support module payment: Pay
                            if (!$.isEmpty($('#securepay_cardNo').val()) &&
                                !$.isEmpty($('#securepay_cardSecurityCode').val()) &&
                                !$.isEmpty($('#securepay_cardExpireMonth').val()) &&
                                !$.isEmpty($('#securepay_cardExpireYear').val()))
                            {
                                CardpaySubmit();
                                return;
                            }

                            var callback_placeorder = '';
                            if(param.validate_payment === true){
                                var callback_placeorder = function(){
                                    var radio_method_payment = $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container #' + Payment.id_payment_selected + ':checked');
                                    var input_url_method_payment = $(radio_method_payment).next();

                                    var id_payment = $(radio_method_payment).attr('id').split('_')[2];
                                    var name_payment = $(radio_method_payment).val();
                                    var url_payment = $(input_url_method_payment).val();
                                    var onclick_payment = $(input_url_method_payment).length > 0 ? $(input_url_method_payment).get(0).getAttribute("onclick") : '';

                                    if ($.isEmpty(id_payment) && $.isEmpty(url_payment) && $.isEmpty(onclick_payment))
                                        return;

                                    if(!$.isEmpty(onclick_payment)){
                                        if (name_payment == 'klikandpay' || name_payment == 'banesto'){

                                            if(!eval(onclick_payment))
                                                return;
                                        }
                                    }

                                    checkOffer(function() {
                                        if (url_payment == 'iframe'){//compatibilidad con paypal integral.
                                            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).html($('#iframe_payment_module_' + id_payment).val());

                                            $('#dialog_opc > .ui-dialog-content').empty();
                                            $('body > .ui-widget-overlay').remove();
                                        }
                                        else if (!$.isUrl(url_payment)){
    //                                        window.location.hash = '#' + 'ad=' + data.id_address_delivery + '&ai=' + data.id_address_invoice;

                                            //compatibilidad con modulo bbva
                                            if (name_payment == 'bbva'){
                                                var peticion_bbva = $('#bbva_form input').val();
                                                var _tmp = peticion_bbva.split('key=');
                                                var old_secure_key = _tmp[1].substr(0, 32);
                                                peticion_bbva = peticion_bbva.replace(old_secure_key, data.secure_key);

                                                $('#bbva_form input').val(peticion_bbva);
                                            }

                                            eval(url_payment);

                                            if (name_payment == 'stripejs' && typeof stripeResponseHandler == typeof undefined){
                                                var _script = document.createElement('script');
                                                _script.type = 'text/javascript';
                                                _script.src = baseDir + 'modules/stripejs/views/js/stripe-prestashop.js';
                                                $("body").append(_script);
                                            }

                                            return false;
                                        }else{
                                            //evita problema de cache en IE
                                            /*if ($.strpos(url_payment, '?'))
                                                url_payment += '&_=' + (new Date()).getTime();
                                            else
                                                url_payment += '?_=' + (new Date()).getTime();*/

                                            //redireccion automatica a la pagina del modulo, ya que por su forma de construccion no es posible mostrarlo en un iframe
                                            var arr_payments_without_popup = payments_without_popup.split(',');
                                            if ($.inArray(name_payment, arr_payments_without_popup) != -1 || $.strpos(url_payment, '?pm='+name_payment)){
    //                                            window.location.hash = '#' + 'ad=' + data.id_address_delivery + '&ai=' + data.id_address_invoice;

                                                window.location = url_payment;

                                                return false;
                                            }

                                            _callbackCheckout = function() {
                                                AppOPC.jqOPC('<div/>').load(url_payment, function(){
                                                    //redirecciona a metodos de pagos que se realicen por fuera de la tienda.
                                                    var $that = $(this).find(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE);

                                                    if (!OnePageCheckoutPS.OPC_SHOW_POPUP_PAYMENT){
                                                        if (($that.find('input[type=submit]').length == 1 || $that.find('button[type=submit]').length == 1)){
                                                            $that.hide().appendTo('body');
                                                            $that.find('input[type=submit]').attr('onclick','').trigger('click');
                                                            $that.find('button[type=submit]').attr('onclick','').trigger('click');

                                                            return false;
                                                        }
                                                    }

                                                    //limpiamos el html devuelto por el metodo de pago para no colocar basura
                                                    $that.find('h2, h1').first().remove();
                                                    $that.find('.breadcrumb').remove();
                                                    $that.find('#order_step').remove();
                                                    $that.find('#currency_payment').hide(); //remueve el select de las divisas que hace recargar la web.
                                                    $that.find(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).attr('style', 'width: auto!important');
                                                    $that.find('button[type="submit"]').removeAttr('class').addClass('button btn btn-primary');

                                                    $.each($that.find('a'), function(i, a){
                                                       if ($.strpos($(a).attr('href'), 'step=3'))
                                                        $(a).remove();
                                                    });

                                                    /*$that.find('input[type="submit"], button[type="submit"]').click(function(){
                                                        $('div#onepagecheckoutps #payment_modal').hide();
                                                        $('div#onepagecheckoutps .loading_big').show();
                                                    });*/

                                                    //elimina esta clase del input del carrito, para que salgan de nuevo los popup para seleccionar punto de envio.
                                                    $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container .module_carrier').each(function(i, carrier){
                                                        var id_delivery_option_selected = $(carrier).val();
                                                        $('#' + id_delivery_option_selected).removeClass('point_selected');
                                                    });

                                                    Fronted.showModal({name: 'payment_modal', type:'normal', title: OnePageCheckoutPS.Msg.confirm_payment_method, title_icon: 'fa-credit-card', content : $that, close : true});
                                                });
                                            };

                                            if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'confirm') ) {
                                                _callbackCheckout();
                                            }
                                        }
                                    });
                                }
                            }else{
                                var callback_placeorder = function(){
                                    $.each(events_payments, function(k, items){
                                        if (param.position_element.item_parent == k){
                                            $.each(items, function(i, item){
                                                if (param.position_element.item_child == i){
                                                    $(item.element).attr('onclick', '').unbind('click');

                                                    if (typeof item.event !== typeof undefined){
                                                        $(item.element).click(item.event);
                                                    }

                                                    if (typeof item.onclick !== typeof undefined)
                                                    {
                                                        $(item.element).attr('onclick', item.onclick);
                                                    }

                                                    $('div#onepagecheckoutps #onepagecheckoutps_step_three form').on('submit', function(event) {
                                                        if (item.module_name == 'npaypalpro'){
                                                            return;
                                                        }
                                                        if (item.module_name == 'braintreejs'){
                                                            if (typeof validate3DSForm !== typeof undefined && validate3DSForm(event)){
                                                                $(event.target)[0].submit();
                                                            }
                                                            if (typeof validateBTForm !== typeof undefined && validateBTForm(event)){
                                                                $(event.target)[0].submit();
                                                            }
                                                            return;
                                                        }

                                                        $(event.target)[0].submit();
                                                        event.preventDefault();
                                                        event.stopPropagation();
                                                    });

                                                    if ($(item.element).is('a, span')){
                                                        $(item.element)[0].click();
                                                    }else{
                                                        $(item.element).click();
                                                    }

                                                    $('div#onepagecheckoutps .loading_big').hide();
                                                }
                                            });
                                        }
                                    });
                                }
                            }

                            //recarga de nuevo los metodos de pago para actualizar los formularios que tengan datos del cliente por defecto.
                            if (!OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.PAYMENTS_WITHOUT_RADIO === false)
                            {
                                Payment.getByCountry({show_loading: false, callback: callback_placeorder});
                            }
                            else {
                                if (typeof callback_placeorder === 'function')
                                    callback_placeorder();
                            }

                        }

                        //recarga las listas de las direcciones
                        Address.loadAddressesCustomer({callback: callback_load_address});
                    }
				},
				error: function(data){
					alert(data);
					$('div#onepagecheckoutps .loading_big').hide();
				}
			};
			$.makeRequest(_json);
        }
    },
    validateAllForm: function(params){
        var param = $.extend({}, {
            validate_payment: true
        }, params);

        //validate fields
        $('div#onepagecheckoutps #form_onepagecheckoutps').submit();

        if (AppOPC.is_valid_all_form){
            $('div#onepagecheckoutps #onepagecheckoutps_step_two').removeClass('alert alert-danger');
            $('div#onepagecheckoutps #onepagecheckoutps_step_three').removeClass('alert alert-warning');
            $('div#onepagecheckoutps #onepagecheckoutps_step_review #div_cgv').removeClass('alert alert-warning');
            $('div#onepagecheckoutps #onepagecheckoutps_step_one #div_privacy_policy').removeClass('alert alert-warning');

            //validate shipping
            if ($('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_options_address').length >= 0 && !OnePageCheckoutPS.IS_VIRTUAL_CART){
                var id_carrier = $('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_option_radio:checked').val();

                if (!$.isEmpty(id_carrier)){
                    Carrier.id_delivery_option_selected = id_carrier;

                    AppOPC.is_valid_all_form = true;
                }else{
                    Carrier.id_delivery_option_selected = null;
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container').addClass('alert alert-warning');

                    Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.shipping_method_required});

                    AppOPC.is_valid_all_form = false;
                }
            }

            //validate payments
            if(AppOPC.is_valid_all_form && param.validate_payment === true){
                if ($('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length <= 0){
                    var payment = $('div#onepagecheckoutps #onepagecheckoutps_step_three input[name="method_payment"]:checked');

                    if (payment.length > 0){
                        Payment.id_payment_selected = $(payment).attr('id');

                        AppOPC.is_valid_all_form = true;
                    }else{
                        Payment.id_payment_selected = '';

						//support module payment: Pay
						if (!$.isEmpty($('#securepay_cardNo').val()) &&
							!$.isEmpty($('#securepay_cardSecurityCode').val()) &&
							!$.isEmpty($('#securepay_cardExpireMonth').val()) &&
							!$.isEmpty($('#securepay_cardExpireYear').val()))
						{
							AppOPC.is_valid_all_form = true;
						}
						else
						{
							$('div#onepagecheckoutps #onepagecheckoutps_step_three').addClass('alert alert-warning');

							Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.payment_method_required});

							AppOPC.is_valid_all_form = false;
						}
                    }
                }
            }

            //terms conditions
            if (AppOPC.is_valid_all_form && OnePageCheckoutPS.ENABLE_TERMS_CONDITIONS && !$('div#onepagecheckoutps #onepagecheckoutps_step_review #cgv').is(':checked')){
                $('div#onepagecheckoutps #onepagecheckoutps_step_review #div_cgv').addClass('alert alert-warning');

                Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_terms_and_conditions});

                AppOPC.is_valid_all_form = false;
            }


            //privacy policy
            if (AppOPC.is_valid_all_form && OnePageCheckoutPS.ENABLE_PRIVACY_POLICY && !OnePageCheckoutPS.IS_LOGGED && !$('div#onepagecheckoutps #onepagecheckoutps_step_one #privacy_policy').is(':checked')){
                $('div#onepagecheckoutps #onepagecheckoutps_step_one #div_privacy_policy').addClass('alert alert-warning');

                Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

                AppOPC.is_valid_all_form = false;
            }

            //if all is rigth, then get all fields
            if (AppOPC.is_valid_all_form){
                $('div#onepagecheckoutps #btn_place_order').removeAttr('disabled');

                return Review.getFields();
            }
        }else{
            Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.fields_required_to_process_order + '\n' + OnePageCheckoutPS.Msg.check_fields_highlighted});
        }

        $('div#onepagecheckoutps #btn_place_order').removeAttr('disabled');

        return false;
    }
}

function updateExtraCarrier(id_delivery_option, id_address)
{
	$.ajax({
		type: 'POST',
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		cache: false,
		dataType : "json",
		data: 'is_ajax=true'
			+'&action=updateExtraCarrier'
			+'&id_address='+id_address
			+'&id_delivery_option='+id_delivery_option
			+'&token='+static_token
			+'&allow_refresh=1',
		success: function(jsonData)
		{
			$('#HOOK_EXTRACARRIER_'+id_address).html(jsonData['content']);
		}
	});
}

function confirmFreeOrder()
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		cache: false,
		dataType : "html",
		data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
		success: function(html)
		{
			$('#btn_place_order').removeClass('disabled');
			var array_split = html.split(':');
			if (array_split[0] == 'freeorder')
			{
				if (!$('#checkbox_create_account_guest').is(':checked') && !OnePageCheckoutPS.IS_LOGGED)
					document.location.href = OnePageCheckoutPS.GUEST_TRACKING_URL+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
				else
					document.location.href = OnePageCheckoutPS.HISTORY_URL;
			}else{
                            //Fronted.closeDialog();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log('ERROR AJAX: ' + textStatus, errorThrown);
        }
	});
}

function updateCarrierSelectionAndGift(){}
function updateCarrierList(){}
function updatePaymentMethods(){}
function updatePaymentMethodsDisplay(){}
function cleanSelectAddressDelivery(){}

//compatibilidad modulo crosselling
function loadJavaScriptReview(){
    $(function(){
//        if($('#crossselling_list').length > 0)
//        {
//        	//init the serialScroll for thumbs
//        	cs_serialScrollNbImages = $('#crossselling_list li').length;
//        	cs_serialScrollNbImagesDisplayed = 5;
//        	cs_serialScrollActualImagesIndex = 0;
//        	$('#crossselling_list').serialScroll({
//        		items:'li',
//        		prev:'a#crossselling_scroll_left',
//        		next:'a#crossselling_scroll_right',
//        		axis:'x',
//        		offset:0,
//        		stop:true,
//        		onBefore:cs_serialScrollFixLock,
//        		duration:300,
//        		step: 1,
//        		lazy:true,
//        		lock: false,
//        		force:false,
//        		cycle:false
//        	});
//        	$('#crossselling_list').trigger( 'goto', [ (typeof cs_middle !== 'undefined' ? cs_middle : middle)-3] );
//        }

//        $('#onepagecheckoutps_step_review #gift-products_block .ajax_add_to_cart_button').die('click');

//
            $('#onepagecheckoutps_step_review .ajax_add_to_cart_button').unbind('click').click(function(event){
                var idProduct = 0;

                if (!$.isEmpty($(event.currentTarget).attr('data-id-product')))
                    idProduct = $(event.currentTarget).attr('data-id-product');
                else
                    idProduct =  $(this).attr('rel').replace('ajax_id_product_', '');

                if ($('#onepagecheckoutps_step_review #gift-products_block').length > 0){
                    event.preventDefault();
                    window.location = $(event.currentTarget).attr('href');

                    return false;
                }

                if (!$.isEmpty(idProduct)){
                    ajaxCart.add(idProduct, null, false, this);
                    Carrier.getByCountry();

                    return false;
                }
            });
//        }

        $('#onepagecheckoutps_step_review .ajax_add_to_cart_button').css({visibility: 'visible'});

        //compatibilidad con modulo CheckoutFields
        if (typeof checkoutfields !== 'undefined')
            checkoutfields.bindAjaxSave();

        //compatibilidad con modulo paragonfaktura
        $('#pfform input').click(function(){
            var value = $('#pfform input:checked').val();
            var id_cart = $('#pfform #pf_id').val();
            $.ajax({
              type: "POST",
              url: "modules/paragonfaktura/save.php",
              data: { value: value, id_cart: id_cart }
            }).done(function( msg ) {

            });
		});
    });
}

//compatibilidad con modulo nacex.
function modalWin(url) {
	var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
  	var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
	window.open(url,'','height=550,width=820,top='+(TopPosition-10)+',left='+LeftPosition+',toolbar=no,directories=no,status=no,menubar=no,scrollbars=si,resizable=no,location=no,modal=yes');
}
function seleccionadoNacexShop(tipo, txt) {
    setDatosSession(txt);

    $('#' + Carrier.id_delivery_option_selected).addClass('point_selected');
}
function setDatosSession(txt){
    $.ajax({
		type: 'POST',
		url: orderOpcUrl + '?rand=' + new Date().getTime(),
		data: 'action=setFieldsNacex&is_ajax=true&txt=' + txt + '&token=' + static_token,
		success: function(){
			Carrier.getByCountry();
		}
    });
}
//support module payment: Sveawebpay
function getAddressSveawebpay(){
    var ssn = $("#sveawebpay_security_number").val();
    var md5v = $("#sveawebpay_md5").val();

    $.get(baseDir + 'modules/sveawebpay/sveagetaddress.php', {ssn: ssn, md5:hex_md5(ssn+md5v), email:'', iscompany:false, sveatype: 1, country: 'SE', isinvoice: true, quickcall: true},
        function(data){
            if(data!='-1')
            {
                var parts = data.split('*');
                var names = parts[0].split(' ');
                var lastname = $.trim(names[0]);
                var firstname = '';
                var address_one=$.trim(parts[1]);
                var address_two=$.trim(parts[2]);
                if(address_one=='')
                {
                    address_one=address_two;
                    address_two='';
                }
                for(var i in names)
                {
                    if(i!=0)
                    {
                        firstname = firstname + ' ' + names[i];
                        firstname = firstname.replace(",", "");
                    }
                }
                lastname = lastname.replace(",", "");
                $('#customer_firstname').val($.trim(firstname));
                $('#customer_lastname').val(lastname);
                $('#delivery_address1').val(address_one);
                $('#delivery_address2').val(address_two);
                $('#delivery_postcode').val($.trim(parts[3]));
                $('#delivery_city').val($.trim(parts[4]));
                $('#delivery_dni').val(ssn);
            }
	});
}

function reloadPage(){
	location.reload();
}
function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}
function compareVersions(installed, required) {
    var a = installed.split('.');
    var b = required.split('.');

    for (var i = 0; i < a.length; ++i) {
        a[i] = Number(a[i]);
    }
    for (var i = 0; i < b.length; ++i) {
        b[i] = Number(b[i]);
    }
    if (a.length == 2) {
        a[2] = 0;
    }

    if (a[0] > b[0]) return true;
    if (a[0] < b[0]) return false;

    if (a[1] > b[1]) return true;
    if (a[1] < b[1]) return false;

    if (a[2] > b[2]) return true;
    if (a[2] < b[2]) return false;

    return true;
}

var reload_init_opc = setInterval(function(){
    if (typeof AppOPC !== typeof undefined){
        if(!AppOPC.initialized)
            AppOPC.init();
        else
            clearInterval(reload_init_opc)
    }
}, 2000);

var remove_uniform_aux = false;
var remove_uniform = setInterval(function(){
    if(!remove_uniform_aux){
        Fronted.removeUniform();
        remove_uniform_aux = true;
    }else
        clearInterval(remove_uniform)
}, 10000);