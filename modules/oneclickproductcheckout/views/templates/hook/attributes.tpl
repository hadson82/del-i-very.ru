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

{if count($attributes)}
    <div id="attributes_ocpc">
        {foreach from=$attributes item=group}
            <div class="form_group">
            <label class="attribute_label">
                {foreach from=$groups item=for_label}
                    {if $for_label.id_attribute == $group.id_attribute && $for_label.id_product_attribute == $group.id_product_attribute}
                        {$for_label.public_group_name|escape:'quotes':'UTF-8'}:
                    {/if}
                {/foreach}
            </label>
            {foreach from=$group key=id_attribute_group item=value}
                {if $id_attribute_group == 'is_color_group' && !$value}
                    <div class="attr_value">
                        {$group.attribute_name|escape:'quotes':'UTF-8'}
                    </div>
                {elseif $id_attribute_group == 'is_color_group' && $value}
                    {assign var='img_color_exists' value=file_exists($col_img_dir|cat:$group.id_attribute|cat:'.jpg')}
                    {foreach from=$groups key=id_group item=value_group}
                        {if  $group.id_product_attribute == $value_group.id_product_attribute && $group.id_attribute == $value_group.id_attribute}
                            {if !$img_color_exists}
                                <div class="attr_color" style="background-color: {$value_group.attribute_color|escape:'quotes':'UTF-8'};">
                                    {*{$group.attribute_name}*}
                                </div>
                            {/if}
                            {if $img_color_exists && $group.id_product_attribute == $value_group.id_product_attribute}
                                <img src="{$img_col_dir|escape:'quotes':'UTF-8'}{$group.id_attribute|intval}.jpg" />
                            {/if}
                        {/if}
                    {/foreach}
                {/if}
            {/foreach}
            </div>
        {/foreach}
    </div>
{/if}