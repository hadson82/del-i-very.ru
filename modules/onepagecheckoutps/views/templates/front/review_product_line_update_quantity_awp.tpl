{*
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
*}

<div id="cart_quantity_button" class="cart_quantity_button">
    <input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count|intval}{else}{$product.cart_quantity-$quantityDisplayed|intval}{/if}" name="quantity_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}_hidden" />
    <div class="row col-xs-12 nopadding pts-vcenter">
        <div class="input-group input-group-sm">
            <span class="input-group-btn">
                <button type="button" class="btn btn-default btn-number cart_quantity_down"
                        {if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}{else}disabled="disabled"{/if}
                        data-type="minus" id="cart_quantity_down_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$id_customization|intval}_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}">
                    <i class="fa fa-minus"></i>
                </button>
            </span>
            <input type="text" autocomplete="off" class="cart_quantity_input form-control input-number text-center" value="{$product_quantity|intval}"
                   name="quantity_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$id_customization|intval}_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}" />
            <span class="input-group-btn">
                <button type="button" class="btn btn-default btn-number cart_quantity_up" data-type="plus"
                        id="cart_quantity_up_{$product.id_product|intval}_{$product.id_product_attribute|intval}_{$id_customization|intval}_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}">
                    <i class="fa fa-plus"></i>
                </button>
            </span>
        </div>
        <div class="input-group input-group-sm nopadding">
            <span class="input-group-btn">
                <a type="button" class="{*btn btn-link*} btn-number cart_quantity_delete"
                   id="{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}"
                   href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;special_instructions={$product.instructions_valid|escape:'html':'UTF-8'}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart|escape:'htmlall':'UTF-8'}")|escape:'htmlall':'UTF-8'}">
                    <i class="fa fa-trash-o"></i>
                </a>
            </span>
        </div>
    </div>
    <div class="input-group input-group-sm hidden">
        <span class="input-group-btn">
            <button type="button" class="btn btn-default btn-number cart_quantity_up"
                    {if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
                    {else}
                        disabled="disabled"
                    {/if}
                    data-type="minus" id="cart_quantity_down_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}">
                <i class="fa fa-minus"></i>
            </button>
        </span>
        <input type="text" autocomplete="off" class="cart_quantity_input form-control input-number"
               value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count|intval}{else}{$product.cart_quantity-$quantityDisplayed|intval}{/if}"
               name="quantity_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}" />
        <span class="input-group-btn">
            <button type="button" class="btn btn-default btn-number cart_quantity_down" data-type="plus"
                    id="cart_quantity_up_{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}">
                <i class="fa fa-plus"></i>
            </button>
        </span>

        {if ((isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0) OR (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0))
            AND (!isset($noDeleteButton) OR !$noDeleteButton)
            AND ((!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed) > 0 AND empty($product.gift))}
            <span class="input-group-btn cart_delete {*visible-xs*}">
                <a type="button" class="btn btn-danger btn-number cart_quantity_delete"
                   id="{$product.id_product|intval}_{$product.id_product_attribute|intval}_0_{$product.instructions_valid|escape:'html':'UTF-8'}_{$product.id_address_delivery|intval}"
                   href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart|escape:'htmlall':'UTF-8'}")|escape:'htmlall':'UTF-8'}">
                    <i class="fa fa-remove"></i>
                </a>
            </span>
        {/if}
    </div>
</div>