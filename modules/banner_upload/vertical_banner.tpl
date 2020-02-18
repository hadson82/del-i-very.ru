<!-- MODULE Block banner upload -->
{if !empty($banner)}
	<div>
		<div class="block">
			<h4>{l s='Ads' mod='banner_upload'}</h4>
			<div class="block_content" align="center">
				{foreach from=$banner item=ban}		
					
						{if !empty($ban.display_type)}
							{math assign="width" equation="(x / 2)" x=$ban.width}
							{math assign="height" equation="(x / 2)" x=$ban.height}
							<div id="blmod_banner_popup-{$ban.id}" class="blmod_banner_popup" style="margin: -{$height}px 0px 0px -{$width}px;">	
								<div id="lmod_banner_popup-{$ban.id}" class="blmod_banner_popup_close" title="{l s='Close' mod='banner_upload'}">X</div>
								<div class="blmod_banner_popup_style">
						{/if}
						<div id="count_blmod_banner_popup-{$ban.id}" class="blmod_banner_click_count{if $ban.type == 'x-shockwave-flash'}_swf{/if}">
							{if empty($ban.ads_type)}
								{if $ban.type != 'x-shockwave-flash'}
									{if $ban.url != 'http://' and !empty($ban.url)}
										{if $ban.new_window == '1'}
											<a href = "{$ban.url}" target = "_blank">
										{else}
											<a href = "{$ban.url}" >
										{/if}
									{/if}
									<img src = "{$base_dir}modules/banner_upload/banner_img/{$ban.image}" {if !empty($ban.banner_name)}alt = "{$ban.banner_name}" title = "{$ban.banner_name}"{/if}/>{if $ban.url != 'http://' and !empty($ban.url)}</a>{/if}
								{else}
									&ensp;
									<object width="{$ban.width}" height="{$ban.height}">
										<param name='wmode' value='transparent' />
										<param name="quality" value="high" />
										<embed src='{$base_dir}modules/banner_upload/banner_img/{$ban.image}' wmode=transparent allowfullscreen='true' allowscriptaccess='always' width="{$ban.width}" height="{$ban.height}">
										</embed>
									</object>
								{/if}
							{else}
								<div class="blmod_ads_vertical">
									{$ban.custome_ads_code}
								</div>
							{/if}
						</div>
						{if !empty($ban.display_type)}
							</div>
							</div>
						{/if}					
				{/foreach}
			</div>	
		</div>
	</div>
{/if}
<!--  END MODULE Block banner upload -->