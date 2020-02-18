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

{if is_array($products) && count($products)}
    <ul class="row">
        {foreach from=$products item=product}
            <li class="col-md-3">
                <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                    <img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'quotes':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'quotes':'UTF-8'}{else}{$product.name|escape:'quotes':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'quotes':'UTF-8'}{else}{$product.name|escape:'quotes':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width|escape:'quotes':'UTF-8'}" height="{$homeSize.height|escape:'quotes':'UTF-8'}"{/if} itemprop="image" />
                </a>
                <h5 itemprop="name">
                    {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
                    <a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
                        {$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
                    </a>
                </h5>
                {if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                    <div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                            <span itemprop="price" class="price product-price">
                                {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                </span>
                            <meta itemprop="priceCurrency" content="{$currency->iso_code|escape:'quotes':'UTF-8'}" />
                            {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                <span class="old-price product-price">
                                        {displayWtPrice p=$product.price_without_reduction}
                                    </span>
                                {if $product.specific_prices.reduction_type == 'percentage'}
                                    <span class="price-percent-reduction">-{($product.specific_prices.reduction * 100)|escape:'quotes':'UTF-8'}%</span>
                                {/if}
                            {/if}
                        {/if}
                    </div>
                {/if}
            </li>
        {/foreach}
    </ul>
{/if}