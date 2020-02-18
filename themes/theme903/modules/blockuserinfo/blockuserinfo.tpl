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

<!-- Block user information module HEADER -->
<section class="blockuserinfo header-box">
	<span class="login-button">
    {if $logged}
        <a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Log out' mod='blockuserinfo'}" class="log_box"><span>{l s='Log out' mod='blockuserinfo'}</span><i class="icon-unlock"></i></a>
    {else}
        <a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Login' mod='blockuserinfo'}" class="log_box" ><span>{l s='Login' mod='blockuserinfo'}</span><i class="icon-lock"></i></a>
    {/if}
    </span>
</section>
<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="acc_box" rel="nofollow">{l s='Your Account' mod='blockuserinfo'}</a>

<section id="header_user" class="blockuserinfo-cart header-box {if $PS_CATALOG_MODE}header_user_catalog{/if}">
		{if !$PS_CATALOG_MODE}
		<div id="shopping_cart">
        <a href="{$link->getPageLink($order_process, true)|escape:'html'}" title="{l s='View my shopping cart' mod='blockuserinfo'}" rel="nofollow">
          <i class="icon-shopping-cart"></i>
                <span class="shopping_cart_title">{l s='Cart' mod='blockuserinfo'}</span>
                <span class="ajax_cart_quantity" {if $cart_qties == 0} style="display:none"{/if}>{$cart_qties}</span>
                <!--span class="ajax_cart_product_txt{if $cart_qties != 1} hidden{/if}">{l s='Product' mod='blockuserinfo'}</span>
                <span class="ajax_cart_product_txt_s{if $cart_qties < 2} hidden{/if}">{l s='Products' mod='blockuserinfo'}</span-->
                 {*<span class="price ajax_cart_total" {if $cart_qties == 0} style="display:none;"{/if}>
				{if $cart_qties > 0}
					{if $priceDisplay == 1}
						{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
						{convertPrice price=$cart->getOrderTotal(false, $blockuser_cart_flag)}
					{else}
						{assign var='blockuser_cart_flag' value='Cart::BOTH_WITHOUT_SHIPPING'|constant}
						{convertPrice price=$cart->getOrderTotal(true, $blockuser_cart_flag)}
					{/if}
				{/if}                  
                </span>*}
                <span class="ajax_cart_no_product" {if $cart_qties > 0} style="display:none;"{/if}>{l s='(empty)' mod='blockuserinfo'}</span>
                <i class="opancart icon-angle-down"></i>
            </a>
		</div>
		{/if}
</section>