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

{if $is_old_browser}
    <div class="alert alert-danger warning bold">
        {l s='You are using an older browser, please try a newer version or other web browser (Google Chrome, Mozilla Firefox, Safari, etc) to proceed with the purchase, thanks.' mod='onepagecheckoutps'}
    </div>
{else}
    {assign var='register_customer' value=((isset($smarty.get.rc) && $smarty.get.rc == 1 && !$IS_LOGGED) || (((isset($soliberte) && $soliberte == '1') || (isset($kiala) && $kiala == '1') || (isset($pronesis_bancasella) && $pronesis_bancasella == '1')) && !$IS_LOGGED && isset($no_products) && $no_products > 0)) ? true : false}

    {if (isset($no_products) && $no_products > 0) or $register_customer}
        {if !$register_customer}
            <style>
            {literal}
                #order-opc #left_column,
                #order-opc #right_column{
                    display: none !important;
                }
            {/literal}
            </style>
        {/if}
        <script type="text/javascript">
            var pts_static_token = '{$token|escape:'htmlall':'UTF-8'}';

            var orderOpcUrl= "{$link->getPageLink('order-opc', true)|escape:'htmlall':'UTF-8':false:true}";
            var orderProcess = 0;
            var cod_id_module_payment = {if isset($cod_id_module_payment)}{$cod_id_module_payment|intval}{else}0{/if};
            var bnkplus_id_module_payment = {if isset($bnkplus_id_module_payment)}{$bnkplus_id_module_payment|intval}{else}0{/if};
            var paypal_id_module_payment = {if isset($paypal_id_module_payment)}{$paypal_id_module_payment|intval}{else}0{/if};
            var tpv_id_module_payment = {if isset($tpv_id_module_payment)}{$tpv_id_module_payment|intval}{else}0{/if};
            var sequra_id_module_payment = {if isset($sequra_id_module_payment)}{$sequra_id_module_payment|intval}{else}0{/if};
            var payments_without_popup = '{$CONFIGS.OPC_MODULES_WITHOUT_POPUP|escape:'htmlall':'UTF-8'}';
            var attributewizardpro = {if isset($attributewizardpro)}true{else}false{/if};
            var payment_modules_fee = {$payment_modules_fee|escape:'quotes':'UTF-8'};
            var OnePageCheckoutPS = {ldelim}
                REGISTER_CUSTOMER : {if $register_customer}true{else}false{/if},
                CONFIGS : {$CONFIGS_JS|escape:'quotes':'UTF-8'},
                ONEPAGECHECKOUTPS_DIR: '{$ONEPAGECHECKOUTPS_DIR|escape:'htmlall':'UTF-8'}',
                ONEPAGECHECKOUTPS_IMG: '{$ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}',
                ENABLE_INVOICE_ADDRESS: Boolean({$CONFIGS.OPC_ENABLE_INVOICE_ADDRESS|intval}),
                REQUIRED_INVOICE_ADDRESS: Boolean({$CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS|intval}),
                ENABLE_TERMS_CONDITIONS: Boolean({$CONFIGS.OPC_ENABLE_TERMS_CONDITIONS|intval}),
                ENABLE_PRIVACY_POLICY: Boolean({$CONFIGS.OPC_ENABLE_PRIVACY_POLICY|intval}),
                SHOW_DELIVERY_VIRTUAL: Boolean({$CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL|intval}),
                USE_SAME_NAME_CONTACT_DA: Boolean({$CONFIGS.OPC_USE_SAME_NAME_CONTACT_DA|intval}),
                USE_SAME_NAME_CONTACT_BA: Boolean({$CONFIGS.OPC_USE_SAME_NAME_CONTACT_BA|intval}),
                OPC_SHOW_POPUP_PAYMENT: Boolean({$CONFIGS.OPC_SHOW_POPUP_PAYMENT|intval}),
                PAYMENTS_WITHOUT_RADIO: Boolean({$CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO|intval}),
                IS_VIRTUAL_CART: Boolean({$IS_VIRTUAL_CART|intval}),
                IS_LOGGED: Boolean({$IS_LOGGED|intval}),
                IS_GUEST: Boolean({$IS_GUEST|intval}),
                id_address_delivery: {$id_address_delivery|intval},
                id_address_invoice: {$id_address_invoice|intval},
                date_format_language: '{$date_format_language|escape:'htmlall':'UTF-8'}',
				id_country_delivery_default: {$id_country_delivery_default|intval},
                PS_GUEST_CHECKOUT_ENABLED: Boolean({$PS_GUEST_CHECKOUT_ENABLED|intval}),
                LANG_ISO: '{$lang_iso|escape:'htmlall':'UTF-8'}',
                LANG_ISO_ALLOW : ['es', 'en', 'ca', 'br', 'eu', 'pt', 'eu', 'mx'],
                IS_NEED_INVOICE : Boolean({$is_need_invoice|intval}),
                GUEST_TRACKING_URL : '{$link->getPageLink("guest-tracking", true)|escape:'htmlall':'UTF-8'}',
                HISTORY_URL : '{$link->getPageLink("history", true)|escape:'htmlall':'UTF-8'}',
                IS_RTL : Boolean({$is_rtl|intval}),
                PS_TAX_ADDRESS_TYPE : '{$PS_TAX_ADDRESS_TYPE|escape:'htmlall':'UTF-8'}',
                Msg: {ldelim}
                    there_are: "{l s='There are' mod='onepagecheckoutps' js=1}",
                    there_is: "{l s='There is' mod='onepagecheckoutps' js=1}",
                    error: "{l s='Error' mod='onepagecheckoutps' js=1}",
                    errors: "{l s='Errors' mod='onepagecheckoutps' js=1}",
                    field_required: "{l s='Required' mod='onepagecheckoutps' js=1}",
                    dialog_title: "{l s='Confirm Order' mod='onepagecheckoutps' js=1}",
                    no_payment_modules: "{l s='There are no payment methods available.' mod='onepagecheckoutps' js=1}",
                    validating: "{l s='Validating, please wait' mod='onepagecheckoutps' js=1}",
                    error_zipcode: "{l s='The Zip / Postal code is invalid' mod='onepagecheckoutps' js=1}",
                    error_registered_email: "{l s='An account is already registered with this e-mail' mod='onepagecheckoutps' js=1}",
                    error_registered_email_guest: "{l s='This email is already registered, you can login or fill form again.' mod='onepagecheckoutps' js=1}",
                    delivery_billing_not_equal: "{l s='Delivery address alias cannot be the same as billing address alias' mod='onepagecheckoutps' js=1}",
                    errors_trying_process_order: "{l s='The following error occurred while trying to process the order' mod='onepagecheckoutps' js=1}",
                    agree_terms_and_conditions: "{l s='You must agree to the terms of service before continuing.' mod='onepagecheckoutps' js=1}",
                    agree_privacy_policy: "{l s='You must agree to the privacy policy before continuing.' mod='onepagecheckoutps' js=1}",
                    fields_required_to_process_order: "{l s='You must complete the required information to process your order.' mod='onepagecheckoutps' js=1}",
                    check_fields_highlighted: "{l s='Check the fields that are highlighted and marked with an asterisk.' mod='onepagecheckoutps' js=1}",
                    error_number_format: "{l s='The format of the number entered is not valid.' mod='onepagecheckoutps' js=1}",
                    oops_failed: "{l s='Oops! Failed' mod='onepagecheckoutps' js=1}",
                    continue_with_step_3: "{l s='Continue with step 3.' mod='onepagecheckoutps' js=1}",
                    email_required: "{l s='Email address is required.' mod='onepagecheckoutps' js=1}",
                    email_invalid: "{l s='Invalid e-mail address.' mod='onepagecheckoutps' js=1}",
                    password_required: "{l s='Password is required.' mod='onepagecheckoutps' js=1}",
                    password_too_long: "{l s='Password is too long.' mod='onepagecheckoutps' js=1}",
                    password_invalid: "{l s='Invalid password.' mod='onepagecheckoutps' js=1}",
                    addresses_same: "{l s='You must select a different address for shipping and billing.' mod='onepagecheckoutps' js=1}",
                    create_new_address: "{l s='Are you sure you wish to add a new delivery address? You can use the current address and modify the information.' mod='onepagecheckoutps' js=1}",
                    cart_empty: "{l s='Your shopping cart is empty.' mod='onepagecheckoutps' js=1}",
                    dni_spain_invalid: "{l s='DNI/CIF/NIF is invalid.' mod='onepagecheckoutps' js=1}",
                    payment_method_required: "{l s='Please select a payment method to proceed.' mod='onepagecheckoutps' js=1}",
                    shipping_method_required: "{l s='Please select a shipping method to proceed.' mod='onepagecheckoutps' js=1}",
                    select_pickup_point: "{l s='To select a pick up point is necessary to complete your information and delivery address in the first step.' mod='onepagecheckoutps' js=1}",
                    need_select_pickup_point: "{l s='You need to select on shipping a pickup point to continue with the purchase.' mod='onepagecheckoutps' js=1}",
                    select_date_shipping: "{l s='Please select a date for shipping.' mod='onepagecheckoutps' js=1}",
                    confirm_payment_method: "{l s='Confirmation payment' mod='onepagecheckoutps' js=1}",
                    to_determinate: "{l s='To determinate' mod='onepagecheckoutps' js=1}",
                    login_customer: "{l s='Loggin' mod='onepagecheckoutps' js=1}"
                {rdelim}
            {rdelim};
            var messageValidate = {ldelim}
                errorGlobal         : "{l s='This is not a valid.' mod='onepagecheckoutps' js=1}",
                errorIsName         : "{l s='This is not a valid name.' mod='onepagecheckoutps' js=1}",
                errorIsEmail        : "{l s='This is not a valid email address.' mod='onepagecheckoutps' js=1}",
                errorIsPostCode     : "{l s='This is not a valid post code.' mod='onepagecheckoutps' js=1}",
                errorIsAddress      : "{l s='This is not a valid address.' mod='onepagecheckoutps' js=1}",
                errorIsCityName     : "{l s='This is not a valid city.' mod='onepagecheckoutps' js=1}",
                isMessage           : "{l s='This is not a valid message.' mod='onepagecheckoutps' js=1}",
                errorIsDniLite      : "{l s='This is not a valid document identifier.' mod='onepagecheckoutps' js=1}",
                errorIsPhoneNumber  : "{l s='This is not a valid phone.' mod='onepagecheckoutps' js=1}",
                errorIsPasswd       : "{l s='This is not a valid password. Minimum 5 characters.' mod='onepagecheckoutps' js=1}",
                errorisBirthDate    : "{l s='This is not a valid birthdate.' mod='onepagecheckoutps' js=1}",
                errorisDate			: "{l s='This is not a valid date.' mod='onepagecheckoutps' js=1}",
                badUrl              : "{l s='This is not a valid url.' mod='onepagecheckoutps' js=1} ex: http://www.domain.com",
                badInt              : "{l s='This is not a valid.' mod='onepagecheckoutps' js=1}",
                notConfirmed        : "{l s='The values do not match.' mod='onepagecheckoutps' js=1}",
                lengthTooLongStart  : "{l s='It is only possible enter' mod='onepagecheckoutps' js=1} ",
                lengthBadEnd        : " {l s='characters.' mod='onepagecheckoutps' js=1}"
            {rdelim};

            var countries = new Array();
            var countriesNeedIDNumber = new Array();
            var countriesNeedZipCode = new Array();
            var countriesIsoCode = new Array();
            {foreach from=$countries item='country'}
                countriesIsoCode[{$country.id_country|intval}] = '{$country.iso_code|escape:'htmlall':'UTF-8'}';
                {if isset($country.states) && $country.contains_states}
                    countries[{$country.id_country|intval}] = new Array();
                    {foreach from=$country.states item='state' name='states'}
                        {if $state.active eq 1}countries[{$country.id_country|intval}].push({ldelim}id : '{$state.id_state|intval}', name:'{$state.name|escape:'html':'UTF-8'|trim}'{rdelim});{/if}
                    {/foreach}
                {/if}
                countriesNeedIDNumber[{$country.id_country|intval}] = {$country.need_identification_number|intval};
                {if $country.need_zip_code}
                    countriesNeedZipCode[{$country.id_country|intval}] = '{$country.zip_code_format|escape:'htmlall':'UTF-8'}';
                {/if}
            {/foreach}
        </script>
        <div id="onepagecheckoutps" class="pts bootstrap">
            <div class="loading_big"><i class="fa fa-spin fa-refresh fa-4x"></i></div>
            <input type="hidden" id="logged" value="{$logged|intval}" />

            <div class="row">
                {if !$register_customer}
                    <div id="onepagecheckoutps_header" class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                            <div id="div_onepagecheckoutps_info" class="col-md-7 col-sm-12 col-xs-12">
                                <h2>{l s='Quick Checkout' mod='onepagecheckoutps'}</h2>
                                <h4>{l s='Complete the following fields to process your order.' mod='onepagecheckoutps'}</h4>
                            </div><!--
                            -->{if $IS_LOGGED}<div id="div_onepagecheckoutps_login" class="col-md-5 col-sm-12 col-xs-12">
                                <div class="row end-md text-right">
									<p>
										<i class="fa fa-lock fa-1x"></i>
										{l s='Welcome' mod='onepagecheckoutps'},&nbsp;
										<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">
											<b>{$cookie->customer_firstname|escape:'htmlall':'UTF-8'} {$cookie->customer_lastname|escape:'htmlall':'UTF-8'}</b>
										</a>
										<a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'htmlall':'UTF-8'}" title="{l s='Log me out' mod='onepagecheckoutps'}" class="btn btn-default btn-xs">
											<i class="fa fa-sign-out fa-1x"></i>
											{l s='Log out' mod='onepagecheckoutps'}
										</a>
									</p>
                                </div>
                            </div>{/if}
                        </div>
                    </div>
                {/if}
                <div id="onepagecheckoutps_contenedor" class="col-md-12 col-sm-12 col-xs-12">
                    <div id="onepagecheckoutps_forms" class="hidden"></div>
                    <div id="opc_temporal" class="hidden"></div>

                    {if !$IS_LOGGED}
                        <div id="opc_login" class="hidden" title="{l s='Login' mod='onepagecheckoutps'}">
                            <div class="loading_small"><i class="fa fa-spin fa-refresh fa-2x"></i></div>
                            <div class="login-box">
                                <form id="form_login" autocomplete="off">
                                    <div class="form-group input-group margin-bottom-sm">
                                        <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                        <input
                                            id="txt_login_email"
                                            class="form-control"
                                            type="text"
                                            placeholder="{l s='E-mail' mod='onepagecheckoutps'}"
                                            data-validation="isEmail"
                                        />
                                    </div>
                                    <div class="form-group input-group margin-bottom-sm">
                                        <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                                        <input
                                            id="txt_login_password"
                                            class="form-control"
                                            type="password"
                                            placeholder="{l s='Password' mod='onepagecheckoutps'}"
                                            data-validation="length"
                                            data-validation-length="min5"
                                        />
                                    </div>

                                    <div class="alert alert-warning hidden"></div>

                                    <button type="button" id="btn_login" class="btn btn-primary btn-block">
                                        <i class="fa fa-lock fa-lg"></i>
                                        {l s='Login' mod='onepagecheckoutps'}
                                    </button>

                                    <p class="forget_password">
                                        <a href="{$link->getPageLink('password')|escape:'htmlall':'UTF-8'}">{l s='Forgot your password?' mod='onepagecheckoutps'}</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    {/if}
                    <form id="form_onepagecheckoutps" autocomplete="on">
                        {foreach from=$position_steps item=column}
                            <div class="{$column.classes|escape:'htmlall':'UTF-8'} nopadding">
                                <div class="row">
                                    {foreach from=$column.rows item=row}
                                        {include file='./steps/'|cat:$row.name_step|cat:'.tpl' classes=$row.classes}
                                    {/foreach}
                                </div>
                            </div>
                        {/foreach}
                        <div class="col-xs-12 clear clearfix">
                            {if $CONFIGS.OPC_ENABLE_HOOK_SHOPPING_CART && !$CONFIGS.OPC_COMPATIBILITY_REVIEW && $CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO}
                                <div id="HOOK_SHOPPING_CART" class="row">{$HOOK_SHOPPING_CART|escape:'html':'UTF-8':false:true}</div>
                                <p class="cart_navigation_extra row">
                                    <span id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA|escape:'html':'UTF-8':false:true}</span>
                                </p>
                            {/if}
                        </div>
                    </form>
                </div>
                <div class="clear clearfix"></div>
            </div>
        </div>
    {else}
        {include file="$tpl_dir./shopping-cart.tpl" empty=""}
    {/if}
{/if}