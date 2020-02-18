{*
* 2007-2015 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
<div class="home_categories col-xs-12">
    <h2 hidden>{l s='Categories' mod='homecategories'}</h2>
    {if isset($categories) AND $categories}
        <div id="subcategories" style="border: none;">
            <ul class="clearfix">
                {foreach from=$categories item=subcategory name=homeCategories}
                    <li class="col-xs-12">
                        <div class="subcategory-image" hidden>
                            <a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}"
                               title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                                {if $subcategory.id_image}
                                    <img class="replace-2x"
                                         src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'medium_default')|escape:'html':'UTF-8'}"
                                         alt="" width="{$mediumSize.width|escape:'htmlall':'UTF-8'}" height="{$mediumSize.height|escape:'htmlall':'UTF-8'}"/>
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir|escape:'htmlall':'UTF-8'}{$lang_iso|escape:'htmlall':'UTF-8'}-default-medium_default.jpg"
                                         alt="" width="{$mediumSize.width|escape:'htmlall':'UTF-8'}" height="{$mediumSize.height|escape:'htmlall':'UTF-8'}"/>
                                {/if}
                            </a>
                        </div>
                        <h5><div class="homecategory">
							<a class="subcategory-name"
                               href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:35:'...'|escape:'html':'UTF-8'}</a>
							</div>
                        </h5>
                    </li>
                {/foreach}
            </ul class="clearfix">
        </div>
    {else}
        <p>{l s='No categories' mod='homecategories'}</p>
    {/if}
    <div class="cr"></div>
</div>
<!-- /MODULE Home categories -->