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

<div class="av_product_award light" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	<meta itemprop="itemreviewed" content="{$product_name|escape:'htmlall':'UTF-8'}">
	<a href="javascript:void(0)" id="AV_button">
		<div id="top">
			<div class="ratingWrapper">
				<div class="ratingInner" style="width:{$av_rate_percent|escape:'htmlall':'UTF-8'}%"></div>
			</div>
			<div id="slide">
				<b><span itemprop="ratingValue" class="ratingValue">{$av_rate|escape:'htmlall':'UTF-8'}</span> / <span itemprop="bestRating" class="bestRating">5</span></b> -  <meta itemprop="worstRating" content="1">
				<span itemprop="reviewCount" class="reviewCount">
					{$av_nb_reviews|escape:'htmlall':'UTF-8'}
				</span>
				{if $av_nb_reviews > 1}
					{l s='reviews' mod='netreviews'}
				{else}
					{l s='review' mod='netreviews'}
				{/if}
			</div>
		</div>
	</a>
</div>