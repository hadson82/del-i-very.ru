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

<div class="comment {if isset($visible) && !$visible}hidden{/if} {if in_array($comment.id_customer, $customers_have_admin_rights)}comment_admin{/if}" data-comment-id="{$comment.id_blog_comment_article|escape:'quotes':'UTF-8'}">
    <div data-comment="{$comment.id_blog_comment_article|intval}"
         {if $comment.parent_id}data-parent-id="{$comment.parent_id|intval}"{/if}
         data-customer="{$comment.id_customer|intval}"
         data-customer-name="@{$comment.firstname|escape:'quotes':'UTF-8'}"
         class="row comment_detail {if !$comment.is_moderated}no_moderated{/if} {if isset($ajax) && $ajax && $comment.id_customer != $cookie->id_customer}no_viewed{/if}">
        <div class="comment_avatar col-md-1 col-sm-2 col-xs-2">
            <img class="img-responsive" src="{customerAvatar id_customer=$comment.id_customer}">
        </div>
        <div class="col-md-11 col-sm-10 col-xs-10">
            <div class="customer_name">{$comment.firstname|escape:'quotes':'UTF-8'}</div>
            <div class="message {if !$comment.is_moderated}after_moderation{/if}">
                {if $comment.answer_id_customer}<span class="message_customer {if in_array($comment.answer_id_customer, $customers_have_admin_rights)}message_admin_customer{/if}">{$comment.answer_firstname|escape:'quotes':'UTF-8'},</span> {/if} {$comment.message|linkWrapper}
            </div>
            <div class="comment_info">
                <span>{$blog_tool->dateFormatTranslate($comment.date_add, 'd M Y H:i:s')|escape:'quotes':'UTF-8'}</span>
                {if $comment.is_moderated} | <a onclick="Comment.answer(this);">{l s='Answer' mod='dblog'}</a>{/if}
                {*{if $cookie->logged && $comment.id_customer == $cookie->id_customer}*}
                {if $cookie->logged && in_array($cookie->id_customer, $customers_have_admin_rights)}
                    | <a onclick="Comment.delete(this);">{l s='Delete' mod='dblog'}</a>
                {/if}
                {if !$comment.is_moderated}
                    <div class="info_moderation">
                        ({l s='Will be available after moderation' mod='dblog'})
                    </div>
                {/if}
            </div>
        </div>
    </div>
    {if isset($comment.children) && !$child}
        <div class="children_list_comment" id="children_{$comment.id_blog_comment_article|intval}">
        {if count($comment.children)}
            {if $comment.nb_children > $limit_view}
                <a data-parent-id="{$comment.id_blog_comment_article|intval}" class="view_more {if $comment.nb_children <= $limit_view}hidden{/if}" href="#">{l s='More' mod='dblog'}(<span class="view_count">{($comment.nb_children-$limit_view)|intval}</span>)</a>
            {/if}
            {foreach from=$comment.children item=child_item name=comment}
                {*{if $smarty.foreach.comment.iteration > $limit_view}*}
                    {*{assign var=visible value=false}*}
                {*{else}*}
                    {assign var=visible value=true}
                {*{/if}*}
                {include file="./comment.tpl" comment=$child_item child=true visible=$visible}
            {/foreach}
        {/if}
        </div>
    {/if}
</div>