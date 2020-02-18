{if isset($articles) && is_array($articles) && count($articles)}
    <div class="block block_left">
        <div class="title_block">
            {*<i class="icon-folder-open"></i>*}
            <h2><a href="{$blog_link->getBlogLink()|escape:'quotes':'UTF-8'}">{l s='Новости' mod='dblog'}</a></h2>
        </div>
        <div class="block_content">
            {foreach from=$articles item=article}
                <div class="article">
                    <div class="article_date">
                        {$blog_tool->dateFormatTranslate($article.date_add)|escape:'quotes':'UTF-8'}
                    </div>
                    <div class="article_title"><h3><a href="{$blog_link->getArticleLink($article.link_rewrite)|escape:'quotes':'UTF-8'}">{$article.name|escape:'quotes':'UTF-8'}</a></h3></div>
                    {if $article.preview}
                        <div class="article_image">
                            <a href="{$blog_link->getArticleLink($article.link_rewrite)|escape:'quotes':'UTF-8'}">
                                <img class="article_img img-responsive" src="{$blog_image->getImgPath($article.preview, 'home')|escape:'quotes':'UTF-8'}">
                            </a>
                        </div>
                    {/if}
                    <div class="article_content">
                        {$article.content|truncate:'128':''}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}