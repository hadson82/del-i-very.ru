{if $htmlitems.items}
{if $page_name == 'index'}
<div id="htmlcontent_left">
    <ul class="htmlcontent-home clearfix">
        {foreach name=items from=$htmlitems.items item=hItem}
        	<li class="htmlcontent-item"><div>
            	{*if $hItem.url}
                	<a href="{$hItem.url}" class="item-link"{if $hItem.target == 1} target="_blank"{/if}>
                {/if*}
	            	{if $hItem.image}
	                	<img src="{$module_dir}images/{$hItem.image}" class="item-img" alt="" />
	                {/if}
	            	{if $hItem.title && $hItem.title_use == 1}
                        <h3 class="item-title">{$hItem.title}</h3>
	                {/if}
	            	{if $hItem.html}
	                	<div class="item-html"><div>
                        	{$hItem.html}
                        </div></div>
	                {/if}
            	{*if $hItem.url}
                	</a>
                {/if*}
            </div></li>
        {/foreach}
    </ul>
</div>
{/if}
{/if}
