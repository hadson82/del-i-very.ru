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

$.fn.selectProducts = function (p)
{
    var _this = this;
    var defaults = {
        path_ajax: null
    };
    defaults = $.extend({}, defaults, p);
    function init()
    {
        function fillField()
        {
            var data = [];
            _this.find('.selected_product option').each(function () {
               data.push($(this).attr('value'));
            });
            $('.selected_product_field').val(data.join(','));
        }


        _this.find('.search_product').live('keyup', function () {
            var query = $(this).val();
            var select_products = [];
            _this.find('.selected_product option').each(function () {
                select_products.push($(this).attr('value'));
            });
            $.ajax({
                url: defaults.path_ajax,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: 'ocpc',
                    method: 'get_products',
                    query: query,
                    select_products: select_products
                },
                success: function (r) {
                    _this.find('.no_selected_product').html('');
                    for (var i in r)
                        _this.find('.no_selected_product').append('<option value="'+r[i].id_product+'">'+ r[i].id_product + ' - ' + r[i].name + '</option>');
                }
            });
        });
        _this.find('.add_select_product').live('click', function () {
            var options = _this.find('.no_selected_product option:selected').remove();
            _this.find('.selected_product').append(options);
            fillField();
        });
        _this.find('.remove_select_product').live('click', function () {
            var options = _this.find('.selected_product option:selected').remove();
            _this.find('.no_selected_product').append(options);
            fillField();
        });
    }
    init();

    $('.translatable-field .col-lg-2').removeClass().addClass('col-xs-2');
    $('.translatable-field .col-lg-9').removeClass().addClass('col-xs-3');
};
