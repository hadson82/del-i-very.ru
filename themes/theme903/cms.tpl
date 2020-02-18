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
{if ($content_only == 0)}
	{include file="$tpl_dir./breadcrumb.tpl"}
{/if}
{if isset($cms) && !isset($cms_category)}
	{if !$cms->active}
		<br />
		<div id="admin-action-cms">
			<p>{l s='This CMS page is not visible to your customers.'}
			<input type="hidden" id="admin-action-cms-id" value="{$cms->id}" />
			<input type="submit" value="{l s='Publish'}" class="exclusive btn btn-default" onclick="submitPublishCMS('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
			<input type="submit" value="{l s='Back'}" class="exclusive btn btn-default" onclick="submitPublishCMS('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
			</p>
			<div class="clear" ></div>
			<p id="admin-action-result"></p>
			</p>
		</div>
	{/if}
	<div class="rte{if $content_only} content_only{/if}">
		{$cms->content}
		{if ($cms->id == '10')}
		{literal}
		<div id="hypercomments_mix"></div>
		<script type="text/javascript">
		_hcwp = window._hcwp || [];
		_hcwp.push({widget:"Mixstream", widget_id: 81093, filter:"last", limit:40});
		(function() {
		if("HC_LOAD_INIT" in window)return;
		HC_LOAD_INIT = true;
		var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage || "ru").substr(0, 2).toLowerCase();
		var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
		hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/81093/"+lang+"/widget.js";
		var s = document.getElementsByTagName("script")[0];
		s.parentNode.insertBefore(hcc, s.nextSibling);
		})();
		</script>
		{/literal}
		{/if}
	</div>
{elseif isset($cms_category)}
	<div class="block-cms">
		<h1><a href="{if $cms_category->id eq 1}{$base_dir}{else}{$link->getCMSCategoryLink($cms_category->id, $cms_category->link_rewrite)}{/if}">{$cms_category->name|escape:'htmlall':'UTF-8'}</a></h1>
		{if isset($sub_category) && !empty($sub_category)}	
			<p class="title_block">{l s='List of sub categories in %s:' sprintf=$cms_category->name}</p>
			<ul class="bullet">
				{foreach from=$sub_category item=subcategory}
					<li>
						<a href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}">{$subcategory.name|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
		{if isset($cms_pages) && !empty($cms_pages)}
		<p class="title_block">{l s='List of pages in %s:' sprintf=$cms_category->name}</p>
			<ul class="bullet">
				{foreach from=$cms_pages item=cmspages}
					<li>
						<a href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'htmlall':'UTF-8'}">{$cmspages.meta_title|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
{else}
	<div class="error">
		{l s='This page does not exist.'}
	</div>
{/if}
<br />
		{if ($cms->id == '6')}
		{literal}
		<!-- Yandex Map start  -->
		<script src="https://api-maps.yandex.ru/2.1/?apikey=1a998042-3dcb-4948-b6cc-0aea5b985180 &lang=ru_RU" type="text/javascript"></script>
		<script type="text/javascript">
			ymaps.ready(init);
			function init(){ 
				var myMap = new ymaps.Map("map", {
					center: [55.72, 37.64],
					zoom: 10,
					controls: ['zoomControl','typeSelector','fullscreenControl','geolocationControl','trafficControl','rulerControl'],
					behaviors: ['drag']
				});
				var del_h = new ymaps.Placemark([55.91, 37.39], {
					hintContent: 'ИКЕА Химки',
					balloonContent: 'ИКЕА Химки, 141400, Московская область, Химки, микрорайон ИКЕА, корпус 1.'
				},{
					// Опции.
					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/del-map.png',
					// Размеры метки.
					iconImageSize: [50, 50]
				});
				var del_t = new ymaps.Placemark([55.60, 37.49], {
					hintContent: 'ИКЕА Теплый Стан',
					balloonContent: 'ИКЕА Теплый Стан, 142770, город Москва, поселение Сосенское, Калужское шоссе 21 км, Торгово-развлекательный центр МЕГА.'
				},{
					// Опции.
					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/del-map.png',
					// Размеры метки.
					iconImageSize: [50, 50]
				});
				var del_b = new ymaps.Placemark([55.65, 37.84], {
					hintContent: 'ИКЕА Белая Дача',
					balloonContent: 'ИКЕА Белая Дача, 140055, Московская область, г. Котельники, 1-й Покровский проезд, дом 4.'
				},{
					// Опции.
					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/del-map.png',
					// Размеры метки.
					iconImageSize: [50, 50]
				});  
				var office = new ymaps.Placemark([55.83, 37.50], {
					hintContent: 'Офис ИКЕА del-i-very.ru',
					balloonContent: 'Офис ИКЕА del-i-very.ru, Москва, ул. Выборгская, д. 22, стр 2, офис 65'
				},{
					// Опции.
					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/del-map.png',
					// Размеры метки.
					iconImageSize: [50, 50]
				});	
				var del_hod = new ymaps.Placemark([55.79, 37.52], {
					hintContent: 'ИКЕА Ходынское поле',
					balloonContent: 'ИКЕА Ходынское поле, Москва, ТРЦ Авиапар, Ходынский бульвар, д. 4, 3 и 4 этажи'
				},{
					// Опции.
					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/del-map.png',
					// Размеры метки.
					iconImageSize: [50, 50]
				});	
				myMap.geoObjects.add(del_h);
				myMap.geoObjects.add(del_t);
				myMap.geoObjects.add(del_b);
				myMap.geoObjects.add(office);
				myMap.geoObjects.add(del_hod);
			};
		</script>
		<!-- Yandex Map end -->
		{/literal}
		{/if}
