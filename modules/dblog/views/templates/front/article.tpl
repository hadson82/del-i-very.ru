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

<script>
    var api_path = "{$blog_link->getAPILink()|escape:'quotes':'UTF-8'}";
    var timer_update = null;
    var logged = {if $cookie->logged}true{else}false{/if};
    var article = "{$article->id|escape:'quotes':'UTF-8'}";
    var customers_have_admin_rights = {$customers_have_admin_rights|json_encode};
    var comment_conf = {
        avatar: '{if !$cookie->logged}{customerAvatar id_guest=$cookie->id_guest}{else}{customerAvatar id_customer=$customer->id}{/if}',
        interval_update: {if BlogConf::getConf('INTERVAL_UPDATE') > 5000}{BlogConf::getConf('INTERVAL_UPDATE')|intval}{else}5000{/if},
        logged: {if $cookie->logged}true{else}false{/if}
    };
</script>
<div id="view_article" class="custom_responsive">
    <div class="article_header">
        {if is_array($images) && count($images)}
            <div class="fotorama" data-nav="thumbs" data-width="100%">
                {if $article->preview}
                    <a href="{$blog_image->getImgPath($article->preview, 'article')|escape:'quotes':'UTF-8'}">
                        <img src="{$blog_image->getImgPath($article->preview, 'gallery_thumb')|escape:'quotes':'UTF-8'}">
                    </a>
                {/if}
                {foreach from=$images item=image}
                    <a href="{$blog_image->getImgPath($image.id_blog_image, 'article')|escape:'quotes':'UTF-8'}">
                        <img src="{$blog_image->getImgPath($image.id_blog_image, 'gallery_thumb')|escape:'quotes':'UTF-8'}">
                    </a>
                {/foreach}
            </div>
        {else}
            {if $article->preview}
                <div class="article_preview">
                    <img class="img-responsive" src="{$blog_image->getImgPath($article->preview, 'article')|escape:'quotes':'UTF-8'}">
                </div>
            {/if}
        {/if}
        <h1 class="article_title">{$article->name|escape:'quotes':'UTF-8'}</h1>
    </div>
    <div class="article_content">
        {if $article->content|strlen}
            <div class="article_desc">
                {$article->content|escape:'quotes':'UTF-8'}
            </div>
        {/if}
    </div>
    <div class="article_footer">
        <div class="row_info">
            {if $article->view_share_btn}
                <span class="addthisshare">
                    {if $share.FB}<a title="{l s='Share in facebook' mod='dblog'}" href="#" onClick="javascript:window.open('http://api.addthis.com/oexchange/0.8/forward/facebook/offer?url={$link_share|escape:'quotes':'UTF-8'}','addthisshare','status=no,toolbar=no, menubar=no,scrollbars=yes,resizable=yes'); return false;" rel="nofollow"><img src="{$modules_dir|escape:'quotes':'UTF-8'}dblog/views/img/fb.png"></a>{/if}
                    {if $share.VK}<a title="{l s='Share in vkontakte' mod='dblog'}" href="#" onClick="javascript:window.open('http://api.addthis.com/oexchange/0.8/forward/vk/offer?url={$link_share|escape:'quotes':'UTF-8'}','addthisshare','status=no,toolbar=no, menubar=no,scrollbars=yes,resizable=yes'); return false;" rel="nofollow"><img src="{$modules_dir|escape:'quotes':'UTF-8'}dblog/views/img/vk.png"></a>{/if}
                    {if $share.TW}<a title="{l s='Share in twitter' mod='dblog'}" href="#" onClick="javascript:window.open('http://api.addthis.com/oexchange/0.8/forward/twitter/offer?url={$link_share|escape:'quotes':'UTF-8'}','addthisshare','status=no,toolbar=no, menubar=no,scrollbars=yes,resizable=yes'); return false;" rel="nofollow"><img src="{$modules_dir|escape:'quotes':'UTF-8'}dblog/views/img/tw.png"></a>{/if}
                    {if $share.OD}<a title="{l s='Share in odnoklassniki' mod='dblog'}" href="#" onClick="javascript:window.open('http://api.addthis.com/oexchange/0.8/forward/odnoklassniki_ru/offer?url={$link_share|escape:'quotes':'UTF-8'}','addthisshare','status=no,toolbar=no, menubar=no,scrollbars=yes,resizable=yes'); return false;" rel="nofollow"><img src="{$modules_dir|escape:'quotes':'UTF-8'}dblog/views/img/od.png"></a>{/if}
                </span>
            {/if}
            <span class="info">
            <i class="icon-calendar"></i>
                {$blog_tool->dateFormatTranslate($article->date_add)|escape:'quotes':'UTF-8'}
        </span>
            {if BlogConf::getConf('SHOW_WHO_CREATE_ARTICLE')}
                <span class="info">
                <i class="icon-user"></i> {$employee->firstname|escape:'quotes':'UTF-8'} {$employee->lastname|escape:'quotes':'UTF-8'}
            </span>
            {/if}
            {if $article->id_blog_category}
                <span class="info">
                <i class="icon-folder-open"></i>
                <a href="{$blog_link->getCategoryLink($category->link_rewrite)|escape:'quotes':'UTF-8'}">{$category->name|escape:'quotes':'UTF-8'}</a>
            </span>
            {/if}
            {if $article->is_comment}
                <span class="info">
                <i class="icon-comments"></i> {$total_comment|intval}
            </span>
            {/if}
        </div>
        {if $tags && count($tags)}
        <div class="row_tags">
            <span class="tags">
                {foreach from=$tags item=tag}
                    <a href="{$blog_link->getTagLink($tag.link_rewrite)|escape:'quotes':'UTF-8'}"><i class="icon-tags"></i> {$tag.name|escape:'quotes':'UTF-8'}</a>
                {/foreach}
            </span>
        </div>
        {/if}
    </div>
    {if $products && count($products)}
        <div class="article_products">
            {include file="./product-list.tpl" products=$products}
        </div>
    {/if}
    {if $article->is_comment}
        {include file="./list_comments.tpl" comments=$comments}
    {/if}
</div>