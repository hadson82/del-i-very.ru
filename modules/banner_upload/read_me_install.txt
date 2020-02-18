Install instruction:
your themes/product-list.top, ~27 line (after {if isset($products)} )

Add this code:
<!-- BlModules banner upload -->
{if !empty($banner_category)}
	<div style="clear: both;">
		{include file="$tpl_dir../../modules/banner_upload/horizontal_banner.tpl" banner=$banner_category}
	</div>
{/if}
<!-- END BlModules banner upload -->