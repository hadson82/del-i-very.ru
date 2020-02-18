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

{if isset($p) AND $p}
    <div>
        {if $start!=$stop}
            <ul class="pagination">
                {if $p != 1}
                    {assign var='p_previous' value=$p-1}
                    <li class="pagination_previous">
                        <a href="{$blog_link->getPaginationLink($request_link, $p_previous)|escape:'quotes':'UTF-8'}" rel="prev">
                            <i class="icon-chevron-left"></i> <b>{l s='Previous' mod='dblog'}</b>
                        </a>
                    </li>
                {/if}
                {if $start==($range + 1)}
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, 1)|escape:'quotes':'UTF-8'}">
                            <span>1</span>
                        </a>
                    </li>
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, 2)|escape:'quotes':'UTF-8'}">
                            <span>2</span>
                        </a>
                    </li>
                {/if}
                {if $start==$range}
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, 1)|escape:'quotes':'UTF-8'}">
                            <span>1</span>
                        </a>
                    </li>
                {/if}
                {if $start>($range + 1)}
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, 1)|escape:'quotes':'UTF-8'}">
                            <span>1</span>
                        </a>
                    </li>
                    <li class="truncate">
						<span>
							<span>...</span>
						</span>
                    </li>
                {/if}
                {section name=pagination start=$start loop=$stop+1 step=1}
                    {if $p == $smarty.section.pagination.index}
                        <li class="active current">
							<span>
								<span>{$p|escape:'html':'UTF-8'}</span>
							</span>
                        </li>
                    {else}
                        <li>
                            <a href="{$blog_link->getPaginationLink($request_link, $smarty.section.pagination.index)|escape:'quotes':'UTF-8'}">
                                <span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
                            </a>
                        </li>
                    {/if}
                {/section}
                {if $nb_pages>($stop+$range)}
                    <li class="truncate">
						<span>
							<span>...</span>
						</span>
                    </li>
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, $nb_pages)|escape:'quotes':'UTF-8'}">
                            <span>{$nb_pages|intval}</span>
                        </a>
                    </li>
                {/if}
                {if $nb_pages==($stop+($range -1))}
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, $nb_pages)|escape:'quotes':'UTF-8'}">
                            <span>{$nb_pages|intval}</span>
                        </a>
                    </li>
                {/if}
                {if $nb_pages==($stop+$range)}
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, $nb_pages-1)|escape:'quotes':'UTF-8'}">
                            <span>{$nb_pages-1|intval}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{$blog_link->getPaginationLink($request_link, $nb_pages)|escape:'quotes':'UTF-8'}">
                            <span>{$nb_pages|intval}</span>
                        </a>
                    </li>
                {/if}
                {if $nb_pages > 1 AND $p != $nb_pages}
                    {assign var='p_next' value=$p+1}
                    <li class="pagination_next">
                        <a href="{$blog_link->getPaginationLink($request_link, $p_next)|escape:'quotes':'UTF-8'}" rel="next">
                            <b>{l s='Next' mod='dblog'}</b> <i class="icon-chevron-right"></i>
                        </a>
                    </li>
                {/if}
            </ul>
        {/if}
    </div>
{/if}