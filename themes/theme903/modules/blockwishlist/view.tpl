{*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="view_wishlist">
<h1><span>{l s='Wishlist' mod='blockwishlist'}</span></h1>
{if $wishlists}
<p>
	{l s='Other wishlists of' mod='blockwishlist'} {$current_wishlist.firstname} {$current_wishlist.lastname}:
	{foreach from=$wishlists item=wishlist name=i}
		{if $wishlist.id_wishlist != $current_wishlist.id_wishlist}
			<a href="{$base_dir_ssl}modules/blockwishlist/view.php?token={$wishlist.token}" title="{$wishlist.name}" rel="nofollow">{$wishlist.name}</a>
			{if !$smarty.foreach.i.last}
				/
			{/if}
		{/if}
	{/foreach}
</p>
{/if}

<div class="wlp_bought">
	{assign var='nbItemsPerLine' value=3}
    {assign var='nbItemsPerLineTablet' value=2}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
    {math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
    <ul class="row wlp_bought_list">
        {foreach from=$products item=product name=i}
        {math equation="(total%perLine)" total=$smarty.foreach.i.total perLine=$nbItemsPerLine assign=totModulo}
        {math equation="(total%perLineT)" total=$smarty.foreach.i.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
            <li id="wlp_{$product.id_product}_{$product.id_product_attribute}" class="col-xs-12 col-sm-6 col-md-4 num-{$smarty.foreach.i.iteration}{if $smarty.foreach.i.iteration%$nbItemsPerLine == 0} last_in_line{elseif $smarty.foreach.i.iteration%$nbItemsPerLine == 1} first_in_line{/if} {if $smarty.foreach.i.iteration > ($smarty.foreach.i.total - $totModulo)}last_line{/if} {if $smarty.foreach.i.iteration%$nbItemsPerLineTablet == 0}last_item_of_tablet_line{elseif $smarty.foreach.i.iteration%$nbItemsPerLineTablet == 1}first_item_of_tablet_line{/if} {if $smarty.foreach.i.iteration > ($smarty.foreach.i.total - $totModuloTablet)}last_tablet_line{/if}">
            <div class="inner_content">
                    <div class="product_image">
                        <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html'}" title="{l s='Product detail' mod='blockwishlist'}">
                            <img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'home_default')|escape:'html'}" alt="{$product.name|escape:'htmlall':'UTF-8'}" />
                        </a>
                    </div>
                    <div class="product_infos">
                        <h4 id="s_title" class="product_name">{$product.name|truncate:30:'...'|escape:'htmlall':'UTF-8'}</h4>
                    	<span class="wishlist_product_detail">
                    {if isset($product.attributes_small)}
                        <a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite)|escape:'html'}" title="{l s='Product detail' mod='blockwishlist'}">{$product.attributes_small|escape:'htmlall':'UTF-8'}</a>
                    {/if}
                    <p class="form-group">
                        <label for="quantity_{$product.id_product}_{$product.id_product_attribute}">{l s='Quantity' mod='blockwishlist'}:</label><input class="form-control" type="text" id="quantity_{$product.id_product}_{$product.id_product_attribute}" value="{$product.quantity|intval}" size="3"  />
                    </p>   
                    <p class="form-group"> 
                        <label for="priority_{$product.id_product}_{$product.id_product_attribute}">{l s='Priority' mod='blockwishlist'}:</label>
                        <select class="form-control" id="priority_{$product.id_product}_{$product.id_product_attribute}">
                            <option value="0"{if $product.priority eq 0} selected="selected"{/if}>{l s='High' mod='blockwishlist'}</option>
                            <option value="1"{if $product.priority eq 1} selected="selected"{/if}>{l s='Medium' mod='blockwishlist'}</option>
                            <option value="2"{if $product.priority eq 2} selected="selected"{/if}>{l s='Low' mod='blockwishlist'}</option>
                        </select>
                    </p>
                    </span>
                    <a class="button_small btn btn-default" href="{$link->getProductLink($product.id_product,  $product.link_rewrite, $product.category_rewrite)|escape:'html'}" title="{l s='View' mod='blockwishlist'}" rel="nofollow">{l s='View' mod='blockwishlist'}</a>
                    {if isset($product.attribute_quantity) AND $product.attribute_quantity >= 1 OR !isset($product.attribute_quantity) AND $product.product_quantity >= 1}
                        {if !$ajax}
                            <form id="addtocart_{$product.id_product|intval}_{$product.id_product_attribute|intval}" action="{$link->getPageLink('cart')|escape:'html'}" method="post">
                                <p class="hidden">
                                    <input type="hidden" name="id_product" value="{$product.id_product|intval}" id="product_page_product_id"  />
                                    <input type="hidden" name="add" value="1" />
                                    <input type="hidden" name="token" value="{$token}" />
                                    <input type="hidden" name="id_product_attribute" id="idCombination" value="{$product.id_product_attribute|intval}" />
                                </p>
                            </form>
                        {/if}
                        <a href="javascript:;" class="exclusive btn btn-default" onclick="WishlistBuyProduct('{$token|escape:'htmlall':'UTF-8'}', '{$product.id_product}', '{$product.id_product_attribute}', '{$product.id_product}_{$product.id_product_attribute}', this, {$ajax});" title="{l s='Add to cart' mod='homefeatured'}" rel="nofollow">{l s='Add to cart' mod='blockwishlist'}</a>
                    {else}
                        <span class="exclusive">{l s='Add to cart' mod='blockwishlist'}</span>
                    {/if}
                    </div>
                </div>
            </li>
        {/foreach}
    </ul>
</div>

</div>
