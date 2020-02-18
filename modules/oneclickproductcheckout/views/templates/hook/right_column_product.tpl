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

{*{l s='Firsname' mod='oneclickproductcheckout'}*}
{*{l s='Lastname' mod='oneclickproductcheckout'}*}
{*{l s='Email' mod='oneclickproductcheckout'}*}
{*{l s='Phone' mod='oneclickproductcheckout'}*}
{if (!$allow_oosp && $product_obj->quantity <= 0) OR !$product_obj->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}
    {*<span href="#" class="exclusive exclusive_disabled btn btn-default">{$OCPC_SUBMIT_TEXT.$id_default_lang|escape:'quotes':'UTF-8'}</span>*}
{else}
<script>
    var ocpc_combinations = Object();
    {foreach from=$ocpc_combinations item=c}
        ocpc_combinations[{$c.id_product_attribute|intval}] = {
            price: {$c.price|floatval},
            image: "{if isset($ocpc_combination_images[$c.id_product_attribute]) && count($ocpc_combination_images[$c.id_product_attribute])}{$link->getImageLink($product_obj->link_rewrite, $ocpc_combination_images[$c.id_product_attribute][0].id_image, 'home_default')|escape:'quotes':'UTF-8'}{else}{$link->getImageLink($product_obj->link_rewrite, $cover_product.id_image, 'home_default')|escape:'quotes':'UTF-8'}{/if}",
            quantity: {$c.quantity|intval},
            available_for_order: {if (!$allow_oosp && $c.quantity <= 0) OR !$product_obj->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}0{else}1{/if}
        };
    {/foreach}
    document.addEventListener("DOMContentLoaded", function(event) {
        window.oldOCOCFindCombination = window.findCombination;
        window.findCombination = function (firstTime)
        {
            oldOCOCFindCombination(firstTime);
            changeOCPC();
        };
        changeOCPC();
    });
    function changeOCPC()
    {
        if (!$('#idCombination').val())
            return;

        var id_combination = parseInt($('#idCombination').val());
        if (!ocpc_combinations[id_combination]['available_for_order'])
        {
            $('#showOneClickCheckout').addClass('exclusive_disabled').attr('disabled', 'disabled');
            return false;
        }
        else
            $('#showOneClickCheckout').removeClass('exclusive_disabled').removeAttr('disabled');
        var form = $('.one_click_product_checkout');
        form.find('[name=id_product_attribute]').val(id_combination);
        form.find('.cover_product img').attr('src', ocpc_combinations[id_combination]['image']);
        form.find('.product_price .price').text(formatCurrency(ocpc_combinations[id_combination]['price'], currencyFormat, currencySign, currencyBlank));
        form.find('[id=quantity]').attr('data-price', ocpc_combinations[id_combination]['price']).data('price', ocpc_combinations[id_combination]['price']).trigger('keyup');
    }

    var enabled_payment = {$enabled_payment|intval};
</script>

{if array_key_exists('id_product_attribute', $product)}
    <input type="hidden" name="id_product_attribute" id="idCombination" value="{$product.id_product_attribute|intval}">
{/if}

    <div class="exclusive-block">
        <a href="#" class="exclusive btn btn-default" id="showOneClickCheckout">
            {if isset($OCPC_SUBMIT_TEXT.$id_default_lang)}
                {$OCPC_SUBMIT_TEXT.$id_default_lang|escape:'quotes':'UTF-8'}
                {else}
                Checkout order
            {/if}
        </a>
    </div>
<div class="ocpc_stage" style="display: none"></div>
<div class="form one_click_product_checkout" style="display: none">
    <a href="#" id="cancelOneClickCheckout" title="{l s='Close' mod='oneclickproductcheckout'}">×</a>
    <div class="column_left">
        <h3 class="heading">{l s='Checkout in one click' mod='oneclickproductcheckout'}</h3>
        <form action="" class="std">
            <input type="hidden" value="0" name="id_product_attribute"/>
            <div class="_loader" style="display: none;"></div>
            <div class="_error" style="display: none;">
            </div>
            <div class="form_content">
                {foreach from=$fields key=k item=ocpc_field}
                    {if !$ocpc_field.visible}
                        {continue}
                    {/if}
                    <div class="field form_group">
                    {if $ocpc_field.type == 'text'}
                            <label class="field_name">{$ocpc_field.label|ld}{if $ocpc_field.required}:<sup class="ocpc_required">*</sup>{/if}</label>
                            <input class="text" type="{if $ocpc_field.name == 'password'}password{else}text{/if}" name="{$ocpc_field.name|escape:'htmlall':'UTF-8'}" value="{$auto_complete_fields[$ocpc_field.name]|escape:'quotes':'UTF-8'}" id="ocpc_{$ocpc_field.name|escape:'htmlall':'UTF-8'}">
                    {elseif $ocpc_field.type == 'select'}
                            <label class="field_name">{$ocpc_field.label|ld}{if $ocpc_field.required}:<sup class="ocpc_required">*</sup>{/if}</label>
                            <select class="text" name="{$ocpc_field.name|escape:'htmlall':'UTF-8'}" id="ocpc_{$ocpc_field.name|escape:'htmlall':'UTF-8'}">
                                {foreach from=$auto_complete_fields[$ocpc_field.name] item=option}
                                    <option value="{if isset($option[$ocpc_field.name])}{$option[$ocpc_field.name]|escape:'htmlall':'UTF-8'}{/if}"{if $auto_complete_fields[$ocpc_field.name|cat:'_default'] == $option['id_'|cat:$ocpc_field.name]} selected{/if}>{l s=$option.name mod='oneclickproductcheckout'}</option>
                                {/foreach}
                            </select>
                    {/if}
                    {if !empty($help[$k][$id_default_lang])}
                        <br /><span style="font-size: 0.85em;">({$help[$k][$id_default_lang]|escape:'htmlall':'UTF-8'})</span>
                    {/if}
                    </div>

                {/foreach}
                {*{if $id_address}
                    <div class="field form_group">
                        <a href="{$link_address}?id_address={$id_address|intval}">{l s='Изменить адрес доставки' mod='oneclickproductcheckout'}</a>
                    </div>
                {/if}*}
					<div class="field form_group">
                        <p>Нажимая КУПИТЬ, Вы даете<a href="https://del-i-very.ru/content/12-obrabotka-personalnih-dannih.html" target="_blank"> согласие на обработку персональных данных</a></p>
						<p>Наш менеджер свяжется с Вами в ближайшее время и сообщит о стоимости услуг доставки.</a></p>
                    </div>
                {if $enabled_payment}
                    <div class="field form_group">
                        <div class="title_form">{l s='Payment methods' mod='oneclickproductcheckout'}</div>
                        <ul class="list_payments">
                            <div class="_loader" style="display: none;"></div>
                        </ul>
                    </div>
                {/if}
                <div class="submit">
                    <button type="button" id="submitOneClickCheckout">
                            {$OCPC_SUBMIT_TEXT.$id_default_lang|escape:'quotes':'UTF-8'}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="column_right">
        {if $cover_product}
        <div class="field form_group cover_product">
            <img class="img-fluid img-responsive" src="{$link->getImageLink($product_obj->link_rewrite, $cover_product.id_image, 'home_default')|escape:'quotes':'UTF-8'}">
        </div>
        {/if}
        {if $product_obj->name}
        <div class="field form_group description_short">
            {$product_obj->name|strip_tags|escape:'quotes':'UTF-8'}
        </div>
        {/if}
        <div class="field form_group product_price">
            {if $product_obj->specificPrice && count($product_obj->specificPrice)}
                    <span class="wholesale_price">{convertPrice price=$product_obj->getPriceStatic($product_obj->id, true, 0, 6, NULL, false, false)}</span>
            {/if}
            <span class="price">{convertPrice price=$product_obj->getPriceStatic($product_obj->id)}</span>
        </div>
        <div class="field form_group">
            <label for="quantity">{l s='Quantity' mod='oneclickproductcheckout'}:</label>
            <div class="wrapper_quantity">
                <input data-price="{$product_obj->getPriceStatic($product_obj->id)|floatval}" type="text" id="quantity" value="1">
                <a href="#" class="btn btn-default button-minus decrementQuantity"><span><i class="icon-minus"></span></i></a>
                <a href="#" class="btn btn-default button-plus  incrementQuantity"><span><i class="icon-plus"></span></i></a>
            </div>
        </div>
        <div class="field form_group price_block">
            <label>{l s='Total' mod='oneclickproductcheckout'}</label>
            <span class="total_price">{convertPrice price=$product_obj->getPriceStatic($product_obj->id)}</span>
        </div>
    </div>
</div>
{/if}
{if $fields_mask}
    <script>
        var fields_mask = {$fields_mask nofilter};
    </script>
{/if}