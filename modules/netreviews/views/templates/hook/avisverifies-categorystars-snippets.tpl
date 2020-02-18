<!--
* 2012-2017 NetReviews
*
*  @author    NetReviews SAS <contact@avis-verifies.com>
*  @copyright 2017 NetReviews SAS
*  @version   Release: $Revision: 7.3.2
*  @license   NetReviews
*  @date      28/03/2017
*  International Registered Trademark & Property of NetReviews SAS
-->

{if isset($av_rate) && !empty($av_rate)}
	<div class="av_category_stars" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<meta itemprop="itemreviewed" content="{$product_name|escape:'htmlall':'UTF-8'}"/>
		<meta itemprop="ratingValue" content="{$average_rate|escape:'htmlall':'UTF-8'}"/>
		<meta itemprop="bestRating" content="5"/>
        <div class="ratingWrapper">
            <div class="ratingInner" style="width:{$av_rate_percent|escape:'htmlall':'UTF-8'}%"></div>
        </div>  
    	<a href="{$link_product|escape:'htmlall':'UTF-8'}" title="{$av_nb_reviews|escape:'htmlall':'UTF-8'} {if $av_nb_reviews > 1}{l s='reviews' mod='netreviews'}{else}{l s='review' mod='netreviews'}{/if}">
           <span itemprop="reviewCount"> {$av_nb_reviews|escape:'htmlall':'UTF-8'}</span>
            {if $av_nb_reviews > 1}
                {l s='reviews' mod='netreviews'}
            {else}
                {l s='review' mod='netreviews'}
            {/if}
    	</a>
    </div>
{/if}
