{*
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
*}

{if isset($css_files)}
    {foreach from=$css_files key=css_uri item=media}
        <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
    {/foreach}
{/if}
{if isset($js_files)}
    {foreach from=$js_files item=js_uri}
        <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
    {/foreach}
{/if}

<script type="text/javascript">
    var payment_modules_fee = {$payment_modules_fee|escape:'quotes':'UTF-8'};
    
    var currencySign = '{$currencySign|escape:'html':'UTF-8'}';
    var currencyRate = '{$currencyRate|floatval}';
    var currencyFormat = '{$currencyFormat|intval}';
    var currencyBlank = '{$currencyBlank|intval}';
    var txtProduct = "{l s='product' mod='onepagecheckoutps' js=1}";
    var txtProducts = "{l s='products' mod='onepagecheckoutps' js=1}";
    var deliveryAddress = {$cart->id_address_delivery|intval};

    {literal}
    $(document).ready(function(){
        /*support some template that use live.*/
        $('.cart_quantity_up').die('click');
		$('.cart_quantity_down').die('click');
		$('.cart_quantity_delete').die('click');
        $('.megacart_quantity_up').die('click');
		$('.megacart_quantity_down').die('click');
		$('.megacart_quantity_delete').die('click');
    });
    {/literal}
</script>

<script type="text/javascript" src="{$js_dir|escape:'htmlall':'UTF-8'}cart-summary.js"></script>

<script type="text/javascript">
    {literal}

    setTimeout(function(){
        showLoadingAndAddEvent('.cart_quantity_up', function(){
            $('.cart_quantity_up').die('click').click(function(e){
                e.preventDefault();
                upQuantity($(this).attr('id').replace('cart_quantity_up_', ''));
                $('#' + $(this).attr('id').replace('_up_', '_down_')).removeClass('disabled');
            });
        });
        showLoadingAndAddEvent('.cart_quantity_down', function(){
            $('.cart_quantity_down').die('click').click(function(e){
                e.preventDefault();
                downQuantity($(this).attr('id').replace('cart_quantity_down_', ''));
            });
        });
        showLoadingAndAddEvent('.cart_quantity_delete', function(){
            $('.cart_quantity_delete').die('click').click(function(e){
                e.preventDefault();
                deleteProductFromSummary($(this).attr('id'));
            });
        });
        showLoadingAndAddEvent('.megacart_quantity_up', '');
        showLoadingAndAddEvent('.megacart_quantity_down', '');
        showLoadingAndAddEvent('.megacart_quantity_delete', '');

        updateCartSummary = function (json){
            if (typeof json !== typeof undefined){
                if (json.is_virtual_cart){
                    location.reload();
                }else{
                    if (typeof json.load === typeof undefined){
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();

                        Carrier.getByCountry();
                    }
                }
            }
        }
    }, 1000);

	function showLoadingAndAddEvent(selector, event){
		var $selector = $(selector);

		if ($selector.length > 0){
			var events = $._data($selector[0], "events");

            if (typeof events === typeof undefined && typeof event == 'function'){
                event();
            }

			if (typeof events != typeof undefined){
				$.each(events, function(type, events) {
					if (type === 'click') {
						var original_events = [];
						$.each(events, function(e, event) {
							original_events.push(event.handler);
						});

						var new_event = function(e) {
							e.preventDefault();

							$('#onepagecheckoutps_step_review_container .loading_small').show();

							$(document).on('click', '.fancybox-close', function(){
								$('#onepagecheckoutps_step_review_container .loading_small').hide();
							});

							$(e.currentTarget).off(type, new_event);
							$.each(original_events, function(o, original_event) {
								$(e.currentTarget).on('click', original_event).trigger(type);
							});
						};

						$selector.off(type).on(type, new_event);
					}
				});
			}
		}
	}

    {/literal}
</script>