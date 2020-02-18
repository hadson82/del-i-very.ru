/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var OCC = {};
OCC.updatePayments = function (success)
{
    var payment = $('[name="occ_payment"]:checked');
    var id_payment = payment.attr('id');
    $('.list_payments').find('._loader').fadeIn(500);
    $.ajax({
        url: document.location.href.replace(document.location.hash, ''),
        type: 'POST',
        dataType: 'html',
        data: {
            ajax: true,
            method: 'loadPaymentMethods'
        },
        success: function (r)
        {
            $('.list_payments').html(r);
            $('#'+id_payment).click();
            if (typeof success != 'undefined')
                success(id_payment);
        }
    });
};

$(document).ready(function () {
    $('body').prepend($('.ocpc_stage, .one_click_product_checkout').remove());

    if (typeof $.fn.live == 'undefined')
        $.fn.live = $.fn.on;

    $('#submitOneClickCheckout').live('click', function () {
        var self = this;
        var form = $(self).closest('.one_click_product_checkout');
        var id_product_attribute = parseInt(form.find('[name="id_product_attribute"]').val());
        if (id_product_attribute > 0 && !ocpc_combinations[id_product_attribute].available_for_order)
            return false;

        form.find('._loader').fadeIn(500);
        setTimeout(function () {
            $.ajax({
                url: document.location.href,
                type: 'POST',
                dataType: 'json',
                data: getDataAuth(form, 'submitOneClickCheckout', 'oneclickproductcheckout'),
                success: function (r)
                {
                    hideAllErrorBox();
                    if(r.hasError)
                    {
                        var errors = '<li>' + r.errors.join('</li><li>') + '</li>';
                        showError(form, errors);
                        form.find('._loader').fadeOut(500);
                    }
                    else
                    {
                        $('body').append(r.google_analytics_script);
                        if (enabled_payment)
                        {
                            OCC.updatePayments(function (id_payment) {
                                var link = $('[data-payment="'+id_payment+'"]').find('a');
                                if (link.attr('href') &&
                                    (link.attr('href').match('http://') != null || link.attr('href').match('https://') != null))
                                    document.location.href = link.attr('href');
                                else
                                {
                                    if (link.attr('href') &&
                                        (link.attr('href').match('javascript:') != null))
                                    {
                                        eval('function funcClick() {'+link.attr('href').replace('javascript:', '')+'}');
                                        link.live('click', funcClick);
                                    }
                                    link.trigger('click');
                                }
                            });
                        }
                        else
                        {
                            setSuccess(form);
                            $('.one_click_product_checkout').setCenterPosAbsBlockOCPC();
                            form.find('._loader').fadeOut(500);
                        }
                    }

                },
                error: function ()
                {
                    showError(form,'<li>Has unknown error</li>');
                    form.find('._loader').fadeOut(500);
                }
            });
        },500);
    });
    $('#quantity').live('keyup', function () {
        var price = $(this).data('price');
        var quantity = parseInt($(this).val());
        $(this).val((!isNaN(quantity) ? quantity : ''));
        var form = $(this).closest('.one_click_product_checkout');
        form.find('.total_price').text(formatCurrency((price * (!isNaN(quantity) ? quantity : 0)), currencyFormat, currencySign, currencyBlank));
    });
    $('#quantity').live('blur', function () {
        var quantity = parseInt($(this).val());
        $(this).val((!isNaN(quantity) ? quantity : 1));
        $(this).trigger('keyup');
    });
    if (newpresta) {
        $('body').on('change', '.product-variants [data-product-attribute]', function () {
            var actionURL = $('form[action$="/cart"]').attr('action');
            var query = $('form[action$="/cart"]').serialize() + '&ajax=1&action=productrefresh';
            $.post(actionURL, query, null, 'json').then(function(resp) {
                var ajax = new XMLHttpRequest();
                ajax.open('get', resp.productUrl);
                ajax.responseType = 'document';
                ajax.onload = function(){
                    $('#idCombination').replaceWith(ajax.responseXML.getElementById('idCombination'));
                    changeOCPC();
                };
                ajax.send();
            });
        });
    }
    $('#showOneClickCheckout').live('click', function (e) {
        if ($('.list_payments').length && $('.list_payments > li').length == 0) {
            OCC.updatePayments();
        }
        var data = Object();
        data.method = 'getAttributesProduct';
        data.module = 'oneclickproductcheckout';
        data.ajax = true;
        data.form = $('form[action$="/cart"]').serialize();
        $.ajax({
            url: document.location.href,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (r)
            {
                $('#attributes_ocpc').remove();
                if(!r.hasError) {
                    var price = $('#quantity').data('price');
                    $('.one_click_product_checkout .product_price').after(r.html);
                    if (!isNaN(r.qty))
                        $('.one_click_product_checkout #quantity').val(r.qty);
                    $('.one_click_product_checkout .total_price').text(formatCurrency((price * (!isNaN(r.qty) ? r.qty : 0)), currencyFormat, currencySign, currencyBlank));
                }
            }
        });
        e.preventDefault();
        $('.one_click_product_checkout, .ocpc_stage').fadeIn(500);
        $('.one_click_product_checkout').setCenterPosAbsBlockOCPC();
    });
    $('#cancelOneClickCheckout,#cancelCheckoutX, .ocpc_stage').live('click', function (e) {
        e.preventDefault();
        $('.one_click_product_checkout, .ocpc_stage').fadeOut(500);
    });
    $('.incrementQuantity').live('click', function (e) {
        e.preventDefault();
        if(parseInt($(this).closest('.wrapper_quantity').find('[id=quantity]').val()) < quantityAvailable || allowBuyWhenOutOfStock)
        {
            $(this).closest('.wrapper_quantity').find('[id=quantity]').val(parseInt($(this).closest('.wrapper_quantity').find('[id=quantity]').val()) + 1);
            $(this).closest('.wrapper_quantity').find('[id=quantity]').trigger('keyup');
        }
    });
    $('.decrementQuantity').live('click', function (e) {
        e.preventDefault();
        if (parseInt($(this).closest('.wrapper_quantity').find('[id=quantity]').val()) > 1){
            $(this).closest('.wrapper_quantity').find('[id=quantity]').val(parseInt($(this).closest('.wrapper_quantity').find('[id=quantity]').val()) - 1);
            $(this).closest('.wrapper_quantity').find('[id=quantity]').trigger('keyup');
        }
    });
    $('body').on('input propertychange', '[id=quantity]', function () {
        if($(this).val() > quantityAvailable && !allowBuyWhenOutOfStock)
        {
            $(this).val(quantityAvailable);
        }
        if($(this).val() < 1 && !allowBuyWhenOutOfStock)
        {
            $(this).val(1);
        }
    })
    if (typeof $.fn.setCenterPosAbsBlockOCPC == 'undefined')
        $.fn.setCenterPosAbsBlockOCPC = function ()
        {
            var offsetElemTop = 20;
            var scrollTop = $(document).scrollTop();
            var elemWidth = $(this).width();
            var windowWidth = $(window).width();
            $(this).css({
                top: ($(this).height() > $(window).height() ? scrollTop + offsetElemTop : scrollTop + (($(window).height()-$(this).height())/2)),
                left: ((windowWidth-elemWidth)/2)
            });
        };
    $(window).resize(function () {
        $('.one_click_product_checkout').setCenterPosAbsBlockOCPC();
    });
    $('.one_click_product_checkout').setCenterPosAbsBlockOCPC();

    if (!!fields_mask) {
        for(var name in fields_mask) {
            $('.one_click_product_checkout input[name="'+name+'"]').inputmask(fields_mask[name]);
        };
    }
});

function getDataAuth(form, method, module)
{
    data = {};
    data.ajax = true;
    data.method = method;
    data.module = module;
    data.qty = form.find('#quantity').val();
    data.id_product_attribute = form.find('[name="id_product_attribute"]').val();
    data.check_payment_method = 0;
    if (form.find('[name="occ_payment"]:checked').length)
        data.check_payment_method = 1;
    var fields = fields_json;
    for(var item in fields)
    {
        if(!fields[item].visible)
            continue;
        data[fields[item].name] = $('#ocpc_' + fields[item].name).val();
    }
    return data;
}

function showError(form,text)
{
    var error_box = form.find('._error');
    error_box.html('<ul>' +  text + '</ul>');
    error_box.show();
    error_box.css({
        height: 'auto',
        overflow: 'visible'
    });
}
function hideAllErrorBox()
{
    $('._error').hide();
}
function setSuccess(form)
{
    form.html('<div class="success_message">' + success_message + '<a href="#" id="cancelCheckoutX" title="Отмена">×</a></div>');
}
/*1.7add begin*/
if(typeof formatCurrency != 'function') {
    function formatCurrency(price, currencyFormat, currencySign, currencyBlank)
    {
        // if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)
        var blank = '';
        if (typeof priceDisplayPrecision === 'undefined')
            var priceDisplayPrecision = priceprecision;
        price = parseFloat(price.toFixed(10));
        price = ps_round(price, priceDisplayPrecision);
        if (currencyBlank > 0)
            blank = ' ';
        if (currencyFormat == 1)
            return currencySign + blank + formatNumber(price, priceDisplayPrecision, ',', '.');
        if (currencyFormat == 2)
            return (formatNumber(price, priceDisplayPrecision, ' ', ',') + blank + currencySign);
        return price;
    }
    //return a formatted number
    function formatNumber(value, numberOfDecimal, thousenSeparator, virgule)
    {
        value = value.toFixed(numberOfDecimal);
        var val_string = value+'';
        var tmp = val_string.split('.');
        var abs_val_string = (tmp.length === 2) ? tmp[0] : val_string;
        var deci_string = ('0.' + (tmp.length === 2 ? tmp[1] : 0)).substr(2);
        var nb = abs_val_string.length;

        for (var i = 1 ; i < 4; i++)
            if (value >= Math.pow(10, (3 * i)))
                abs_val_string = abs_val_string.substring(0, nb - (3 * i)) + thousenSeparator + abs_val_string.substring(nb - (3 * i));

        if (parseInt(numberOfDecimal) === 0)
            return abs_val_string;
        return abs_val_string + virgule + (deci_string > 0 ? deci_string : '00');
    }

    function ps_round_half_up(value, precision)
    {
        var mul = Math.pow(10, precision);
        var val = value * mul;

        var next_digit = Math.floor(val * 10) - 10 * Math.floor(val);
        if (next_digit >= 5)
            val = Math.ceil(val);
        else
            val = Math.floor(val);

        return val / mul;
    }

    function ps_round(value, places)
    {
        if (typeof(roundMode) === 'undefined')
            roundMode = 2;
        if (typeof(places) === 'undefined')
            places = 2;

        var method = roundMode;

        if (method === 0)
            return ceilf(value, places);
        else if (method === 1)
            return floorf(value, places);
        else if (method === 2)
            return ps_round_half_up(value, places);
        else if (method == 3 || method == 4 || method == 5)
        {
            // From PHP Math.c
            var precision_places = 14 - Math.floor(ps_log10(Math.abs(value)));
            var f1 = Math.pow(10, Math.abs(places));

            if (precision_places > places && precision_places - places < 15)
            {
                var f2 = Math.pow(10, Math.abs(precision_places));
                if (precision_places >= 0)
                    tmp_value = value * f2;
                else
                    tmp_value = value / f2;

                tmp_value = ps_round_helper(tmp_value, roundMode);

                /* now correctly move the decimal point */
                f2 = Math.pow(10, Math.abs(places - precision_places));
                /* because places < precision_places */
                tmp_value /= f2;
            }
            else
            {
                /* adjust the value */
                if (places >= 0)
                    tmp_value = value * f1;
                else
                    tmp_value = value / f1;

                if (Math.abs(tmp_value) >= 1e15)
                    return value;
            }

            tmp_value = ps_round_helper(tmp_value, roundMode);
            if (places > 0)
                tmp_value = tmp_value / f1;
            else
                tmp_value = tmp_value * f1;

            return tmp_value;
        }
    }
}
/*1.7add end*/