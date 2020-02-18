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

<!-- Block Newsletter module-->
<section id="newsletter_block_left"  class="block products_block column_box col-sm-3">
	<h4><span>{l s='Newsletter' mod='blocknewsletter'}</span><i class="column_icon_toggle icon-plus-sign"></i></h4>
	<div class="block_content toggle_content email-sub">
	{if isset($msg) && $msg}
		<p class="{if $nw_error}warning_inline{else}success_inline{/if}">{$msg}</p>
	{/if}
	<div class="form-newsletter">	
        <form action="{$link->getPageLink('index')|escape:'html'}" method="post">
			<div class="input-group">
				<input class="inputNew form-control form-group" id="newsletter-input" type="email" name="email" size="18" value="{if isset($value) && $value}{$value}{else}{l s='your e-mail' mod='blocknewsletter'}{/if}" />
                <span class="input-group-btn">
                	<button type="submit" class="btn btn-default" name="submitNewsletter">ok</button>
                </span>
				<input type="hidden" name="action" value="0" />
			</div>
		</form>
	</div>	
	</div>
</section>
    <div class="footer-phone col-sm-3">

        <div>
            <a href="tel:+7(495)128-19-16">+7 (495) 128-19-16</a>
        </div>

        <p>
            Ежедневно с 10:00 до 22:00 МСК
        </p>
    </div>
    <div class="yandex-counter col-sm-3">
    <!-- Yandex.Metrika informer -->
                <a href="https://metrika.yandex.ru/stat/?id=5766790&amp;from=informer"
                target="_blank" rel="nofollow"><img src="https://informer.yandex.ru/informer/5766790/3_1_FFFFFFFF_FFFFFFFF_0_pageviews"
                style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" /></a>
    <!-- /Yandex.Metrika informer -->
    </div>
    <div class="yandex-count-mobile col-sm-3">
        <!-- Yandex.Metrika informer -->
                    <a href="https://metrika.yandex.ru/stat/?id=5766790&amp;from=informer"
                    target="_blank" rel="nofollow"><img src="https://informer.yandex.ru/informer/5766790/3_1_FFFFFFFF_FFFFFFFF_0_pageviews"
                    style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" /></a>
        <!-- /Yandex.Metrika informer -->
    </div>

<!-- /Block Newsletter module-->

<script type="text/javascript">
    var placeholder = "{l s='your e-mail' mod='blocknewsletter' js=1}";
    {literal}
        $(document).ready(function() {
            $('#newsletter-input').on({
                focus: function() {
                    if ($(this).val() == placeholder) {
                        $(this).val('');
                    }
                },
                blur: function() {
                    if ($(this).val() == '') {
                        $(this).val(placeholder);
                    }
                }
            });
        });
    {/literal}
</script>
<div class="clearfix"></div>