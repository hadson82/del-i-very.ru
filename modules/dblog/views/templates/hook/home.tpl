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

{if isset($articles) && is_array($articles) && count($articles)}
	<div id="blog_home">
    <div class="block_title">
         <h4>Новости</h4>
     </div>
    <div class="block_content">
                {foreach from=$articles item=article}
                    <div class="article">
                        <div class="article_date">
                            {$blog_tool->dateFormatTranslate($article.date_add)|escape:'quotes':'UTF-8'}
                        </div>
                        <div class="article_title"><h3><a href="{$blog_link->getArticleLink($article.link_rewrite)|escape:'quotes':'UTF-8'}">{$article.name|escape:'quotes':'UTF-8'}</a></h3></div>
							<div class="row">
							{if $article.preview}
								<div class="article_image col-xs-12 col-md-6">
									<a href="{$blog_link->getArticleLink($article.link_rewrite)|escape:'quotes':'UTF-8'}">
										<img class="article_img img-responsive" src="{$blog_image->getImgPath($article.preview, 'home')|escape:'quotes':'UTF-8'}">
									</a>
								</div>
								{/if}
								<div class="article_content col-xs-12 col-md-6">
									{$article.content|truncate:'256':'.'}
								</div>
							</div>
                    </div>
    </div>
                {/foreach}

	<div class="block_title">
		<a href="/content/10-otzyvy.html"><h4>{l s='Отзывы' mod='homefeatured'}</h4></a>
	</div>
			<div class="row" style="margin: 10px 0;">
				<div id="hypercomments_mix"></div>
				{literal}
				<script type="text/javascript">
				_hcwp = window._hcwp || [];
				_hcwp.push({widget:"Mixstream", widget_id:81093, filter:"last", limit:3});
				(function() {
				if("HC_LOAD_INIT" in window)return;
				HC_LOAD_INIT = true;
				var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage ||  "en").substr(0, 2).toLowerCase();
				var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
				hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/81093/"+lang+"/widget.js";
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(hcc, s.nextSibling);
				})();
				</script>
				{/literal}
			</div>
<script>
    $('.article_carousel').owlCarousel({
        items: 3,
        pagination: false,
        navigation: true,
        navigationText: ['<i class="icon-angle-left"></i>', '<i class="icon-angle-right"></i>']
    });
</script>
{/if}
</div>