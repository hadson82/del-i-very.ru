<!-- MODULE Block banner upload-->
{if !empty($header_show_files) and $header_show_files == 1}
	<script type="text/javascript" src="{$base_dir_ssl}modules/banner_upload/banner_popup.js"></script>
	<link href="{$base_dir_ssl}modules/banner_upload/banner_uploader.css" rel="stylesheet" type="text/css" media="all" />
	<div id="blmod_click_file" style="display: none;">{$base_dir}modules/banner_upload/banner_upload_click.php</div>
{/if}
{if !empty($banner_extra) and !empty($header_status)}
	{assign var="be_old" value='none'}
	{foreach from=$banner_extra item=be}
		{if $be.id != $be_old}
			<div id="count_blmod_banner_popup-{$be.b_id}" class="blmod_banner_click_count{if $be.type == 'x-shockwave-flash'}_swf{/if}" style="position:fixed; z-index:9999999; {if $be.pos_x == 'r'}right{else}left{/if}:{$be.val_x}px; {if $be.pos_y == 't'}top{else}bottom{/if}:{$be.val_y}px; ">
		{/if}
		
			{if empty($be.ads_type)}
				{if $be.type != 'x-shockwave-flash'}	
					{if $be.url != 'http://' and !empty($be.url)}
						{if $be.new_window == '1'}
							<a href = "{$be.url}" target = "_blank">
						{else}
							<a href = "{$be.url}" >
						{/if}
					{/if}
					<img src = "{$base_dir}modules/banner_upload/banner_img/{$be.image}" {if !empty($be.banner_name)}alt = "{$be.banner_name}" title = "{$be.banner_name}"{/if}/>{if $be.url != 'http://' and !empty($be.url)}</a>{/if}
				{else}
					&ensp;
					<object id="FlashID" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{$be.width}" height="{$be.height}">
					<param name="movie" value= "{$base_dir}modules/banner_upload/banner_img/{$be.image}" /><param name="quality" value="high" />
					<param name="wmode" value="opaque" />
					<param name="swfversion" value="6.0.65.0" />
					<!-- This param tag prompts users with Flash Player 6.0 r65 and higher to download the latest version of Flash Player. Delete it if you don\'t want users to see the prompt. -->
					<param name="expressinstall" value="Scripts/expressInstall.swf" />
					<!-- Next object tag is for non-IE browsers. So hide it from IE using IECC. -->
					<!--[if !IE]>-->
					<object type="application/x-shockwave-flash" data= "{$base_dir}modules/banner_upload/banner_img/{$be.image}" width="{$be.width}" height="{$be.height}">
					<!--<![endif]-->
					<param name="quality" value="high" />
					<param name="wmode" value="opaque" />
					<param name="swfversion" value="6.0.65.0" />
					<param name="expressinstall" value="Scripts/expressInstall.swf" />
					<!-- The browser displays the following alternative content for users with Flash Player 6.0 and older. -->
					<div>
					<p>Content on this page requires a newer version of Adobe Flash Player.</p>
					<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
					</div>
					<!--[if !IE]>-->
					</object>
					<!--<![endif]-->
					</object>
				{/if}
				<br/>
			{else}
				<div>
					{$be.custome_ads_code}
				</div>
			{/if}
		
		{if $be.id != $be.next}
			</div>
		{/if}
		
		{assign var="be_old" value=$be.id}
	{/foreach}
{/if}
{if !empty($banner)}
	<div style = "width:100%; text-align: center;">
		{foreach from=$banner item=ban}			
				{if !empty($ban.display_type)}
					{math assign="width" equation="(x / 2)" x=$ban.width}
					{math assign="height" equation="(x / 2)" x=$ban.height}
					<div id="blmod_banner_popup-{$ban.id}" class="blmod_banner_popup" style="margin: -{$height}px 0px 0px -{$width}px;">	
						<div id="lmod_banner_popup-{$ban.id}" class="blmod_banner_popup_close" title="{l s='Close' mod='banner_upload'}">X</div>
						<div class="blmod_banner_popup_style">
				{/if}
					<span id="count_blmod_banner_popup-{$ban.id}" class="blmod_banner_click_count{if $ban.type == 'x-shockwave-flash'}_swf{/if}">
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
							<div>
								{$ban.custome_ads_code}
							</div>
						{/if}
					</span>
				{if !empty($ban.display_type)}
					</div>
					</div>
				{/if}			
		{/foreach}
	</div>
{/if}
{if !empty($banner_cicle)}
	{literal}
		<script type = "text/javascript">
		$(document).ready(function()
		{	
			$(document.body).append('<div id="blmod_marquee_bg" style="height:{/literal}{$banner_cicle.height}{literal}px;">&nbsp;</div>');
		});
		</script>
	{/literal}
	<div id="blmod_marquee_img" style="display:none;">{$base_dir}modules/banner_upload/banner_img/{$banner_cicle.image}</div>
	<div id="blmod_marquee_img_w" style="display:none;">{$banner_cicle.width}</div>
	<script type="text/javascript" src="{$base_dir}modules/banner_upload/banner_cicle.js"></script>
	<div id="count_blmod_banner_popup-{$banner_cicle.id}" class="blmod_banner_click_count" style="width: 100%; height: {$banner_cicle.height}px; position:fixed; z-index:99999999; bottom: 0px; left:0px; margin-left:0px;">		
		{if $banner_cicle.url != 'http://' and !empty($banner_cicle.url)}
			{if $banner_cicle.new_window == '1'}
				<a href = "{$banner_cicle.url}" target = "_blank">
			{else}
				<a href = "{$banner_cicle.url}" >
			{/if}
		{/if}
			<div style="width: 100%; height: {$banner_cicle.height}px; position:fixed; z-index:99999999; bottom: 0px; left:0px;" id="marquee_banner_upload"></div>	
		{if $banner_cicle.url != 'http://' and !empty($banner_cicle.url)}</a>{/if}
	</div>
{/if}
<!--  END MODULE Block banner upload-->