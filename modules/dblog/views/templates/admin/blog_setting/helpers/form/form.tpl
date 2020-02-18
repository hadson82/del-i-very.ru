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
    {if $input.type == 'select2customer'}
        {assign var="field_value" value=$fields_value[$input.name]}
        <div class="form-group">
            <div class="col-lg-9">
                <input type="hidden" class="select2customer" name="{$input.name|escape:'quotes':'UTF-8'}">
            </div>
        </div>
        <script>
            $('.select2customer[name={$input.name|escape:'quotes':'UTF-8'}]').select2({
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
                            action: 'get_customers'
                        };
                    },
                    results: function(data, page)
                    {
                        return {
                            results: data.customers
                        };
                    },
                    params: {
                        type: 'POST'
                    }
                }
            });
            $('.select2customer[name={$input.name|escape:'quotes':'UTF-8'}]').select2('data',{$field_value|json_encode});
        </script>
    {/if}
{/block}