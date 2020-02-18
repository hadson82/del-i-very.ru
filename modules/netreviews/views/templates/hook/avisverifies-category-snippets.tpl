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

{if $count_reviews != 0}
    <div id="netreviews_category_review" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <meta itemprop="itemreviewed" content="{$nom_category|escape:'htmlall':'UTF-8'}" />
            <span>
                <meta itemprop="ratingValue" content="{$average_rate|escape:'htmlall':'UTF-8'}" />
                <meta itemprop="bestRating" content="5" />
                <meta itemprop="worstRating" content="1" />
                <meta itemprop="reviewCount" content="{$count_reviews|escape:'htmlall':'UTF-8'}" />
            </span>
    </div>
{/if}





