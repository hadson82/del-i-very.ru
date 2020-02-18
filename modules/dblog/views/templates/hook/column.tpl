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

{if isset($tags) && is_array($categories) && count($categories)}
    <div class="block">
        <div class="title_block">
            <i class="icon-folder-open"></i>
            {l s='Categories' mod='dblog'}
        </div>
        <div class="block_content list-block">
            <ul>
                {foreach from=$categories item=category}
                    <li>
                        <a href="{$blog_link->getCategoryLink($category.link_rewrite)|escape:'quotes':'UTF-8'}">{$category.name|escape:'quotes':'UTF-8'}</a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}

{if isset($tags) && is_array($tags) && count($tags)}
<div class="block block_tags">
    <div class="title_block">
        <i class="icon-tags"></i>
        {l s='Tags' mod='dblog'}
    </div>
    <div class="block_content tagcloud">
        {foreach from=$tags item=tag}
            <a href="{$blog_link->getTagLink($tag.link_rewrite)|escape:'quotes':'UTF-8'}"><i class="icon-tags"></i> {$tag.name|escape:'quotes':'UTF-8'}</a>
        {/foreach}
    </div>
</div>
{/if}