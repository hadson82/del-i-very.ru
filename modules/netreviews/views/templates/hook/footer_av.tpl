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

<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/Product" id="av_snippets_block">
	<div id="av_snippets_left">
		<img src="{$modules_dir|escape:'htmlall':'UTF-8'}netreviews/views/img/{l s='Sceau_100_en.png' mod='netreviews'}" width="30"/>
	</div>
	<div id="av_snippets_right">
		<meta itemprop="description" content="{$product_description|escape:'htmlall':'UTF-8'|strip_tags}">
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="priceCurrency" content="EUR">
			<meta itemprop="price" content="{$product_price}">
			<link itemprop="availability" href="http://schema.org/InStock" />
		</span>
				{l s='Evaluation of' mod='netreviews'} <span itemprop="name">{$product_name|escape:'htmlall':'UTF-8'}</span> 
			<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
				<div>
					<span itemprop="ratingValue">{$average_rate|escape:'htmlall':'UTF-8'}</span>/<span itemprop="bestRating">5</span> {l s='out of' mod='netreviews'} <span itemprop="reviewCount">{$count_reviews|escape:'htmlall':'UTF-8'}</span> {l s='reviews' mod='netreviews'} 
					<div class="ratingWrapper">
						<div class="ratingInner" style="width:{$average_rate_percent|escape:'htmlall':'UTF-8'}%"></div>
					</div>
				</div>
			</div>
		</div>
</div>

