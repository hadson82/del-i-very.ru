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

{assign var='addresses_tab' value=(isset($OPC_FIELDS[$OPC_GLOBALS->object->delivery]) && isset($OPC_FIELDS[$OPC_GLOBALS->object->invoice]) && $CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && !$IS_VIRTUAL_CART && sizeof($OPC_FIELDS[$OPC_GLOBALS->object->delivery]) > 1) || $CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && $IS_VIRTUAL_CART && $CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL}

{if isset($OPC_FIELDS[$OPC_GLOBALS->object->customer])}
    <h5 class="onepagecheckoutps_p_step onepagecheckoutps_p_step_one">
        <i class="fa fa-user fa-3x"></i>
        {l s='Your data' mod='onepagecheckoutps'}
        {* {if !$IS_LOGGED}
            <button type="button" id="opc_show_login" class="btn btn-primary btn-xs pull-right" >
                <i class="fa fa-unlock-alt fa-1x"></i>
                {l s='Already registered?' mod='onepagecheckoutps'}
            </button>
        {/if} *}
    </h5>

	{* Support module: sociallogin *}
	{if isset($social_networks)}
		<section id="module_sociallogin">
			{$i = 1}
			{foreach from=$social_networks item=item key=k}
				{if $item.complete_config}
					<button type="button" class="btn btn-social{if $button}-icon{/if} {if $size != 'st'}btn-{$size|escape:'html':'UTF-8'}{/if} btn-{$item.icon_class|escape:'html':'UTF-8'}" onclick="window.open('{$item.connect|escape:'html':'UTF-8'}', {if $popup}'_blank'{else}'_self'{/if}, 'menubar=no, status=no, copyhistory=no, width=640, height=640, top=220, left=640')">
						<i class="fa fa-{$item.fa_icon|escape:'html':'UTF-8'}"></i>
						{if !$button}
							{if $sign_in}{l s='Sign in with' mod='onepagecheckoutps'}{/if}
							{$item.name|escape:'html':'UTF-8'|capitalize}
						{/if}
					</button>
					{$i = $i + 1}
				{/if}
			{/foreach}
		</section>
	{/if}

	{if !$isLogged && isset($opc_social_networks) && ($opc_social_networks->facebook->client_id neq '' || $opc_social_networks->google->client_id neq '')}
		<section id="opc_social_networks">
			<h5>{l s='Login using your social networks' mod='onepagecheckoutps'}</h5>
			{foreach from=$opc_social_networks key='name' item='network'}
				{if $network->client_id neq ''}
					<button type="button" class="btn btn-sm btn-{$name|escape:'html':'UTF-8'}" onclick="Fronted.openWindow('{$link->getModuleLink('onepagecheckoutps', 'login')|escape:'htmlall':'UTF-8'}?sv={$network->network|escape:'html':'UTF-8'}', true)">
						<i class="fa fa-1x {$network->class_icon|escape:'html':'UTF-8'}"></i>
						{$network->name_network|escape:'html':'UTF-8'}
					</button>
				{/if}
			{/foreach}
		</section>
	{/if}

    {*if !$IS_LOGGED*}
        <div id="hook_create_account_top" class="col-xs-12">
            {$HOOK_CREATE_ACCOUNT_TOP|escape:'htmlall':'UTF-8':false:true}
        </div>
    {*/if*}

    <section id="customer_container">
        {if isset($sveawebpay_md5)}
            <div class="form-group col-xs-12 clear clearfix">
               <label for="sveawebpay_security_number">
                   {l s='Social security number' mod='onepagecheckoutps'}:
               </label>
               <input id="sveawebpay_md5" name="sveawebpay_md5" type="hidden" value="{$sveawebpay_md5|escape:'html':'UTF-8'}"/>
               <input id="sveawebpay_security_number" name="sveawebpay_security_number" type="text" class="form-control input-sm not_unifrom not_uniform" onblur="getAddressSveawebpay()" />
            </div>
        {/if}

        {foreach from=$OPC_FIELDS[$OPC_GLOBALS->object->customer] item='fields' name='f_row_fields'}
            <div class="row">
                {foreach from=$fields item='field' name='f_fields'}
                    {include file="./controls.tpl" field=$field cant_fields=$smarty.foreach.f_fields.total}
                {/foreach}
            </div>
        {/foreach}

        {if $CONFIGS.OPC_ENABLE_PRIVACY_POLICY && !$IS_LOGGED}
            <div class="row">
                <div class="form-group col-xs-12 clear clearfix" id="div_privacy_policy">
                    <p id="p_privacy_policy">
                        <label for="privacy_policy">
                            <input type="checkbox" class="not_unifrom not_uniform" name="privacy_policy" id="privacy_policy" value="1" {if $checkedTOS}checked="checked"{/if}/>
                            {l s='I have read and accept the Privacy Policy.' mod='onepagecheckoutps'}
                            <span class="read">{l s='(read)' mod='onepagecheckoutps'}</span>
                        </label>
                    </p>
                </div>
            </div>
        {/if}
    </section>
{/if}

{*if !$IS_LOGGED*}
    <div id="hook_create_account" class="col-xs-12">
        {$HOOK_CREATE_ACCOUNT_FORM|escape:'htmlall':'UTF-8':false:true}
    </div>
{*/if*}

{if $addresses_tab}
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#delivery_address_container" data-toggle="tab">{l s='Delivery address' mod='onepagecheckoutps'}</a>
        </li>
        <li>
            <a href="#invoice_address_container" data-toggle="tab">{l s='Invoice address' mod='onepagecheckoutps'}</a>
        </li>
    </ul>
{/if}

<div class="{if $addresses_tab}tab-content{/if}">
    {if isset($OPC_FIELDS[$OPC_GLOBALS->object->delivery]) && sizeof($OPC_FIELDS[$OPC_GLOBALS->object->delivery]) > 1}
        {if ($CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL && $IS_VIRTUAL_CART) or !$IS_VIRTUAL_CART}
            {if not $addresses_tab}
                <h5 id="p_delivery_address" class="onepagecheckoutps_p_step p_address">{l s='Delivery address' mod='onepagecheckoutps'}</h5>
            {/if}
            <section id="delivery_address_container" class="{if $addresses_tab}page-product-box tab-pane active{/if}">
                <div class="fields_container">
                    {foreach from=$OPC_FIELDS[$OPC_GLOBALS->object->delivery] item='fields' name='f_row_fields'}
                        <div class="row">
                            {foreach from=$fields item='field' name='f_fields'}
                                {include file="./controls.tpl" field=$field cant_fields=$smarty.foreach.f_fields.total}
                            {/foreach}
                        </div>
                    {/foreach}
                </div>
            </section>
        {/if}
    {/if}

    {if isset($OPC_FIELDS[$OPC_GLOBALS->object->invoice])}
        {if $CONFIGS.OPC_ENABLE_INVOICE_ADDRESS}
            {if not $addresses_tab}
                <h5 id="p_invoice_address" class="onepagecheckoutps_p_step p_address">{l s='Invoice address' mod='onepagecheckoutps'}</h5>
            {/if}
            <section id="invoice_address_container" class="{if $addresses_tab}page-product-box tab-pane{/if}">
                <div class="row {if $CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS}hidden{/if}">
                    <div class="form-group col-xs-12">
                        <label for="checkbox_create_invoice_address">
                            <input type="checkbox" {if $is_need_invoice}checked="true"{/if} name="checkbox_create_invoice_address" id="checkbox_create_invoice_address" class="input_checkbox not_unifrom not_uniform"/>
                            {l s='I want to set another address for my invoice.' mod='onepagecheckoutps'}
                        </label>
                    </div>
                </div>
                <div class="fields_container">
                    {foreach from=$OPC_FIELDS[$OPC_GLOBALS->object->invoice] item='fields' name='f_row_fields'}
                        <div class="row">
                            {foreach from=$fields item='field' name='f_fields'}
                                {include file="./controls.tpl" field=$field cant_fields=$smarty.foreach.f_fields.total}
                            {/foreach}
                        </div>
                    {/foreach}
                </div>
            </section>
        {/if}
    {/if}
</div>