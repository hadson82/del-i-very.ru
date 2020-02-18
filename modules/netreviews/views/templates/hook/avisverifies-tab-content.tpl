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
<div class="tab-pane tab_media" id="idTabavisverifies">
{literal}
	<style type="text/css">
		.groupAvis{
			display: none;
		}
	</style>
{/literal}
	<div id="headerAV">{l s='Product Reviews' mod='netreviews'}</div>
	<div id="under-headerAV"  style="background: url({$modules_dir|escape:'htmlall':'UTF-8'}netreviews/views/img/{l s='Sceau_100_en.png' mod='netreviews'}) no-repeat #f1f1f1;background-size:45px 45px;background-repeat:no-repeat;">
	   <ul id="aggregateRatingAV">
	      <li>
	         <b>
	         {l s='Number of Reviews' mod='netreviews'}
	         </b> : {$count_reviews|escape:'htmlall':'UTF-8'}
	      </li>
	      <li>
	         <b>{l s='Average Grade' mod='netreviews'}</b> : {$average_rate|floatval} /5 
	         <div class="ratingWrapper">
	            <div class="ratingInner" style="width:{$average_rate_percent|escape:'htmlall':'UTF-8'}%"></div>
	         </div>
	      </li>
	   </ul>
	   <ul id="certificatAV">
	      <li><a href="{$url_certificat|escape:'htmlall':'UTF-8'}" target="_blank" class="display_certificat_review" >{l s='Show the attestation of Trust' mod='netreviews'}</a></li>
	   </ul>
	   <div class="clear"></div>
	</div>

	<div id="ajax_comment_content">

		{assign var = 'i' value = 1}
		{assign var = 'first' value = true}

		{foreach from=$reviews key=k_review item=review}
			{if $i == 1 && !$first}
				<span class="groupAvis">
			{/if}
			<div class="reviewAV">
				<ul class="reviewInfosAV">
					<li style="text-transform:capitalize">{$review['customer_name']|escape:'htmlall':'UTF-8'}</li>
					<li>&nbsp;{l s='the' mod='netreviews'} {$review['horodate']|escape:'htmlall':'UTF-8'}</li>
					<li class="rateAV"><img src="{$modules_dir|escape:'htmlall':'UTF-8'}netreviews/views/img/etoile{$review['rate']|escape:'htmlall':'UTF-8'}.png" width="80" height="15" /> {$review['rate']|escape:'htmlall':'UTF-8'}/5</li>
				</ul>

				<div class="triangle-border top">{$review['avis']|escape:'htmlall':'UTF-8'}</div>

			{if $review['discussion']}
				{foreach from=$review['discussion'] key=k_discussion item=discussion}

				<div class="triangle-border top answer" {if $k_discussion > 0} review_number={$review['id_product_av']|escape:'htmlall':'UTF-8'} style= "display: none" {/if}>

					<span>&rsaquo; {l s='Comment from' mod='netreviews'}  <b style="text-transform:capitalize;">{$discussion['origine']|escape:'htmlall':'UTF-8'}</b> {l s='the' mod='netreviews'} {$discussion['horodate']|escape:'htmlall':'UTF-8'}</span>
					<p class="answer-bodyAV">{$discussion['commentaire']|escape:'htmlall':'UTF-8'}</p>

				</div>

				{/foreach}

				{if $k_discussion > 0}
					<a href="javascript:switchCommentsVisibility('{$review['id_product_av']|escape:'htmlall':'UTF-8'}')" id="display{$review['id_product_av']|escape:'htmlall':'UTF-8'}" class="display-all-comments" review_number={$review['id_product_av']|escape:'htmlall':'UTF-8'} >{l s='Show exchanges' mod='netreviews'}</a>

					<a href="javascript:switchCommentsVisibility('{$review['id_product_av']|escape:'htmlall':'UTF-8'}')" style="display: none;" id="hide{$review['id_product_av']|escape:'htmlall':'UTF-8'}" class="display-all-comments" review_number={$review['id_product_av']|escape:'htmlall':'UTF-8'} >{l s='Hide exchanges' mod='netreviews'}</a>
					</a>
			  	{/if}
			{/if}

			</div>
			{if $i == $avisverifies_nb_reviews && !$first}
				</span>
				{$i = 1}
			{else}
                {if $i == $avisverifies_nb_reviews && $first}
                    {$first = false}
                    {$i = 1}
                {else}
                    {$i = $i + 1}
                {/if}
			{/if}

		{/foreach}


	</div>
	<img src="{if $is_https}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}modules/netreviews/views/img/pagination-loader.gif" id="av_loader" style="display:none" />
	{if $count_reviews > $avisverifies_nb_reviews}
		<a href="#" id="av_load_comments" class="av-btn-morecomment" rel="1">{l s='More reviews...' mod='netreviews' }</a>
	{/if}

{literal}
<script>
	//<![CDATA[
    $('#av_load_comments').live("click", function(){

		vnom_group = {/literal}{if !empty({$nom_group})}{$nom_group}{else}0{/if}{literal} ;
		vid_shop = {/literal}{if !empty({$id_shop|escape:'htmlall':'UTF-8'})}{$id_shop|escape:'htmlall':'UTF-8'}{else}0{/if}{literal} ;

		counted_reviews = {/literal}{$count_reviews|escape:'htmlall':'UTF-8'}{literal};
		maxpage = Math.ceil(counted_reviews / {/literal}{$avisverifies_nb_reviews|escape:'htmlall':'UTF-8'}{literal}) ;

		console.log(counted_reviews);
		console.log(maxpage);

		if($('.groupAvis:hidden').first().length !== 0){
			$('.groupAvis:hidden').first().css({ visibility: "visible", display: "block" });

			console.log($(this).attr('rel'));
			console.log(maxpage);

            $(this).attr('rel',parseInt($(this).attr('rel')) + 1 );
            if(maxpage == parseInt($(this).attr('rel')) && $('.groupAvis:hidden').length === 0){
                $(this).hide();
            }

			return false;
		}

		$.ajax({
			url: "{/literal}{if $is_https}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}{literal}modules/netreviews/ajax-load.php",
			type: "POST",
			data: {p : $(this).attr('rel'), id_product : $('input[name="id_product"]').val(), count_reviews : counted_reviews, id_shop : vid_shop, nom_group : vnom_group, avisverifies_nb_reviews : {/literal}{$avisverifies_nb_reviews}{literal}},
			beforeSend: function() {
				backup_content = $("#ajax_comment_content").html();
			   // $("#ajax_comment_content").slideUp().empty();
			   $('#av_loader').show();
			},
			success: function( html ){
			  //  $("#ajax_comment_content").empty();
			  $('#av_loader').hide();
				$("#ajax_comment_content").append(html);
				$('#av_load_comments').attr('rel', parseInt($('#av_load_comments').attr('rel')) + 1);
                if(maxpage == parseInt($('#av_load_comments').attr('rel')) && $('.groupAvis:hidden').length === 0){
                    $('#av_load_comments').hide();
                }
			},
			error: function ( jqXHR, textStatus, errorThrown ){
				alert('something went wrong...');
				$("#ajax_comment_content").html( backup_content );
			}
		});
		return false;
    })

	//]]>
</script>
{/literal}
</div>