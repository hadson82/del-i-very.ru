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
 * @author    Goryachev Dmitry    <dariusakafest@gmail.com>
 * @copyright 2007-2016 Goryachev Dmitry
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

$(function () {
    $('.style_input_file input[type=file]').change(function () {
        var file = $(this).get(0);
        var text_input = $('[data-file-name="'+$(this).attr('id')+'"]');
        if (text_input.length)
        {
            if (file.files.length)
                text_input.text(file.files[0].name);
            else
                text_input.text(text_input.data('not-select'));
        }
    });

    $('.style_input_file_multiple input[type=file]').live('change', function () {
        var file = $(this).get(0);
        var text_input = $('[data-file-name="'+$(this).attr('id')+'"]');
        if (text_input.length)
        {
            if (file.files.length)
            {
                text_input.text(text_input.data('file-selected').replace('%s', file.files.length));
                $('[data-upload-files="'+$(this).attr('id')+'"]').show();
            }
            else
            {
                text_input.text(text_input.data('not-select'));
                $('[data-upload-files="'+$(this).attr('id')+'"]').hide();
            }
        }
    });

    $('[data-upload-files]').live('click', function () {
        var input_name = $(this).data('upload-files');
        var input_file = $('#'+input_name);
        var files = input_file.get(0).files;

        var upload_files = [];

        if (!files.length)
            return false;

        $.each(files, function (index, value) {
            upload_files.push(value);
        });

        function uploadFiles(upload_files)
        {
            if (!files.length)
                return false;

            var file = upload_files.pop();
            var data = new FormData();
            data.append(input_file.attr('name'), file);
            data.append('ajax', true);
            data.append('action', 'upload_' + input_file.attr('name'));

            $.ajax({
                url: document.location.href.replace(document.location.hash, ''),
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                data: data,
                success: function (r)
                {
                    if (!r.hasError)
                    {
                        $('[data-list-files="'+input_name+'"]').prepend(r.image);
                    }
                    if (upload_files.length)
                    {
                        uploadFiles(upload_files);
                    }
                    else
                    {
                        input_file.replaceWith(input_file.clone());
                        var text_input = $('[data-file-name="'+input_name+'"]');
                        text_input.text(text_input.data('not-select'));
                        $('[data-upload-files="'+input_name+'"]').hide();
                    }
                }
            });
        }
        uploadFiles(upload_files);
    });

    $('[data-delete-file]').live('click', function () {
        var self = this;
        $.ajax({
            url: document.location.href.replace(document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'delete_' + $(this).data('delete-file'),
                id: $(this).data('file-id')
            },
            success: function (r)
            {
                if (!r.hasError)
                    self.closest('.item_file').remove();
            }
        });
    });
});