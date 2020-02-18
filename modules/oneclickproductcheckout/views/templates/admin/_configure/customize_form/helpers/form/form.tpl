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
* @author    SeoSA <885588@bk.ru>
* @copyright 2012-2017 SeoSA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="field"}
    {if $input.ps_version < 1.6}
    <div class="{$input.form_group_class|escape:'quotes':'UTF-8'} v15" >
        {$smarty.block.parent}
    </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="defaultForm"}
    <div class="hint alert alert-info" style="display: block;">
        {l s='If you make the field inactive, the data on it is generated randomly when ordering.' mod='oneclickproductcheckout'}
    </div>
    {$smarty.block.parent}
{/block}


{block name="label"}
    {if $input.ps_version >= 1.6}
        {if isset($input.label)}
                <label for="{if isset($input.id)}{$input.id|intval}{if isset($input.lang) AND $input.lang}_{$current_id_lang|escape:'quotes':'UTF-8'}{/if}{else}{$input.name|escape:'quotes':'UTF-8'}{if isset($input.lang) AND $input.lang}_{$current_id_lang|escape:'quotes':'UTF-8'}{/if}{/if}" class="control-label col-xs-2 {if isset($input.required) && $input.required && $input.type != 'radio'}required{/if}">
                    {if isset($input.hint)}
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                          title="{if is_array($input.hint)}
                                                            {foreach $input.hint as $hint}
                                                                {if is_array($hint)}
                                                                    {$hint.text|escape:'quotes':'UTF-8'}
                                                                {else}
                                                                    {$hint|escape:'quotes':'UTF-8'}
                                                                {/if}
                                                            {/foreach}
                                                        {else}
                                                            {$input.hint|escape:'quotes':'UTF-8'}
                                                        {/if}">
                                                {/if}
                        {$input.label|ld}
                        {if isset($input.hint)}
                                                </span>
                    {/if}
                </label>
        {/if}
    {/if}
{/block}
{block name="field"}
    {if $input.type == 'ocpc_field'}
            <input class="position_fields" type="hidden" name="fields[{$input.name|escape:'quotes':'UTF-8'}][position]" value="{$input.position|escape:'quotes':'UTF-8'}">
            <input type="hidden" name="fields[{$input.name|escape:'quotes':'UTF-8'}][name]" value="{$input.name|escape:'quotes':'UTF-8'}">
            {if $input.ps_version < 1.6}
            <div>
                <label>{$input.label|escape:'quotes':'UTF-8'}</label>
                <div class="margin-form">
                    <span style="width: 150px; display: inline-block;">{l s='Display?' mod='oneclickproductcheckout'}</span>
                    <input type="radio" name="fields[{$input.name|escape:'quotes':'UTF-8'}][visible]" value="1" {if $input.visible == true}checked="checked"{/if}>
                    {l s='Yes' mod='oneclickproductcheckout'}
                    <input type="radio" name="fields[{$input.name|escape:'quotes':'UTF-8'}][visible]" value="0" {if $input.visible == false}checked="checked"{/if}>
                    {l s='No' mod='oneclickproductcheckout'}
                </div>
                <div class="margin-form">
                    <span style="width: 150px; display: inline-block;">{l s='Required field ?' mod='oneclickproductcheckout'}</span>
                    <input type="radio" name="fields[{$input.name|escape:'quotes':'UTF-8'}][required]" value="1" {if $input.required == true}checked="checked"{/if}>
                    {l s='Yes' mod='oneclickproductcheckout'}
                    <input type="radio" name="fields[{$input.name|escape:'quotes':'UTF-8'}][required]" value="0" {if $input.required == false}checked="checked"{/if}>
                    {l s='No' mod='oneclickproductcheckout'}
                </div>
            </div>
            {else}
                <div class="col-xs-9 col-xs-offset-3" style="margin-top: -19px; margin-bottom: 3px;">
                <span class="col-xs-2">{l s='Display?' mod='oneclickproductcheckout'}</span>
                <span class="switch prestashop-switch fixed-width-md col-xs-10 col-xs-offset-2 col-md-offset-0">
											{foreach $input.values as $value}
                                                <input
                                                        type="radio"
                                                        name="fields[{$input.name|escape:'quotes':'UTF-8'}][visible]"
                                                        {if $value.value == 1}
                                                            id="visible_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {else}
                                                            id="visible_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {/if}
                                                        value="{$value.value|escape:'quotes':'UTF-8'}"
                                                        {if $input.visible == $value.value}checked="checked"{/if}
                                                        {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                                        />
                                                <label
                                                        {if $value.value == 1}
                                                            for="visible_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {else}
                                                            for="visible_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {/if}
                                                        >
                                                    {if $value.value == 1}
                                                        {l s='Yes' mod='oneclickproductcheckout'}
                                                    {else}
                                                        {l s='No' mod='oneclickproductcheckout'}
                                                    {/if}
                                                </label>
                                            {/foreach}
                    <a class="slide-button btn"></a>
										</span>
                </div>
                <div class="col-xs-offset-3 col-xs-9" style="margin-bottom: 3px;">
                <span class="col-xs-2">{l s='Required field ?' mod='oneclickproductcheckout'}</span>
                <span class="switch prestashop-switch fixed-width-md col-xs-10 col-xs-offset-2 col-md-offset-0">
											{foreach $input.values as $value}
                                                <input
                                                        type="radio"
                                                        name="fields[{$input.name|escape:'quotes':'UTF-8'}][required]"
                                                        {if $value.value == 1}
                                                            id="required_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {else}
                                                            id="required_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {/if}
                                                        value="{$value.value|escape:'quotes':'UTF-8'}"
                                                        {if $input.required == $value.value}checked="checked"{/if}
                                                        {if isset($input.disabled) && $input.disabled}disabled="disabled"{/if}
                                                        />
                                                <label
                                                        {if $value.value == 1}
                                                            for="required_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {else}
                                                            for="required_{$value.id|escape:'quotes':'UTF-8'}"
                                                        {/if}
                                                        >
                                                    {if $value.value == 1}
                                                        {l s='Yes' mod='oneclickproductcheckout'}
                                                    {else}
                                                        {l s='No' mod='oneclickproductcheckout'}
                                                    {/if}
                                                </label>
                                            {/foreach}
                    <a class="slide-button btn"></a>
                </span>
                </div>
                <div class="col-xs-9 col-xs-offset-3">
                    <div class="row">
                        <label class="control-label col-xs-2">
                            <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Add a tooltip for this field' mod='oneclickproductcheckout'}">
                                {l s='Entry assistance' mod='oneclickproductcheckout'}
                            </span>
                        </label>
                        <div class="col-xs-4 col-xs-offset-2 col-md-offset-0">
                            {foreach $languages as $language}
                                <div class="translatable-field lang-{$language.id_lang|escape:'quotes':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                                    <input type="text"
                                           id="help_{$input.name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}"
                                           name="fields[{$input.name|escape:'html':'UTF-8'}][help][{$language.id_lang|escape:'html':'UTF-8'}]"
                                           class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}"
                                           value="{$input['help'][$language.id_lang]|escape:'html':'UTF-8'}"
                                           onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"
                                            {if isset($input.size)} size="{$input.size|escape:'html':'UTF-8'}"{/if}
                                            {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                            {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                            {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                            {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                            {if isset($input.required) && $input.required} required="required" {/if}
                                            {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
                                </div>
                            {/foreach}
                        </div>
                        <div class="col-xs-1">
                            <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                {$language.iso_code|escape:'html':'UTF-8'}
                                <i class="icon-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu">
                                {foreach from=$languages item=language}
                                    <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                                {/foreach}
                            </ul>
                        </div>
                        </div>
                </div>
                {if $input.mask_visible}
                <div class="col-xs-9 col-xs-offset-3">
                    <div class="row" style="margin-top: 4px;">
                        <label class="control-label col-xs-2">
                            <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Enter your phone mask "+9(999)999-99-99"' mod='oneclickproductcheckout'}">
                                {l s='Mask' mod='oneclickproductcheckout'}
                            </span>
                        </label>
                        <div class="col-xs-4 col-xs-offset-2 col-md-offset-0">
                            <div>
                                <input type="text"
                                       name="fields[{$input.name|escape:'html':'UTF-8'}][mask_value]"
                                       class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if}{if $input.type == 'tags'} tagify{/if}"
                                       value="{$fields_value[$input.name]['mask_value']|escape:'html':'UTF-8'}"
                                        {if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
                                        {if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
                                        {if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
                                        {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
                                        {if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
                                        {if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder|escape:'html':'UTF-8'}"{/if} />
                            </div>
                        </div>
                        <span class="note_field">{l s='Be sure to use "9"' mod='oneclickproductcheckout'}</span>
                    </div>
                </div>
                {/if}
            {/if}
    {/if}
    {$smarty.block.parent}
{/block}