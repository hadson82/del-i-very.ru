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

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<div class="panel col-lg-12">
<div class="panel-heading">
    {$title|escape:'quotes':'UTF-8'}
</div>
<div class="table-responsive clearfix">
    <div class="form-group"><b>{l s='Customer' mod='dblog'}:</b> {$comment.customer|escape:'quotes':'UTF-8'}</div>
    <div class="form-group"><b>{l s='Date add' mod='dblog'}:</b> {$comment.date_add|escape:'quotes':'UTF-8'}</div>
    <div class="form-group"><b>{l s='Article' mod='dblog'}:</b> <a target="_blank" href="{$blog_link->getArticleLink($comment.article_link_rewrite)|escape:'quotes':'UTF-8'}">{$comment.article|escape:'quotes':'UTF-8'}</a></div>
    <div class="form-group"><b>{l s='Message' mod='dblog'}:</b></div>
    <div class="form-group">
        {$comment.message|linkWrapper|escape:'quotes':'UTF-8'}
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
    </div>
</div>
</div>
    {if $show_toolbar}
        <div class="panel-footer" id="toolbar-footer">
            {foreach from=$toolbar_btn item=btn key=k}
                {if $k != 'modules-list'}
                    <a id="desc-{$table|escape:'quotes':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'quotes':'UTF-8'}{else}{$k|escape:'quotes':'UTF-8'}{/if}" class="btn btn-default{if $k=='save' || $k=='save-and-stay'} pull-right{/if}" href="{if isset($btn.href)}{$btn.href|escape:'quotes':'UTF-8'}{else}#{/if}" {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js|escape:'quotes':'UTF-8'}"{/if}>
                        <i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'quotes':'UTF-8'}{else}{$k|escape:'quotes':'UTF-8'}{/if} {if isset($btn.class)}{$btn.class|escape:'quotes':'UTF-8'}{/if}" ></i> <span {if isset($btn.force_desc) && $btn.force_desc == true } class="locked" {/if}>{$btn.desc|escape:'quotes':'UTF-8'}</span>
                    </a>
                {/if}
            {/foreach}

            <script language="javascript" type="text/javascript">
                //<![CDATA[
                var submited = false

                //get reference on save link
                btn_save = $('#desc-{$table|escape:'quotes':'UTF-8'}-save');

                //get reference on form submit button
                btn_submit = $('#{$table|escape:'quotes':'UTF-8'}_form_submit_btn');

                if (btn_save.length > 0 && btn_submit.length > 0)
                {
                    //get reference on save and stay link
                    btn_save_and_stay = $('#desc-{$table|escape:'quotes':'UTF-8'}-save-and-stay');

                    //get reference on current save link label
                    lbl_save = $('#desc-{$table|escape:'quotes':'UTF-8'}-save');

                    //override save link label with submit button value
                    if (btn_submit.html().length > 0)
                        lbl_save.find('span').html(btn_submit.html());

                    if (btn_save_and_stay.length > 0)
                    {
                        //get reference on current save link label
                        lbl_save_and_stay = $('#desc-{$table|escape:'quotes':'UTF-8'}-save-and-stay');

                        //override save and stay link label with submit button value
                        if (btn_submit.html().length > 0 && lbl_save_and_stay && !lbl_save_and_stay.hasClass('locked'))
                            lbl_save_and_stay.find('span').html(btn_submit.html() + " {l s='and stay' mod='dblog'} ");
                    }

                    //hide standard submit button
                    btn_submit.hide();
                    //bind enter key press to validate form
                    $('#{$table|escape:'quotes':'UTF-8'}_form').find('input').keypress(function (e) {
                        if (e.which == 13 && e.target.localName != 'textarea' && !$(e.target).parent().hasClass('tagify-container'))
                            $('#desc-{$table|escape:'quotes':'UTF-8'}-save').click();
                    });
                    //submit the form
                    {block name=formSubmit}
                    btn_save.click(function() {
                        // Avoid double click
                        if (submited)
                            return false;
                        submited = true;

                        if ($(this).attr('href').replace('#', '').replace(/\s/g, '') != '')
                            return true;

                        //add hidden input to emulate submit button click when posting the form -> field name posted
                        btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'" value="1" />');

                        $('#{$table|escape:'quotes':'UTF-8'}_form').submit();
                        return false;
                    });

                    if (btn_save_and_stay)
                    {
                        btn_save_and_stay.click(function() {
                            if ($(this).attr('href').replace('#', '').replace(/\s/g, '') != '')
                                return true;

                            //add hidden input to emulate submit button click when posting the form -> field name posted
                            btn_submit.before('<input type="hidden" name="'+btn_submit.attr("name")+'AndStay" value="1" />');

                            $('#{$table|escape:'quotes':'UTF-8'}_form').submit();
                            return false;
                        });
                    }
                    {/block}
                }
                //]]>
            </script>
        </div>
    {/if}
{/block}
