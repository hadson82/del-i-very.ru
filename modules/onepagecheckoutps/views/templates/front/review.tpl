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

<div id="header-order-detail-content" class="row hidden-xs hidden-sm">
    <div class="col-md-{if $CONFIGS.OPC_SHOW_UNIT_PRICE}4{else}6{/if} col-md-offset-1">
        <h5>{l s='Description' mod='onepagecheckoutps'}</h5>
    </div>
    {if $CONFIGS.OPC_SHOW_UNIT_PRICE}
        <div class="col-md-2">
            <h5 class="text-right">{l s='Unit price' mod='onepagecheckoutps'}</h5>
        </div>
    {/if}
    <div class="col-md-3">
        <h5 class="text-center">{l s='Qty' mod='onepagecheckoutps'}</h5>
    </div>
    <div class="col-md-2">
        <h5 class="text-right">{l s='Total' mod='onepagecheckoutps'}</h5>
    </div>
</div>
<div id="order-detail-content">
    {foreach from=$products|@sortby:'name' item=product}
        {assign var='productId' value=$product.id_product}
        {assign var='productAttributeId' value=$product.id_product_attribute}
        {assign var='quantityDisplayed' value=0}
        {assign var='odd' value=$product@iteration%2}
        {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) or count($gift_products)}
        {* Display the product line *}
        {if isset($product.productmega)}
            {foreach from=$product.productmega item=mega name=productMegas}
                {include file="./review_product_line_megaproduct.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first mega=$mega}
            {/foreach}
        {else}
			{if isset($attributewizardpro)}
				{include file="./review_product_line_awp.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first}
			{else}
				{include file="./review_product_line.tpl" CONFIGS=$CONFIGS productLast=$product@last productFirst=$product@first}
			{/if}
        {/if}
    {/foreach}
	{foreach from=$gift_products|@sortby:'name' item=product}
        {assign var='productId' value=$product.id_product}
        {assign var='productAttributeId' value=$product.id_product_attribute}
        {assign var='quantityDisplayed' value=0}
        {assign var='odd' value=($product@iteration+$last_was_odd)%2}
        {assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
        {assign var='cannotModify' value=1}
        {* Display the gift product line *}
        {include file="./review_product_line.tpl" productLast=$product@last productFirst=$product@first}
    {/foreach}

    <div class="nopadding order_total_items">
        {if sizeof($discounts)}
            {foreach $discounts as $discount}
                <div class="row middle item_total cart_discount end-xs" id="cart_discount_{$discount.id_discount|intval}">
                    <div class="col-xs-8 col-md-10 text-right">
                        <span class="bold cart_discount_name text-right">
                            {$discount.name|escape:'htmlall':'UTF-8'}:
                        </span>
                    </div>
                    <div class="col-xs-4 col-md-2 cart_discount_price text-right">
                        <span class="price-discount price">
                            {if strlen($discount.code)}
                                <i class="fa fa-trash-o cart_quantity_delete pull-left"
                                   onclick="Review.processDiscount({ldelim}'id_discount' : {$discount.id_discount|intval}, 'action' : 'delete'{rdelim})"></i>
                            {/if}
                            {if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
                        </span>
                    </div>
                </div>
            {/foreach}
        {/if}

        <!--{if $CONFIGS.OPC_SHOW_TOTAL_PRODUCT}
            {assign var='value_total_products' value=$total_products}
            {if $use_taxes and not $priceDisplay}
                {assign var='value_total_products' value=$total_products_wt}
            {/if}
            <div class="row middle item_total cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total products' mod='onepagecheckoutps'}
                        {if $use_taxes}
                            {if $priceDisplay}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                            {else}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                            {/if}
                        {/if}
                        :
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="total_product">
                        {displayPrice price=$value_total_products}
                    </span>
                </div>
            </div>
        {/if}-->
        {if $CONFIGS.OPC_SHOW_TOTAL_DISCOUNT}
            <div class="row middle item_total cart_total_voucher {if $total_discounts eq 0}hidden{/if} end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total vouchers' mod='onepagecheckoutps'}
                        {if $use_taxes}
                            {if $priceDisplay}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                            {else}
                                {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                            {/if}
                        {/if}
                        :
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price-discount price" id="total_discount">
                        {if $use_taxes && !$priceDisplay}
                            {assign var='total_discounts_negative' value=$total_discounts * -1}
                        {else}
                            {assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
                        {/if}
                        {displayPrice price=$total_discounts_negative}
                    </span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_WRAPPING}
            <div class="row middle item_total cart_total_voucher {if $total_wrapping eq 0}hidden{/if} end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total gift-wrapping' mod='onepagecheckoutps'}
                        {if $use_taxes and $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                        {elseif $use_taxes and not $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                        {/if}:
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price-discount price" id="total_discount">
                        {if $use_taxes}
                            {if $priceDisplay}
                                {displayPrice price=$total_wrapping_tax_exc}
                            {else}
                                {displayPrice price=$total_wrapping}
                            {/if}
                        {else}
                            {displayPrice price=$total_wrapping_tax_exc}
                        {/if}
                    </span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_SHIPPING}
            <div class="row middle item_total cart_total_delivery end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right row end-xs">
                        {l s='Total shipping' mod='onepagecheckoutps'}
                        {if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
{*                            {l s='Shipping' mod='onepagecheckoutps'}*}
                        {elseif $use_taxes and $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>{/if}
                        {elseif $use_taxes and not $priceDisplay}
                            {if $display_tax_label}<span class="tax">&nbsp;{l s='(tax incl.)' mod='onepagecheckoutps'}</span>{/if}
                        {/if}:
                    </span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="total_shipping">
                        {if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
                            {l s='Free Shipping!' mod='onepagecheckoutps'}
                        {elseif $use_taxes and not $priceDisplay}
                            {displayPrice price=$total_shipping}
                        {else}
                            {displayPrice price=$total_shipping_tax_exc}
                        {/if}
                    </span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_WITHOUT_TAX}
            {if $use_taxes}
                <div class="row middle item_total cart_total_price total_without_tax end-xs">
                    <div class="col-xs-8 col-md-10 text-right">
                        <span class="bold text-right row end-xs">
                            {l s='Total' mod='onepagecheckoutps'}
                            <span class="tax">&nbsp;{l s='(tax excl.)' mod='onepagecheckoutps'}</span>:
                        </span>
                    </div>
                    <div class="col-xs-4 col-md-2 text-right">
                        <span class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</span>
                    </div>
                </div>
            {/if}
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_TAX}
            <div class="row middle item_total cart_total_tax end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total tax' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="total_tax">{displayPrice price=$total_tax}</span>
                </div>
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_TOTAL_PRICE}
            <div class="row middle item_total cart_total_price total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total amount of your purchase' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="bold price" id="total_price">
                        {if $use_taxes}{displayPrice price=$total_price}{else}{displayPrice price=$total_price_without_tax}{/if}
                    </span>
                </div>
            </div>
        {/if}
        {if isset($COD_FEE)}
            <div class="row middle item_total cod_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='COD Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_cod_fee">{displayPrice price=$COD_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cod_fee cart_total_price total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total + COD Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_cod" equation='a + b' a=$total_price b=$COD_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_cod}</span>
                </div>
            </div>
        {/if}
        {if isset($BNKPLUS_DISCOUNT)}
            <div class="row middle item_total bnkplus_discount cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Discount Bank Wire' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_bnkplus_discount">{displayPrice price=$BNKPLUS_DISCOUNT}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price bnkplus_discount end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total - Discount Bank Wire' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_bnkplus" equation='a - b' a=$total_price b=$BNKPLUS_DISCOUNT}
                    <span class="price" id="total_price">{displayPrice price=$total_price_bnkplus}</span>
                </div>
            </div>
        {/if}
        {if isset($PAYPAL_FEE)}
            <div class="row middle item_total paypal_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Paypal Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_paypal_fee">{displayPrice price=$PAYPAL_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price paypal_fee end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total + Paypal Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_paypal" equation='a + b' a=$total_price b=$PAYPAL_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_paypal}</span>
                </div>
            </div>
        {/if}
		{if isset($TPV_FEE)}
            <div class="row middle item_total tpv_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='TPV Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_tpv_fee">{displayPrice price=$TPV_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price tpv_fee end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total + TPV Fee' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_tpv" equation='a + b' a=$total_price b=$TPV_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_tpv}</span>
                </div>
            </div>
        {/if}
        {if isset($SEQURA_FEE)}
            <div class="row middle item_total sequra_fee cart_total_price end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Administration fees payment in 7 days' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    <span class="price" id="price_sequra_fee">{displayPrice price=$SEQURA_FEE}</span>
                </div>
            </div>
            <div class="row middle item_total cart_total_price total_price sequra_fee end-xs">
                <div class="col-xs-8 col-md-10 text-right">
                    <span class="bold text-right">{l s='Total fees incl' mod='onepagecheckoutps'}:</span>
                </div>
                <div class="col-xs-4 col-md-2 text-right">
                    {math assign="total_price_sequra" equation='a + b' a=$total_price b=$SEQURA_FEE}
                    <span class="price" id="total_price">{displayPrice price=$total_price_sequra}</span>
                </div>
            </div>
        {/if}
        <div class="row middle item_total extra_fee cart_total_price end-xs hidden">
            <div class="col-xs-8 col-md-10 text-right">
                <span class="bold text-right" id="extra_fee_label"></span>
            </div>
            <div class="col-xs-4 col-md-2 text-right">
                <span class="price" id="extra_fee_price"></span>
            </div>
        </div>
        <div class="row middle item_total cart_total_price total_price extra_fee end-xs hidden">
            <div class="col-xs-8 col-md-10 text-right">
                <span class="bold text-right" id="extra_fee_total_price_label"></span>
            </div>
            <div class="col-xs-4 col-md-2 text-right">
                <span class="price" id="extra_fee_total_price"></span>
            </div>
        </div>
        {if $voucherAllowed}
            <div class="row cart_total_price" id="list-voucher-allowed">
                <div class="col-xs-12 col-md-6 nopadding">
                    <div class="row col-xs-8 col-md-8 pts-vcenter">
                        <div class="col-xs-6 col-sm-5 col-md-5">
                            <span class="bold">{l s='Voucher' mod='onepagecheckoutps'}</span>
                        </div><!--
                        --><div class="col-xs-6 col-sm-7 col-md-7 nopadding-xs">
                            <input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name|escape:'htmlall':'UTF-8'}{/if}" />
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <span type="button" id="submitAddDiscount" name="submitAddDiscount" class="btn btn-default btn-small">
                            {l s='Add' mod='onepagecheckoutps'}
                        </span>
                    </div>
                </div>
                {if $displayVouchers}
                    <div class="col-xs-12 col-md col-lg-4">
                        <div id="display_cart_vouchers">
                            <ul>
                                {foreach $displayVouchers as $voucher}
                                    <li>
                                        <span data-code="{$voucher.code|escape:'htmlall':'UTF-8'}" class="voucher_name">
                                            <i class="fa fa-caret-right"></i>
                                            {$voucher.code|escape:'htmlall':'UTF-8'} - {$voucher.name|escape:'htmlall':'UTF-8'}
                                        </span>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                {/if}
            </div>
        {/if}
        {if $CONFIGS.OPC_SHOW_REMAINING_FREE_SHIPPING}
            {if $total_shipping_tax_exc > 0 && !isset($virtualCart) && $free_ship > 0}
                <div class="alert alert-info text-center">
                    {l s='Remaining amount to be added to your cart in order to obtain free shipping' mod='onepagecheckoutps'}:
                    <span id="free_shipping"><b>{displayPrice price=$free_ship}</b></span>
                </div>
            {/if}
        {/if}
    </div>
</div>