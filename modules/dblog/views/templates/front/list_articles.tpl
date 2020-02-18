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

{foreach from=$articles item=article}
    <div class="article">
        <div class="article_header">
            {if $article.preview}
                <div class="article_img">
                    <img class="img-responsive" src="{$blog_image->getImgPath($article.preview, 'category')|escape:'quotes':'UTF-8'}">
                </div>
            {/if}
            <div class="article_title"><a href="{$blog_link->getArticleLink($article.link_rewrite)|escape:'quotes':'UTF-8'}">{$article.name|escape:'quotes':'UTF-8'}</a></div>
            <div class="article_info">
                <span class="info">
                    <i class="icon-calendar"></i>
                    {$blog_tool->dateFormatTranslate($article.date_add)|escape:'quotes':'UTF-8'}
                </span>
                {if BlogConf::getConf('SHOW_WHO_CREATE_ARTICLE')}
                <span class="info">
                    <i class="icon-user"></i> {$article.employee|escape:'quotes':'UTF-8'}
                </span>
                {/if}
                {if $article.category}
                <span class="info">
                    <i class="icon-folder-open"></i>
                    <a href="{$blog_link->getCategoryLink($article.cat_link_rewrite)|escape:'quotes':'UTF-8'}">{$article.category|escape:'quotes':'UTF-8'}</a>
                </span>
                {/if}
                {if $article.is_comment}
                    <span class="info">
                        <i class="icon-comments"></i> {$article.count_comment|intval}
                    </span>
                {/if}
            </div>
        </div>
        <div class="article_body">
            {$article.content|strip_tags|nl2br|truncate:220:'...'|escape:'quotes':'UTF-8'}
        </div>
        <div class="article_footer">
            {if isset($article.tags) && count($article.tags)}
            <span class="tags">
              {foreach from=$article.tags item=tag}
                  <a href="{$blog_link->getTagLink($tag.link_rewrite)|escape:'quotes':'UTF-8'}"><i class="icon-tags"></i> {$tag.name|escape:'quotes':'UTF-8'}</a>
              {/foreach}
             </span>
            {/if}
        </div>
    </div>
{/foreach}
