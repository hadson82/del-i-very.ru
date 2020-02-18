{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{include file="$tpl_dir./breadcrumb.tpl"}
{include file="$tpl_dir./errors.tpl"}
{if isset($category)}
	{if $category->id AND $category->active}
		<h1>
        <span>
			{strip}
				{$category->name|escape:'htmlall':'UTF-8'}
				{if isset($categoryNameComplement)}
					{$categoryNameComplement|escape:'htmlall':'UTF-8'}
				{/if}
				<strong class="category-product-count">
					{include file="$tpl_dir./category-count.tpl"}
				</strong>
			{/strip}
           </span>
		</h1>
		<div>
			<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- delivery-cat-up -->
			<ins class="adsbygoogle"
				style="display:block"
				data-ad-client="ca-pub-2284583363630994"
				data-ad-slot="8594778268"
				data-ad-format="auto"
				data-full-width-responsive="true"></ins>
			<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
		{if isset($subcategories)}
		<!-- Subcategories -->
		<div id="subcategories" class="titled_box">
			<h2>{l s='Subcategories'}</h2>
			<ul class="row">
			{foreach from=$subcategories item=subcategory name=subcategories}
				<li class="categories_box col-xs-4 col-sm-3 col-md-3 col-lg-3 {if $smarty.foreach.subcategories.iteration is div by 5}product_list_5{/if} {if $smarty.foreach.subcategories.iteration is div by 4}product_list_4{/if} {if $smarty.foreach.subcategories.iteration is div by 3}product_list_3{/if} {if $smarty.foreach.subcategories.iteration is div by 4}product_list_4{/if}">
					<a class="" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}">
						{*if $subcategory.id_image}
							<img src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'category_default')|escape:'html'}" alt="" />
						{else}
						<img src="{$img_cat_dir}default-medium_default.jpg" alt="" />
						{/if*}

					</a>
              <a class="lnk_more_sub" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$subcategory.name|escape:'htmlall':'UTF-8'}"><i class="icon-caret-right "></i> {$subcategory.name|escape:'htmlall':'UTF-8'|truncate:150:'...'}</a>
				</li>
			{/foreach}
			</ul>
		</div>
		{/if}
		{if $products}
            <div class="sortPagiBar shop_box_row shop_box_row clearfix">
            {include file="./product-sort.tpl"}
            {include file="./nbr-product-page.tpl"}
            </div>
            {include file="./product-list.tpl" products=$products}
            <div class="bottom_pagination shop_box_row  clearfix">
            {include file="./product-compare.tpl" paginationId='bottom'}
            {include file="./pagination.tpl" paginationId='bottom'}
            </div>
        {/if}
	{elseif $category->id}
		<p class="alert alert-info">{l s='This category is currently unavailable.'}</p>
	{/if}
{/if}
        <div class="row_category clearfix">
		{if $scenes}
			<!-- Scenes -->
			{include file="$tpl_dir./scenes.tpl" scenes=$scenes}

		{else}
			<!-- Category image -->
			{if $category->id_image}
			<div class="align_center category_image ">
				<img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html'}" alt="{$category->name|escape:'htmlall':'UTF-8'}" title="{$category->name|escape:'htmlall':'UTF-8'}" id="categoryImage"  />
			</div>
			{/if}

		{/if}
        		{if $category->description}
			{if strlen($category->description) > 2000}
				<div class="cat_desc clearfix" id="category_description_short">{$category->description|truncate:2000}&nbsp;<!-- <span onclick="$('#category_description_short').hide(); $('#category_description_full').show();" class="lnk_more_cat"><i class="icon-plus-sign"></i> {l s='More'}</span> --></div>
			<div class="cat_desc clearfix" id="category_description_full" style="display:none">{$category->description}<!-- <span onclick="$('#category_description_short').show(); $('#category_description_full').hide();" class="lnk_more_cat close_cat"><i class="icon-minus-sign"></i> {l s='Hide'}</span> --></div>
			{else}
			<div class="cat_desc clearfix">{$category->description}</div>
			{/if}

            		{/if}
        </div>
		<div>
			<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- div-cat-down -->
			<ins class="adsbygoogle"
				style="display:block"
				data-ad-client="ca-pub-2284583363630994"
				data-ad-slot="5936254515"
				data-ad-format="auto"
				data-full-width-responsive="true"></ins>
			<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
<div class="col-md-12 yellowtextcategory">
	<h2>Порядок работы:</h2>
			<p>Для добавления товара в корзину нажмите "Купить". После наполнения корзины, оформите заказ, заполнив простую форму.</p>

			<p>Менеджер свяжется с Вами в ближайшее время.</p>

			<h2>Комплектация заказа</h2>

			<p>Мы проверяем наличие и комплектуем заказы по всем складам ИКЕА:</p> 

			<p><ul>
			<li>- ИКЕА Москва Химки</li>
			<li>- ИКЕА Москва Теплый Стан</li> 
			<li>- ИКЕА Москва Белая Дача</li>
			<li>- ИКЕА Москва Ходынское поле</li>  
			<li>- Официальный интернет-магазин ИКЕА</li>
			<li>- Магазины ИКЕА в городах России</li>
			<li>- Магазины ИКЕА в Европе</li>
			</ul></p>

			<p>У нас самое полное наличие товаров ИКЕА!</p>

			<h2>Доставка по Москве и в города России</h2>

			<p>Доставка по Москве выполняется в день заказа при условии оформления заказа до 14:00, либо на следующий день с 14:00 до 18:00 или вечером с 18:00 до 22:00.</p>

			<p>Доставка в города России выполняется через транспортные компании: Деловые Линии, Энергия, СДЭК и другие.</p>
</div>
