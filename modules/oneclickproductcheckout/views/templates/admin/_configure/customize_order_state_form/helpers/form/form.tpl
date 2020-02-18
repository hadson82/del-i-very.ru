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
* @author    SeoSA <885588@bk.ru>
* @copyright 2012-2017 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="input"}
    {if $input.type == 'search_product'}
        {assign var="field_value" value=$fields_value[$input.name]}
        {assign var="field_value_array" value=$fields_value["`$input.name`_ARRAY"]}
        <div class="select_products">
            <div class="search_row">
                <label class="control-label">{l s='Write for search' mod='oneclickproductcheckout'}</label>
                <input class="search_product" type="text"/>
            </div>
            <div class="search_row">
                <div class="left_column">
                    <label class="control-label">{l s='Select from list' mod='oneclickproductcheckout'}</label>
                    <select class="no_selected_product" multiple></select>
                    <input class="add_select_product btn btn-default" value="{l s='Add in select products' mod='oneclickproductcheckout'}" type="button"/>
                </div>
                <div class="right_column">
                    <label class="control-label">{l s='Selected' mod='oneclickproductcheckout'}</label>
                    <select name="{$input.name|escape:'quotes':'UTF-8'}_ARRAY[]" class="selected_product" multiple>
                        {if is_array($field_value_array) && count($field_value_array)}
                            {foreach from=$field_value_array item=product}
                                <option value="{$product.id_product|escape:'quotes':'UTF-8'}">{$product.id_product|escape:'quotes':'UTF-8'} - {$product.name|escape:'quotes':'UTF-8'}</option>
                            {/foreach}
                        {/if}
                    </select>
                    <input value="{$field_value|escape:'quotes':'UTF-8'}" type="hidden" class="selected_product_field" name="{$input.name|escape:'quotes':'UTF-8'}">
                    <input class="remove_select_product btn btn-default" value="{l s='Remove from select products' mod='oneclickproductcheckout'}" type="button"/>
                </div>
            </div>
        </div>
        <script>
            $(function () {
                $('.select_products').selectProducts({
                    path_ajax: "{$ajax_path|escape:'quotes':'UTF-8'}"
                });
            });
        </script>
    {/if}
    {$smarty.block.parent}
{/block}