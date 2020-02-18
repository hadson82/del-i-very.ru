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
<li>
	<a href="#idTabavisverifies" class="avisverifies_tab" data-toggle="tab" id="tab_avisverifies">
		{$count_reviews|escape:'htmlall':'UTF-8'}
		{if $av_nb_reviews > 1}
                    {l s='reviews' mod='netreviews'}
                {else}
                    {l s='review' mod='netreviews'}
        {/if}
	</a>
</li>