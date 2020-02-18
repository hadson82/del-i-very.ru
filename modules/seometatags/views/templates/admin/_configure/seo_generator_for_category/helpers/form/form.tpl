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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Goryachev Dmitry    <dariusakafest@gmail.com>
* @copyright 2007-2015 Goryachev Dmitry
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/form/form.tpl"}
{block name="defaultForm"}
    <div class="help_block bs-callout bs-callout-info">
        <h3 class="title">{l s='Let\'s define those responsible for what each variable template' mod='seometatags'}</h3>
        <ul>
            <li>
                <b>
                    {literal}{category_name}{/literal}
                </b>
                - {l s='Name category' mod='seometatags'}
            </li>
            <li>
                <b>
                    {literal}{category_description}{/literal}
                </b>
                - {l s='Description category' mod='seometatags'}
            </li>
        </ul>
    </div>
    <script>
        var success_message = "{l s='Regenerate successfully!' mod='seometatags' js=true}";
    </script>
    {$smarty.block.parent}
{/block}

{block name="field"}
    {$smarty.block.parent}
    {if $input.type == 'html_smt'}
        <div class="box_loading">
            <div class="progress_bar">
                <span class="progress_percent">0%</span>
                <div style="width: 0%" class="progress_line"></div>
            </div>
        </div>
        <div style="clear: both; margin-bottom: 20px;">
        {$input.html_content|no_escape}
        </div>
    {/if}
    {if isset($input.meta_vars) && count($input.meta_vars)}
        <div class="meta_vars">
            {foreach from=$input.meta_vars item=meta_var name=mv}
                <a title="{l s='Click to insert' mod='seometatags'}" data-field-name="{$input.name|no_escape}" data-meta-var="{$meta_var|no_escape}" class="addMetaVar" href="#">{$meta_var|no_escape}</a>
                {if !$smarty.foreach.mv.last},{/if}
            {/foreach}
        </div>
    {/if}
{/block}