/**
 * 2007-2015 PrestaShop
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
 * @author    Goryachev Dmitry    <dariusakafest@gmail.com>
 * @copyright 2007-2015 Goryachev Dmitry
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$('.addMetaVar').live('click', function (e) {
    e.preventDefault();
    var meta_var = $(this).data('meta-var');
    var field_name = $(this).data('field-name');
    var field = $('[name^='+field_name+']:visible');
    var cursor_pos = getCursorPosition(field[0]);
    var first_part = field.val().substring(0, cursor_pos);
    var end_part = field.val().substring(cursor_pos);
    field.val(first_part + meta_var + end_part);
});

function getCursorPosition(ctrl) {
    var caret_pos = 0;
    if (document.selection) {
        ctrl.focus ();
        var sel = document.selection.createRange();
        sel.moveStart ('character', -ctrl.value.length);
        caret_pos = sel.text.length;
    } else if ( ctrl.selectionStart || ctrl.selectionStart == '0' ) {
        caret_pos = ctrl.selectionStart;
    }
    return caret_pos;
}

$(function () {
    $('#generate_form').live('submit', function (e) {
        $('#product_form').addClass('loading');
        e.preventDefault();
        var count_products = 0;
        var list = [];
        function beginGenerate()
        {
            $.ajax({
                url: document.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    method: 'get_ids_product'
                },
                success: function (r)
                {
                    list = r;
                    count_products = list.length;
                    var first = list.shift();
                    generateMetaTags(first);

                },
                error: function () {
                    $('#product_form').removeClass('loading');
                    alert('Has error');
                }
            });
        }

        function generateMetaTags(id_product)
        {
            var progress = 100 - parseInt(list.length/(count_products / 100));
            setProgress(progress);
            $.ajax({
                url: document.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    method: 'generate_product_meta_tags',
                    id_product: id_product
                },
                success: function (r)
                {
                    if (list.length)
                    {
                        var next = list.shift();
                        generateMetaTags(next);
                    }
                    else
                        successGenerate(success_message);
                },
                error: function () {
                    $('#product_form').removeClass('loading');
                    alert('Has error');
                    setProgress(0);
                }
            });
        }
        function successGenerate(message)
        {
            alert(message);
            $('#product_form').removeClass('loading');
            setProgress(0);
        }
        function setProgress(progress)
        {
            var progress_bar = $('.progress_bar');
            progress_bar.find('.progress_percent').text(progress + '%');
            progress_bar.find('.progress_line').css({
                width: progress + '%'
            });
        }
        beginGenerate();
    });

    $('#generate_form_category').live('submit', function (e) {
        $('#category_form').addClass('loading');
        e.preventDefault();
        var count_categories = 0;
        var list = [];
        function beginGenerate()
        {
            $.ajax({
                url: document.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    method: 'get_ids_category'
                },
                success: function (r)
                {
                    list = r;
                    count_categories = list.length;
                    var first = list.shift();
                    generateMetaTags(first);

                },
                error: function () {
                    $('#category_form').removeClass('loading');
                    alert('Has error');
                }
            });
        }

        function generateMetaTags(id_category)
        {
            var progress = 100 - parseInt(list.length/(count_categories / 100));
            setProgress(progress);
            $.ajax({
                url: document.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    method: 'generate_category_meta_tags',
                    id_category: id_category
                },
                success: function (r)
                {
                    if (list.length)
                    {
                        var next = list.shift();
                        generateMetaTags(next);
                    }
                    else
                        successGenerate(success_message);
                },
                error: function () {
                    $('#category_form').removeClass('loading');
                    alert('Has error');
                    setProgress(0);
                }
            });
        }
        function successGenerate(message)
        {
            alert(message);
            $('#category_form').removeClass('loading');
            setProgress(0);
        }
        function setProgress(progress)
        {
            var progress_bar = $('.progress_bar');
            progress_bar.find('.progress_percent').text(progress + '%');
            progress_bar.find('.progress_line').css({
                width: progress + '%'
            });
        }
        beginGenerate();
    });
});