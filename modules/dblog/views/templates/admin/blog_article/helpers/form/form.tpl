{*
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
*}

{extends file="helpers/form/form.tpl"}

{block name="input"}
    {$smarty.block.parent}
    {if $input.type == 'blog_gallery'}
        {assign var="field_value" value=$fields_value[$input.name]}
        <div class="style_input_file_multiple">
            <label for="file_{$input.name|escape:'quotes':'UTF-8'}">
                <span data-file-selected="{l s='(%s) files selected' mod='dblog'}" data-not-select="{l s='please, select files' mod='dblog'}" data-file-name="file_{$input.name|escape:'quotes':'UTF-8'}">{l s='please, select files' mod='dblog'}</span>
                <input multiple id="file_{$input.name|escape:'quotes':'UTF-8'}" type="file" name="{$input.name|escape:'quotes':'UTF-8'}">
            </label>
            <button style="display: none" data-upload-files="file_{$input.name|escape:'quotes':'UTF-8'}" type="button">{l s='Upload files' mod='dblog'}</button>
            <div data-list-files="file_{$input.name|escape:'quotes':'UTF-8'}" class="multiple_files">
                {if is_array($field_value) && count($field_value)}
                    {foreach from=$field_value item=image}
                        {include file="./gallery_image.tpl" id=$image.id_blog_image name=$input.name path=BlogImage::getImgPath($image.id_blog_image, 'preview_logo')}
                    {/foreach}
                {/if}
            </div>
        </div>
    {elseif $input.type == 'blog_file'}
        {assign var="field_value" value=$fields_value[$input.name]}
        <label class="style_input_file" for="file_{$input.name|escape:'quotes':'UTF-8'}">
            <span data-not-select="{l s='not select' mod='dblog'}" data-file-name="file_{$input.name|escape:'quotes':'UTF-8'}">{l s='not select' mod='dblog'}</span>
            <span>{l s='Select file' mod='dblog'}</span>
            <input id="file_{$input.name|escape:'quotes':'UTF-8'}" type="file" name="{$input.name|escape:'quotes':'UTF-8'}">
        </label>
        {if $field_value}
            <div class="item_file">
                <img src="{BlogImage::getImgPath($field_value, 'preview_logo')|escape:'quotes':'UTF-8'}">
                <div>
                    <button class="btn btn-danger" data-delete-file="{$input.name|escape:'quotes':'UTF-8'}" data-file-id="{$field_value|escape:'quotes':'UTF-8'}" type="button">
                        <i class="icon-remove"></i>
                        {l s='Delete' mod='dblog'}
                    </button>
                </div>
            </div>
        {/if}
    {elseif $input.type == 'select2tags'}
        {assign var="field_value" value=$fields_value[$input.name]}
        <div class="form-group">
            <div class="col-lg-9">
                <input type="hidden" class="select2tags" name="{$input.name|escape:'quotes':'UTF-8'}">
            </div>
        </div>
        <script>
            $('.select2tags[name={$input.name|escape:'quotes':'UTF-8'}]').select2({
                tags:{$fields_value['available_tags']|json_encode},
                maximumInputLength: 50,
                width: '100%',
                separator: '|'
            });
            $('.select2tags[name={$input.name|escape:'quotes':'UTF-8'}]').select2('data',{$field_value|json_encode});
        </script>
    {elseif $input.type == 'addAfter'}
        <p class="alert alert-danger">
            {$input.descError|escape:'quotes':'UTF-8'}
        </p>
    {elseif $input.type == 'select2products'}
        {assign var="field_value" value=$fields_value[$input.name]}
        <div class="form-group">
            <div class="col-lg-9">
                <input type="hidden" class="select2products" name="{$input.name|escape:'quotes':'UTF-8'}">
            </div>
        </div>
        <script>
            $('.select2products[name={$input.name|escape:'quotes':'UTF-8'}]').select2({
                tags: [],
                maximumInputLength: 50,
                width: '100%',
                separator: '|',
                ajax: {
                    url: "{$link->getAdminLink($smarty.get.controller)|escape:'quotes':'UTF-8'}",
                    dataType: 'json',
                    data: function (term, page)
                    {
                        return {
                          guery : term,
                          ajax: true,
                          action: 'get_products'
                        };
                    },
                    results: function(data, page)
                    {
                        return {
                            results: data.products
                        };
                    },
                    params: {
                        type: 'POST'
                    }
                }
            });
            $('.select2products[name={$input.name|escape:'quotes':'UTF-8'}]').select2('data',{$field_value|json_encode});
        </script>
    {/if}
{/block}

{block name="script"}
    var PS_ALLOW_ACCENTED_CHARS_URL = {$PS_ALLOW_ACCENTED_CHARS_URL|intval};
    var ps_force_friendly_product = "{$ps_force_friendly_product|escape:'quotes':'UTF-8'}";
    setInterval(function () {
        $.ajax({ url: 'index.php' });
    }, 60000);
{/block}