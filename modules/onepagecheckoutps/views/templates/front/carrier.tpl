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

{if isset($css_files)}
    {foreach from=$css_files key=css_uri item=media}
        <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
    {/foreach}
{/if}
{if isset($js_files)}
    {foreach from=$js_files item=js_uri}
        <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
    {/foreach}
{/if}

<script type="text/javascript">
    var txtProduct = '{l s='product' mod='onepagecheckoutps' js=1}';
    var txtProducts = '{l s='products' mod='onepagecheckoutps' js=1}';
    var orderUrl = '{$link->getPageLink('order', true)|escape:'htmlall':'UTF-8'}';
    var is_necessary_postcode = Boolean({if isset($is_necessary_postcode)}{$is_necessary_postcode|intval}{/if});
    var is_necessary_city = Boolean({if isset($is_necessary_city)}{$is_necessary_city|intval}{/if});
    var id_carrier_selected = '{if isset($id_carrier_selected)}{$id_carrier_selected|escape:'htmlall':'UTF-8'}{/if}';

    var nacex_agcli = '{if isset($nacex_agcli)}{$nacex_agcli|escape:'htmlall':'UTF-8'}{/if}';

    {literal}
        if (is_necessary_postcode)
            $('div#onepagecheckoutps')
                .off('blur', 'input#delivery_postcode', Carrier.getByCountry)
                .on('blur', 'input#delivery_postcode', Carrier.getByCountry);
        if (is_necessary_city)
            $('div#onepagecheckoutps')
                .off('blur', 'input#delivery_city', Carrier.getByCountry)
                .on('blur', 'input#delivery_city', Carrier.getByCountry);
    {/literal}
</script>

{if isset($IS_VIRTUAL_CART) && $IS_VIRTUAL_CART}
    <input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
{else}
    <div id="shipping_container">
        {if ($hasError)}
            <p class="alert alert-warning">
                {foreach from=$errors key=k item="error" name="f_errors"}
                    -&nbsp;{$error|escape:'htmlall':'UTF-8'}
                    {if !$smarty.foreach.f_errors.last}<br/><br/>{/if}
                {/foreach}
            </p>

			<button class="btn btn-default pull-right btn-sm" type="button" onclick="Carrier.getByCountry();">
				<i class="fa fa-refresh"></i>
				{l s='Reload' mod='onepagecheckoutps'}
			</button>
        {else}
            <div class="delivery_options_address">
                {if isset($delivery_option_list)}
                    {foreach $delivery_option_list as $id_address => $option_list}
                        {foreach $option_list as $key => $option}
                            <div class="delivery_option {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}selected alert alert-info{/if}">
                                <div class="row pts-vcenter col-xs-12 nopadding">
                                    <div class="col-xs-1">
                                        <input class="delivery_option_radio not_unifrom not_uniform" type="radio" name="delivery_option[{$id_address|intval}]" id="delivery_option_{$id_address|intval}_{$option@index|intval}" value="{$key|escape:'htmlall':'UTF-8'}" {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
                                    </div><!--
                                    --><div class="delivery_option_logo {if !$CONFIGS.OPC_SHOW_IMAGE_CARRIER && !$CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}col-xs-8{else}col-md-3 col-xs-2{/if}">
                                        {foreach $option.carrier_list as $carrier}
                                            {if ($CONFIGS.OPC_SHOW_IMAGE_CARRIER)}
                                                {if $carrier.logo}
                                                    <img src="{$carrier.logo|escape:'htmlall':'UTF-8'}" alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}" class="img-thumbnail"/>
                                                {else}
                                                    <img src="{$ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}shipping.png" class="img-thumbnail"/>
                                                {/if}
                                            {else}
                                                <div class="delivery_option_title">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</div>
                                                {if !$carrier@last} - {/if}
                                            {/if}

                                            {if $carrier.instance->external_module_name != ''}
                                                <input type="hidden" class="module_carrier" name="{$carrier.instance->external_module_name|escape:'htmlall':'UTF-8'}" value="delivery_option_{$id_address|intval}_{$option@index|intval}" />
                                                <input type="hidden" name="name_carrier" id="name_carrier_{$id_address|intval}_{$option@index|intval}" value="{$carrier.instance->name|escape:'htmlall':'UTF-8'}" />
                                            {/if}
                                        {/foreach}
                                    </div><!--
                                    {if $CONFIGS.OPC_SHOW_IMAGE_CARRIER || $CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}
                                    --><div class="carrier_delay col-xs-6 col-md-6">
										{foreach $option.carrier_list as $carrier}
                                            {if $CONFIGS.OPC_SHOW_IMAGE_CARRIER}
                                                <div class="delivery_option_title">{$carrier.instance->name|escape:'htmlall':'UTF-8'}</div>
                                            {/if}
                                            {if $CONFIGS.OPC_SHOW_DESCRIPTION_CARRIER}
                                                {if $option.unique_carrier}
                                                    {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                        <div class="delivery_option_delay">
                                                            {$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}
                                                        </div>
                                                    {/if}
                                                {/if}
                                            {/if}
										{/foreach}
                                        </div><!--
                                    {/if}
                                    -->{*<div class="carrier_price col-xs-3">
                                        <div class="delivery_option_price text-right">
                                            {if $option.total_price_with_tax && (!isset($option.is_free) || (isset($option.is_free) && !$option.is_free)) && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                                {if $use_taxes == 1}
													{if $priceDisplay == 1}
														{convertPrice price=$option.total_price_without_tax}
														<span class="tax">
															{if $display_tax_label}{l s='(tax excl.)' mod='onepagecheckoutps'}{/if}
														</span>
													{else}
														{convertPrice price=$option.total_price_with_tax}
														<span class="tax">
															{if $display_tax_label} {l s='(tax incl.)' mod='onepagecheckoutps'}{/if}
														</span>
													{/if}
                                                {else}
                                                    {convertPrice price=$option.total_price_without_tax}
                                                {/if}
                                            {else}
                                                {l s='Free!' mod='onepagecheckoutps'}
                                            {/if}
                                        </div>
                                    </div>*}
                                    {if $carrier.instance->external_module_name != '' && isset($carrier.extra_info_carrier) && isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}
                                        <div class="extra_info_carrier pull-right" style="display:{if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}block{else}none{/if}">
                                            {if not empty($carrier.extra_info_carrier)}
                                                {$carrier.extra_info_carrier|escape:'quotes':'UTF-8'}
                                                <br />
                                                <a class="edit_pickup_point" onclick="Carrier.displayPopupModule_{$carrier.instance->external_module_name|escape:'htmlall':'UTF-8'}()">{l s='Edit pickup point' mod='onepagecheckoutps'}</a>
                                            {else}
                                                <a class="select_pickup_point" onclick="Carrier.displayPopupModule_{$carrier.instance->external_module_name|escape:'htmlall':'UTF-8'}()">{l s='Select pickup point' mod='onepagecheckoutps'}</a>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                        {if isset($HOOK_EXTRACARRIER_ADDR) && isset($HOOK_EXTRACARRIER_ADDR.$id_address)}
                            <div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address|intval}">
                                {$HOOK_EXTRACARRIER_ADDR.$id_address|escape:'htmlall':'UTF-8':false:true}
                                <div class="clear clearfix">&nbsp;</div>
                            </div>
                        {/if}
                    {/foreach}
                {/if}
            </div>

            {if (isset($recyclablePackAllowed) && $recyclablePackAllowed) or (isset($giftAllowed) && $giftAllowed)}
                <div class="row">
                    {if $recyclablePackAllowed}
                        <div class="col-xs-12">
                            <label for="recyclable">
                                <input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} class="carrier_checkbox not_unifrom not_uniform"/>
                                {l s='I agree to receive my order in recycled packaging' mod='onepagecheckoutps'}
                            </label>
                        </div>
                    {/if}
                    {if $giftAllowed}
                        <div class="col-xs-12">
                            <label for="gift">
                                <input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} class="carrier_checkbox not_unifrom not_uniform"/>
                                {l s='I would like the order to be gift-wrapped.' mod='onepagecheckoutps'}
                                &nbsp;
                                {if $gift_wrapping_price > 0}
                                    <span class="price" id="gift-price">
                                        ({l s='Additional cost of' mod='onepagecheckoutps'}
                                        {if $priceDisplay == 1}{convertPrice price=$total_wrapping_tax_exc_cost}{else}{convertPrice price=$total_wrapping_cost}{/if}
                                        {if $use_taxes}{if $priceDisplay == 1} {l s='(tax excl.)' mod='onepagecheckoutps'}{else} {l s='(tax incl.)' mod='onepagecheckoutps'}{/if}{/if})
                                    </span>
                                {/if}
                            </label>
                        </div>
                    {/if}
                </div>
            {/if}

            {if isset($giftAllowed) && $giftAllowed}
                <div class="row">
                    <div class="col-xs-12">
                        <p id="gift_div_opc" class="textarea {if $cart->gift != 1}hidden{/if}">
                            <label for="gift_message">{l s='If you wish, you can add a note to the gift:' mod='onepagecheckoutps'}</label>
                            <textarea rows="1" id="gift_message" name="gift_message" class="form-control">{$cart->gift_message|escape:'htmlall':'UTF-8'}</textarea>
                        </p>
                    </div>
                </div>
            {/if}

            <div id="HOOK_BEFORECARRIER">
                {if isset($HOOK_BEFORECARRIER)}
                    {$HOOK_BEFORECARRIER|escape:'htmlall':'UTF-8':false:true}
                {/if}
            </div>
        {/if}
    </div>
{/if}