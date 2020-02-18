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

<style>
    {literal}
    #order_step, #order_steps, div.order_delivery{
        display:none;
    }
    {/literal}
    {if isset($paramsFront.CONFIGS.OPC_THEME_BACKGROUND_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_BACKGROUND_COLOR)}
        #onepagecheckoutps {ldelim}
            background: {$paramsFront.CONFIGS.OPC_THEME_BACKGROUND_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
        .loading_small, .loading_big, .modal-backdrop, .lock_controls {ldelim}
            background-color: {$paramsFront.CONFIGS.OPC_THEME_BACKGROUND_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR)}
        #onepagecheckoutps #onepagecheckoutps_contenedor {ldelim}
            border: 1px solid {$paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
        #onepagecheckoutps #opc_social_networks {ldelim}
            border-bottom: 1px solid {$paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
        div#onepagecheckoutps div#onepagecheckoutps_step_review #order-detail-content .image_zoom{ldelim}
            border: 2px solid {$paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_ICON_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_ICON_COLOR)}
        div#onepagecheckoutps .onepagecheckoutps_p_step i.fa {ldelim}
            color: {$paramsFront.CONFIGS.OPC_THEME_ICON_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_TEXT_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_TEXT_COLOR)}
        #onepagecheckoutps *, #onepagecheckoutps {ldelim}
            color: {$paramsFront.CONFIGS.OPC_THEME_TEXT_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_SELECTED_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_SELECTED_COLOR)}
        #onepagecheckoutps .module_payment_container.selected.alert.alert-info,
        #onepagecheckoutps .delivery_option.selected.alert.alert-info {ldelim}
            background-color: {$paramsFront.CONFIGS.OPC_THEME_SELECTED_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_SELECTED_TEXT_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_SELECTED_TEXT_COLOR)}
        #onepagecheckoutps .module_payment_container.selected.alert.alert-info,
        #onepagecheckoutps .delivery_option.selected.alert.alert-info {ldelim}
            color: {$paramsFront.CONFIGS.OPC_THEME_SELECTED_TEXT_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_CONFIRM_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_CONFIRM_COLOR)}
        #onepagecheckoutps #btn_place_order,
        #onepagecheckoutps #payment_modal #cart_navigation button,
        #onepagecheckoutps #btn_save_customer {ldelim}
            background: {$paramsFront.CONFIGS.OPC_THEME_CONFIRM_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_CONFIRM_TEXT_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_CONFIRM_TEXT_COLOR)}
        #onepagecheckoutps #btn_place_order i.fa, #onepagecheckoutps #btn_place_order,
        #onepagecheckoutps #payment_modal #cart_navigation button i, #onepagecheckoutps #payment_modal #cart_navigation button,
        #onepagecheckoutps #btn_save_customer i.fa, #onepagecheckoutps #btn_save_customer {ldelim}
            border-color: {$paramsFront.CONFIGS.OPC_THEME_CONFIRM_TEXT_COLOR|escape:'htmlall':'UTF-8'} !important;
            color: {$paramsFront.CONFIGS.OPC_THEME_CONFIRM_TEXT_COLOR|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
        #onepagecheckoutps #btn_place_order:hover,
        #onepagecheckoutps #payment_modal #cart_navigation button:hover,
        #onepagecheckoutps #btn_save_customer:hover {ldelim}
            opacity: 0.8;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON) and not empty($paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON)}
        #onepagecheckoutps #opc_show_login{ldelim}
            background: {$paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON|escape:'htmlall':'UTF-8'} !important;
            border-color: {$paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON_TEXT) and not empty($paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON_TEXT)}
        #onepagecheckoutps #opc_show_login{ldelim}
            color: {$paramsFront.CONFIGS.OPC_ALREADY_REGISTER_BUTTON_TEXT|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON) and not empty($paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON)}
        #onepagecheckoutps #form_login #btn_login{ldelim}
            background: {$paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON|escape:'htmlall':'UTF-8'} !important;
            border-color: {$paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON_TEXT) and not empty($paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON_TEXT)}
        #onepagecheckoutps #form_login #btn_login{ldelim}
            color: {$paramsFront.CONFIGS.OPC_THEME_LOGIN_BUTTON_TEXT|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON) and not empty($paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON)}
        #onepagecheckoutps #list-voucher-allowed #submitAddDiscount{ldelim}
            background: {$paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON|escape:'htmlall':'UTF-8'} !important;
            border-color: {$paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON_TEXT) and not empty($paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON_TEXT)}
        #onepagecheckoutps #list-voucher-allowed #submitAddDiscount{ldelim}
            color: {$paramsFront.CONFIGS.OPC_THEME_VOUCHER_BUTTON_TEXT|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_BACKGROUND_BUTTON_FOOTER) and not empty($paramsFront.CONFIGS.OPC_BACKGROUND_BUTTON_FOOTER)}
        div#onepagecheckoutps div#onepagecheckoutps_step_review .stick_buttons_footer{ldelim}
            background-color: {$paramsFront.CONFIGS.OPC_BACKGROUND_BUTTON_FOOTER|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}
    {if isset($paramsFront.CONFIGS.OPC_THEME_BORDER_BUTTON_FOOTER) and not empty($paramsFront.CONFIGS.OPC_THEME_BORDER_BUTTON_FOOTER)}
        div#onepagecheckoutps div#onepagecheckoutps_step_review .stick_buttons_footer{ldelim}
            border-color: {$paramsFront.CONFIGS.OPC_THEME_BORDER_BUTTON_FOOTER|escape:'htmlall':'UTF-8'} !important;
        {rdelim}
    {/if}

    @media (max-width: 992px) {ldelim}
        #order-detail-content .cart_item {ldelim}
            {if isset($paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR) and not empty($paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR)}
                border-bottom: 1px solid {$paramsFront.CONFIGS.OPC_THEME_BORDER_COLOR|escape:'htmlall':'UTF-8'};
            {else}
                border-bottom: 1px solid #d6d4d4;
            {/if}
        {rdelim}
    {rdelim}

</style>
<script type="text/javascript">
    {if isset($paramsFront.paypal_ec_canceled) and $paramsFront.paypal_ec_canceled}
        window.location = "{$link->getPageLink('order', true)|escape:'htmlall':'UTF-8'}";
    {/if}
</script>