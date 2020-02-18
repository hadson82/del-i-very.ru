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

<div id="article_comments">
    <div data-form-comment class="form_add_comment row">
        <div class="col-md-1 col-sm-2 col-xs-2 comment_avatar">
            <div class="wrapp_input_file">
                {if !$cookie->logged}
                    <img class="img-responsive" src="{customerAvatar id_guest=$cookie->id_guest}">
                {else}
                    <img class="img-responsive" src="{customerAvatar id_customer=$customer->id}">
                {/if}
                <input type="file" class="upload_avatar">
            </div>
        </div>
        <div class="col-md-11 col-sm-10 col-xs-10">
            <input type="hidden" name="parent_id" value="0">
            <input type="hidden" name="id_blog_article" value="{$article->id|intval}">
            <input type="hidden" name="answer_id_customer" value="0">
            {if !$cookie->logged}
                <div data-create-account class="form-group">
                    <div class="auth_before_message">
                        {l s='Leave a comment can only registered users. By specifying an email, your name and the comment you pass a simple registration. On your specified email password will be sent from your account. If you already have an account then' mod='dblog'}
                        <a class="comment_login" href="#">
                            {l s='login' mod='dblog'}
                        </a>
                    </div>
                </div>
                <div data-create-account class="form-group">
                    <label>{l s='Email' mod='dblog'}<sup class="required">*</sup></label>
                    <div>
                        <input type="text" name="email">
                    </div>
                </div>
                <div data-create-account class="form-group">
                    <label>{l s='Your name' mod='dblog'}<sup class="required">*</sup></label>
                    <div>
                        <input type="text" name="firstname">
                    </div>
                </div>
            {/if}
            <div  class="form-group">
                {if !$cookie->logged}
                    <label data-create-account>{l s='Message' mod='dblog'}<sup class="required">*</sup></label>
                {/if}
                <div>
                    <div contenteditable="true" name="message"></div>
                </div>
            </div>
            <div class="form-group">
                <div>
                    <button type="button" class="btn_add" onclick="Comment.addMain(this)">
                        {l s='Send' mod='dblog'}
                    </button>
                </div>
            </div>
        </div>
    </div>
    {if $comments && count($comments)}
        <div class="list_comments">
            {foreach from=$comments item=comment}
                {include file="./comment.tpl" comment=$comment child=false}
            {/foreach}
        </div>
    {else}
        <div class="list_comments">
        </div>
        <div class="no_comments">{l s='No comments. But you can leave his by first.' mod='dblog'}</div>
    {/if}
    {if $nb_comment > $limit_view}
        <a class="view_more {if $nb_comment <= $limit_view}hidden{/if}" href="#">{l s='More' mod='dblog'}(<span class="view_count">{($nb_comment-$limit_view)|escape:'quotes':'UTF-8'}</span>)</a>
    {/if}
</div>


<div id="template_form_add_comment_child" style="display: none">
    <div data-form-comment class="form_add_comment_child">
        <input type="hidden" name="parent_id" value="%parent_id%">
        <input type="hidden" name="id_blog_article" value="%id_blog_article%">
        <input type="hidden" name="answer_id_customer" value="%answer_id_customer%">
        <div class="form-group">
            <div class="col-md-1 col-sm-2 col-xs-2 comment_avatar">
                <div class="wrapp_input_file">
                    <img class="img-responsive" t-src="%avatar%">
                    <input type="file" class="upload_avatar">
                </div>
            </div>
            <div class="col-md-11 col-sm-10 col-xs-10">
                {if !$cookie->logged}
                    <div data-create-account class="auth_before_message form-group">
                        {l s='Leave a comment can only registered users. By specifying an email, your name and the comment you pass a simple registration. On your specified email password will be sent from your account. If you already have an account then' mod='dblog'}
                        <a class="comment_login" href="#">
                            {l s='login' mod='dblog'}
                        </a>
                    </div>
                    <div data-create-account class="form-group">
                        <label>{l s='Email' mod='dblog'}<sup class="required">*</sup></label>
                        <div>
                            <input type="text" name="email">
                        </div>
                    </div>
                    <div data-create-account class="form-group">
                        <label>{l s='Your name' mod='dblog'}<sup class="required">*</sup></label>
                        <div>
                            <input type="text" name="firstname">
                        </div>
                    </div>
                    <label data-create-account>{l s='Message' mod='dblog'}<sup class="required">*</sup></label>
                {/if}
                <div name="message" contenteditable="true">
                    %answer_customer%
                </div>
            </div>
        </div>
        <div class="form-group">
           <div class="col-md-12">
               <button type="button" onclick="Comment.cancel(this);" class="btn_cancel">
                   {l s='Cancel' mod='dblog'}
               </button>
               <button type="button" onclick="Comment.add(this);" class="btn_add">
                   {l s='Send' mod='dblog'}
               </button>
           </div>
        </div>
    </div>
</div>

<div id="template_popups_comment">
    <div class="comment_stage_popup" style="display: none"></div>
    <div class="comment_popup comment_popup_login" style="display: none">
        <div class="popup_heading">
            {l s='Login' mod='dblog'}
            <a href="#" class="comment_popup_close">
                <i class="icon-remove"></i>
            </a>
        </div>
        <div class="popup_body">
            <div class="form-group">
               <label class="field_label">
                   {l s='Email' mod='dblog'}<sup class="required">*</sup>
               </label>
               <div class="field_input">
                   <input type="text" name="email">
               </div>
            </div>
            <div class="form-group">
                <label class="field_label">
                    {l s='Password' mod='dblog'}<sup class="required">*</sup>
                </label>
                <div class="field_input">
                    <input type="password" name="passwd">
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="click_login popup_btn_submit">
                    {l s='Login' mod='dblog'}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        Comment.init();
    });
</script>
