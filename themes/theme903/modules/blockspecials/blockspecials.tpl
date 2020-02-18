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

<!-- MODULE Block specials -->
    <section id="specials" class="main_page_products block column_box">
	<h4><span>{l s='Specials' mod='blockspecials'}</span><i class="column_icon_toggle icon-plus-sign"></i></h4>
	<div class="block_content toggle_content">
{if $special}
		<ul>
			<li class="clearfix">
            	<div class="inner_content clearfix">
                	<div class="product_image">
                    	<a class="product_image" href="{$special.link|escape:'html'}"><img src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'home_default')|escape:'html'}" alt="{$special.legend|escape:html:'UTF-8'}" title="{$special.name|escape:html:'UTF-8'}" /></a>
                    </div>
                    <div>
                        <div class="product_content">
                            <span class="price price-new">{if !$priceDisplay}{displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}{/if}</span>
                            <span class="price-old price">{if !$priceDisplay}{displayWtPrice p=$special.price_without_reduction}{else}{displayWtPrice p=$priceWithoutReduction_tax_excl}{/if}</span>
                            <h5><a class="product_link" href="{$special.link|escape:'html'}" title="{$special.name|escape:html:'UTF-8'}">{$special.name|escape:html:'UTF-8'|truncate:60:'...'}</a></h5>
                            <p class="product_desc">{$special.description_short|strip_tags|escape:html:'UTF-8'|truncate:60:'...'}</p>    
                        </div>
                    </div>
                </div>
			</li>
		</ul>
		<a class="button_large btn btn-default" href="{$link->getPageLink('prices-drop')|escape:'html'}" title="{l s='All specials' mod='blockspecials'}">{l s='All specials' mod='blockspecials'}</a>
{else}
		<p>{l s='No product specials are available at this time.' mod='blockspecials'}</p>
{/if}
	</div>
</section>
<!-- /MODULE Block specials -->