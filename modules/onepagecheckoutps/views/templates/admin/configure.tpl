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

<div id="pts_content" class="pts bootstrap nopadding clear clearfix">
        <div class="row">
            {if isset($show_saved_message) and $show_saved_message}
                <br class="clearfix"/>
                <div class="clearfix col-xs-12">
                    <div class="alert alert-success">
                        {l s='Configuration was saved successful' mod='onepagecheckoutps'}
                    </div>
                </div>
            {/if}
            <div class="clear row-fluid clearfix col-xs-12">
                <div class="pts-menu-xs visible-xs visible-sm pts-menu">
                    <span class="belt text-center">
                        <i class="fa fa-align-justify fa-3x nohover"></i>
                    </span>
                    <div class="pts-menu-xs-container hidden"></div>
                </div>
                <div class="hidden-xs hidden-sm col-sm-3 col-lg-2 pts-menu">
                    <ul class="nav">
                        <li class="pts-menu-title hidden-xs hidden-sm">
                            <a>
                                {l s='Menu' mod='onepagecheckoutps'}
                            </a>
                        </li>
                        {foreach from=$paramsBack.HELPER_FORM.tabs item='tab' name='tabs'}
                            <li class="{if (isset($CURRENT_FORM) && $CURRENT_FORM eq $tab.href) || (not isset($CURRENT_FORM) && $smarty.foreach.tabs.first)}active{/if}">
                                <a href="#tab-{$tab.href|escape:'htmlall':'UTF-8'}" data-toggle="tab" class="{if isset($tab.sub_tab)}has-sub{/if}">
                                    <i class='fa fa-{if isset($tab.icon)}{$tab.icon|escape:'htmlall':'UTF-8'}{else}cogs{/if} fa-1x'></i>&nbsp;{$tab.label|escape:'htmlall':'UTF-8'}
                                </a>
                                {if isset($tab.sub_tab)}
                                    <div class="sub-tabs" data-tab-parent="{$tab.href|escape:'htmlall':'UTF-8'}" style="display: none;overflow: hidden;">
                                        <ul class="nav">
                                            {foreach from=$tab.sub_tab item='sub_tab'}
                                                <li class="{if (isset($CURRENT_FORM) && $CURRENT_FORM eq $sub_tab.href)}active{/if}">
                                                    <a href="#tab-{$sub_tab.href|escape:'htmlall':'UTF-8'}" data-toggle="tab">
                                                        <i class='fa {if isset($sub_tab.icon)}{$sub_tab.icon|escape:'htmlall':'UTF-8'}{else}{$tab.icon|escape:'htmlall':'UTF-8'}{/if} fa-1x'></i>&nbsp;{$sub_tab.label|escape:'htmlall':'UTF-8'}
                                                    </a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                </div>
                <div class="col-xs-12 col-md-10 pts-content">
                    <div class="panel pts-panel nopadding">
                        <div class="panel-heading main-head">
                            <span class="pull-right bold">{l s='Version' mod='onepagecheckoutps'}&nbsp;{$paramsBack.VERSION|escape:'htmlall':'UTF-8'}</span>
                            <span class="pts-content-current-tab">&nbsp;</span>
                        </div>
                        <div class="panel-body">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                {if isset($ANOTHER_MODULES) and file_exists($paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/another_modules.tpl')}
                                    <div class="tab-pane{if (isset($CURRENT_FORM) && $CURRENT_FORM eq 'another_modules')} active{/if}" id="tab-another_modules">
                                        {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/another_modules.tpl' modules=$ANOTHER_MODULES}
                                    </div>
                                {/if}
                                {if isset($ADDONS) and file_exists($paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/another_modules.tpl')}
                                    <div class="tab-pane{if (isset($CURRENT_FORM) && $CURRENT_FORM eq 'addons')} active{/if}" id="tab-addons">
                                        {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/another_modules.tpl' modules=$ADDONS}
                                    </div>
                                {/if}
                                {if isset($paramsBack.HELPER_FORM)}
                                    {if isset($paramsBack.HELPER_FORM.forms) and is_array($paramsBack.HELPER_FORM.forms) and count($paramsBack.HELPER_FORM.forms)}
                                        {foreach from=$paramsBack.HELPER_FORM.forms key='key' item='form' name='forms'}
                                            {if isset($form.modal) and $form.modal}{assign var='modal' value=1}{else}{assign var='modal' value=0}{/if}
                                            <div class="tab-pane {if (isset($CURRENT_FORM) && $CURRENT_FORM eq $form.tab) || (not isset($CURRENT_FORM) && $smarty.foreach.forms.first)}active{/if}" id="tab-{$form.tab|escape:'htmlall':'UTF-8'}">
                                                <form action="{$paramsBack.ACTION_URL|escape:'htmlall':'UTF-8'}" {if isset($form.method) and $form.method neq 'ajax'}method="{$form.method|escape:'htmlall':'UTF-8'}"{/if}
                                                      class="form form-horizontal clearfix {if isset($form.class)}{$form.class|escape:'htmlall':'UTF-8'}{/if}"
                                                      {if isset($form.id)}id="{$form.id|escape:'htmlall':'UTF-8'}"{/if}
                                                      autocomplete="off">
                                                    <div class="col-xs-12 {if not $modal}col-md-8{/if} content-form pts-content nopadding-xs">
                                                        {foreach from=$form.options item='option'}
                                                            <div class="form-group clearfix clear {if isset($option.hide_on) and $option.hide_on}hidden{/if}"
                                                                {if isset($option.data_hide)}data-hide="{$option.data_hide|escape:'htmlall':'UTF-8'}"{/if}
                                                                id="container-{$option.name|escape:'htmlall':'UTF-8'}">
                                                                <div class="row">
                                                                    {if isset($option.label)}
                                                                        <div class="col-xs-{if $modal}3{else}{if $option.type eq $paramsBack.GLOBALS->type_control->checkbox}9 pts-nowrap{else}12{/if} col-sm-6 col-md-5 nopadding-xs{/if}"
                                                                             title="{$option.label|escape:'quotes':'UTF-8'}">
                                                                            <label class="pts-label-tooltip col-xs-12 nopadding control-label">
                                                                                {$option.label|escape:'quotes':'UTF-8'}
                                                                                {if isset($option.tooltip)}
                                                                                    {include file='./helper/tooltip.tpl' option=$option}
                                                                                {/if}
                                                                            </label>
                                                                        </div>
                                                                    {/if}
                                                                    {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/form.tpl' option=$option global=$paramsBack.GLOBALS modal=$modal}
                                                                    <div class="clear clearfix"></div>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                    <div class="col-xs-12 nopadding clear clearfix">
                                                        <hr />
                                                        {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/action.tpl' form=$form key=$key modal=$modal}
                                                    </div>
                                                </form>
                                                {if isset($form.list) and is_array($form.list) and count($form.list)}
                                                    {if isset($form.list.headers) and is_array($form.list.headers) and count($form.list.headers)}
                                                        {if $form.tab eq 'required_fields'}
                                                            <div class="clearfix">
                                                                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-3 nopadding-xs">
                                                                    <div class="pull-left col-xs-12 nopadding">
                                                                        <span id="btn-manage_field_options" class="btn btn-default btn-block">
                                                                            <i class="fa fa-list nohover"></i>
                                                                            {l s='Manage field options' mod='onepagecheckoutps'}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-xs-12 col-sm-6 col-md-5 col-lg-3 nopadding-xs pull-right">
                                                                    <div class="pull-right pull-left-xs col-xs-12 nopadding">
                                                                        <span id="btn-new_register" class="btn btn-success btn-block">
                                                                            <i class="fa fa-edit nohover"></i>
                                                                            {l s='New custom field' mod='onepagecheckoutps'}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            {* Modal options *}
                                                            <form class="form form-horizontal clearfix hidden" id="form_manage_field_options">
                                                                <div class="col-xs-12 nopadding">
                                                                    <div class="row">
                                                                        <div class="col-xs-6">
                                                                            <span>{l s='Object' mod='onepagecheckoutps'}</span>
                                                                        </div>
                                                                        <div class="col-xs-6">
                                                                            <span>{l s='Field' mod='onepagecheckoutps'}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-xs-6">
                                                                            <select id="lst-manage-object" class="form-control" autocomplete="false"></select>
                                                                        </div>
                                                                        <div class="col-xs-6">
                                                                            <select id="lst-manage-field" class="form-control" disabled autocomplete="false">
                                                                                <option value="">--</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">&nbsp;</div>
                                                                    <div class="col-xs-12 nopadding">
                                                                        <div class="hidden" id="aux_clone_translatable_input">
                                                                            {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/input_text_lang.tpl'
                                                                            languages=$paramsBack.LANGUAGES input_name='' input_value=''}
                                                                        </div>
                                                                        <div class="clearfix">
                                                                            <span class="btn btn-success pull-right disabled" id="btn-add_field_option">{l s='Add' mod='onepagecheckoutps'}</span>
                                                                        </div>
                                                                        <table id="table-field-options">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="{*col-xs-5 nopadding*}">{l s='Value' mod='onepagecheckoutps'}</th>
                                                                                    <th class="">{l s='Description' mod='onepagecheckoutps'}</th>
                                                                                    <th class="">{l s='Action' mod='onepagecheckoutps'}</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody></tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="row">&nbsp;</div>
                                                                    <div class="row">
                                                                        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
                                                                            <span id="btn-update_field_options" class="btn btn-primary btn-block disabled">
                                                                                <i class="fa fa-save nohover"></i>
                                                                                {l s='Save' mod='onepagecheckoutps'}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            {*/Modal options *}
                                                        {/if}
                                                        <div class="row">&nbsp;</div>
                                                        <div class="table-responsive">
                                                            <div class="pts-overlay"></div>
                                                            <table class="table table-bordered" id="{$form.list.table|escape:'htmlall':'UTF-8'}">
                                                                <thead>
                                                                    <tr>
                                                                        {foreach from=$form.list.headers item='header_text' key='header'}
                                                                            <th {if $header eq 'actions'}class="col-sm-2 col-md-1 text_center"{/if}>{$header_text|escape:'htmlall':'UTF-8'}</th>
                                                                        {/foreach}
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    {/if}
                                                {/if}
                                            </div>
                                        {/foreach}
                                    {/if}
                                {/if}
                                <div class="tab-pane{if (isset($CURRENT_FORM) && $CURRENT_FORM eq 'ship_pay')} active{/if}" id="tab-ship_pay">
                                    <div class="row">
                                        <div class="clearfix col-xs-12 nopadding-xs">
                                            <div class="alert alert-info">
                                                <b>{l s='Choose the payment methods that are available according to the delivery method. (If you have no restrictions, not select anything to show all payments)' mod='onepagecheckoutps'}</b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">&nbsp;</div>
                                    <div id="ship-pay-container" class="row">
                                        {foreach from=$paramsBack.CARRIERS item='carrier'}
                                            <div class="col-xs-12 col-sm-6 col-md-4 clearfix nopadding-xs">
                                                <div class="panel clearfix panel-primary">
                                                    <div class="panel-heading">
                                                        <span class="panel-title">
                                                            <i class='fa fa-truck fa-1x nohover'></i>&nbsp;{$carrier.name|escape:'htmlall':'UTF-8'}
                                                        </span>
                                                    </div>
                                                    <div class="panel-body nopadding">
                                                        <div id="carrier_{$carrier.id_carrier|intval}" class="carrier_container">
                                                            {foreach from=$paramsBack.PAYMENTS item='payment'}
                                                                <label class="checkbox-inline">
                                                                    <input type="checkbox" id="payment_{$payment.id_module|intval}_{$carrier.id_carrier|intval}">&nbsp;{$payment.name|escape:'htmlall':'UTF-8'}
                                                                </label>
                                                            {/foreach}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                    <button type="button" class="btn btn-primary pull-right has-action" id="btn-update_ship_pay">
                                        <i class="fa fa-save nohover"></i>
                                        {l s='Save' mod='onepagecheckoutps'}
                                    </button>
                                </div>
                                <div class="tab-pane{if (isset($CURRENT_FORM) && $CURRENT_FORM eq 'fields_position')} active{/if}" id="tab-fields_position">
                                    <div class="row" id="fields-position">
                                        {foreach from=$paramsBack.FIELDS_POSITION item='rows' key='group_name'}
                                            <div class="col-xs-12 col-md-4">
                                                <div class="row">
                                                    <label>
                                                        {if $group_name eq 'customer'}
                                                            {l s='Customer' mod='onepagecheckoutps'}
                                                        {elseif $group_name eq 'delivery'}
                                                            {l s='Delivery' mod='onepagecheckoutps'}
                                                        {elseif $group_name eq 'invoice'}
                                                            {l s='Invoice' mod='onepagecheckoutps'}
                                                        {/if}
                                                        &nbsp;
                                                        <span class="label label-{if $group_name eq 'customer'}primary{elseif $group_name eq 'delivery'}success{elseif $group_name eq 'invoice'}warning{/if}">&nbsp;</span>
                                                    </label>
                                                </div>
                                                <div class="row">
                                                    <ol class="nested_fields_position list-group col-xs-12" data-group='{$group_name|escape:'htmlall':'UTF-8'}'>
                                                        {foreach from=$rows item='row'}
                                                            <li class="list-group-item li-row">
                                                                <ol class="list-group ol-row">
                                                                    {foreach from=$row item='field'}
                                                                        <li data-field="{$field->id|intval}">
                                                                            <label class="label label-{if $field->object eq 'customer'}primary{elseif $field->object eq 'delivery'}success{elseif $field->object eq 'invoice'}warning{/if}">{$field->description|escape:'htmlall':'UTF-8'}</label>
                                                                        </li>
                                                                    {/foreach}
                                                                </ol>
                                                            </li>
                                                        {/foreach}
                                                    </ol>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
{*                                   Payment methods*}
                                <div class="tab-pane{if (isset($CURRENT_FORM) && $CURRENT_FORM eq 'pay_methods')} active{/if}" id="tab-pay_methods">
                                    {assign var='filename_default' value=$paramsBack.ONEPAGECHECKOUTPS_IMG|cat:'payments/default.png'}

                                    <div class="col-xs-12 clearfix">&nbsp;</div>
                                    <div class="col-xs-12">
                                        <div class="alert alert-info">
                                            {l s='Here you can configure the images, titles and descriptions of the methods of payment. The recommended images size is 86x49 pixels.' mod='onepagecheckoutps'}
                                        </div>
                                    </div>

                                    <div id="payment-images-container" class="row">
                                        {foreach from=$paramsBack.PAYMENTS item='payment'}
                                            <div class="col-xs-12 col-md-6">
                                                <form autocomplete="off">
                                                    <div class="panel panel-primary">
                                                        <div class="panel-heading">
                                                            <span class="panel-title">
                                                                <i class='fa fa-credit-card fa-1x nohover'></i>&nbsp;{$payment.name|escape:'htmlall':'UTF-8'}
                                                            </span>
                                                        </div>
                                                        <div class="panel-body nopadding">
                                                            <input type="hidden" id="id_module_payment_{$payment.name|escape:'htmlall':'UTF-8'}" value="{$payment.id_module|escape:'htmlall':'UTF-8'}" />
                                                            <div id="payment_{$payment.name|escape:'htmlall':'UTF-8'}" class="payment_container">
                                                                <div class="col-xs-12">
                                                                    <form class="form-horizontal" role="form">
                                                                        <div class="form-group row">
                                                                            <label class="col-xs-12 col-md-3 control-label">{l s='Title' mod='onepagecheckoutps'}</label>
                                                                            <div class="col-xs-12 col-md-9">
                                                                                {if isset($payment.data.title)}
                                                                                    {assign var='input_value' value=$payment.data.title}{else}{assign var='input_value' value=[]}
                                                                                {/if}
                                                                                {include languages=$paramsBack.LANGUAGES input_name='txt-image_payment_title-'|cat:$payment.name
                                                                                file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/input_text_lang.tpl' input_value=$input_value}
                                                                            </div>
                                                                            <div class="clear clearfix"></div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-xs-12 col-md-3 control-label">{l s='Description' mod='onepagecheckoutps'}</label>
                                                                            <div class="col-xs-12 col-md-9">
                                                                                {if isset($payment.data.description)}
                                                                                    {assign var='input_value' value=$payment.data.description}{else}{assign var='input_value' value=[]}
                                                                                {/if}
                                                                                {include languages=$paramsBack.LANGUAGES input_name='ta-image_payment_description-'|cat:$payment.name
                                                                                file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/textarea_lang.tpl' input_value=$input_value}
                                                                            </div>
                                                                            <div class="clear clearfix"></div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-xs-12 col-md-3 control-label">{l s='Force display' mod='onepagecheckoutps'}</label>
                                                                            <div class="col-xs-12 col-md-9 simple-switch">
                                                                                <label class="pull-right-xs switch">
                                                                                    <input type="checkbox" class="switch-input" data-switch="force_display"
                                                                                       name="force_display" id="chk-force_display-{$payment.name|escape:'htmlall':'UTF-8'}"
                                                                                       {if $payment.force_display == 1}checked{/if} autocomplete="off">
                                                                                    <span class="switch-label" data-on="{l s='Yes' mod='onepagecheckoutps'}" data-off="{l s='No' mod='onepagecheckoutps'}"></span>
                                                                                    <span class="switch-handle"></span>
                                                                                </label>
                                                                            </div>
                                                                            <div class="clear clearfix"></div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <div class="hidden-xs hidden-sm col-md-3">&nbsp;</div>
                                                                            <div class="col-xs-12 col-md-9 pts-image-change-container">
                                                                                {assign var='filename' value=$paramsBack.ONEPAGECHECKOUTPS_PATH_ABSOLUTE|cat:'views/img/payments/'|cat:$payment.name|cat:'.gif'}
                                                                                <img class="img-thumbnail" id="image_payment_{$payment.name|escape:'htmlall':'UTF-8'}" height="49" width="86"
                                                                                    src="{if file_exists($filename)}{$paramsBack.ONEPAGECHECKOUTPS_IMG|cat:'payments/'|cat:$payment.name|cat:'.gif'}{else}{$filename_default|escape:'htmlall':'UTF-8'}{/if}?{$smarty.now|escape:'htmlall':'UTF-8'}" />
                                                                                <br />
                                                                                <a href="#" class="pts-change-image-handler">{l s='Change' mod='onepagecheckoutps'}&nbsp;<span class="pts-change-image-name">{l s='image' mod='onepagecheckoutps'}</span></a>
                                                                                <input id="file-image_payment-{$payment.name|escape:'htmlall':'UTF-8'}" type="file" class="hidden">
                                                                            </div>
                                                                            <div class="clear clearfix"></div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <div class="col-xs-12 col-md-6 col-lg-4 col-md-push-3">
                                                                                <span class="btn btn-primary btn-block save-image-payment has-action" id="btn-save_image_payment-{$payment.name|escape:'htmlall':'UTF-8'}">
                                                                                    <i class="fa fa-save nohover"></i>
                                                                                    {l s='Save' mod='onepagecheckoutps'}
                                                                                </span>
                                                                            </div>
                                                                            <div class="clear clearfix"></div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
{*                                SOCIAL LOGIN FORMS*}
                                {foreach from=$paramsBack.SOCIAL_LOGIN item='social_network' key='name_social_network'}
                                    <div class="tab-pane{if (isset($CURRENT_FORM) && $CURRENT_FORM eq 'social_login_'|cat:$name_social_network)} active{/if}" id="tab-social_login_{$name_social_network|escape:'htmlall':'UTF-8'}">
                                        <div class="row">
                                            <div class="clearfix col-xs-12 nopadding">
                                                <a data-social-modal="how-to-{$name_social_network|escape:'htmlall':'UTF-8'}" role="button" class="btn btn-info handler-modal-social-login">
                                                    <i class="fa fa-question-circle nohover"></i>
                                                    {l s='How to I get this info?' mod='onepagecheckoutps'}
                                                </a>

                                                <!-- Modal -->
                                                <div id="how-to-{$name_social_network|escape:'htmlall':'UTF-8'}" class="hidden" tabindex="-1" role="dialog">
                                                    <div class="row clearfix">&nbsp;</div>
                                                    {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/social/'|cat:{$name_social_network|escape:'htmlall':'UTF-8'}|cat:'.tpl' paramsBack=$paramsBack}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix">&nbsp;</div>
                                        <div id="social_login_{$name_social_network|escape:'htmlall':'UTF-8'}-container" class="row">
                                            <div class="form-group clearfix clear">
                                                <div class="col-xs-12 col-sm-6 col-md-5 nopadding-xs" title="{l s='API Key' mod='onepagecheckoutps'} {$social_network->name_network|escape:'quotes':'UTF-8'}">
                                                    <label class="pts-label-tooltip col-xs-12 nopadding control-label">
                                                        {l s='API Key' mod='onepagecheckoutps'}&nbsp;{$social_network->name_network|escape:'quotes':'UTF-8'}
                                                    </label>
                                                </div>
                                                {capture assign='id_lang'}{l s='API Key' mod='onepagecheckoutps'}{/capture}
                                                {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/form.tpl' global=$paramsBack.GLOBALS
                                                option=['name' => 'social_login_id','prefix' => 'txt','label' => $id_lang,'type' => $paramsBack.GLOBALS->type_control->textbox,'value' => $social_network->client_id]}
                                            </div>

                                            <div class="form-group clearfix clear">
                                                <div class="col-xs-12 col-sm-6 col-md-5 nopadding-xs" title="{l s='Secret Key' mod='onepagecheckoutps'} {$social_network->name_network|escape:'quotes':'UTF-8'}">
                                                    <label class="pts-label-tooltip col-xs-12 nopadding control-label">
                                                        {l s='Secret Key' mod='onepagecheckoutps'}&nbsp;{$social_network->name_network|escape:'quotes':'UTF-8'}
                                                    </label>
                                                </div>
                                                {capture assign='id_lang'}{l s='Secret Key' mod='onepagecheckoutps'}{/capture}
                                                {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/form.tpl' global=$paramsBack.GLOBALS
                                                option=['name' => 'social_login_secret','prefix' => 'txt','label' => $id_lang, 'type' => $paramsBack.GLOBALS->type_control->textbox,'value' => $social_network->client_secret]}
                                            </div>
                                        </div>

                                        <div class="col-xs-12 nopadding">
                                            <hr />
                                            {capture assign='save_lang'}{l s='Save' mod='onepagecheckoutps'}{/capture}
                                            {include file=$paramsBack.ONEPAGECHECKOUTPS_TPL|cat:'views/templates/admin/helper/action.tpl'
                                                form=['method' => 'ajax', 'tab' => 'social_login_'|cat:$name_social_network,
                                                    'actions' => ['save' => ['label' => $save_lang, 'class' => 'save-social_login', 'icon' => 'save']]]}
                                        </div>
                                    </div>
                                {/foreach}
{*                                END SOCIAL LOGIN FORMS*}
                                 <div id="tab-translate" class="tab-pane">
                                    <div class="row">
                                        <div class="col-md-12 nopadding">
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <span>{l s='Select language' mod='onepagecheckoutps'}</span>
                                                    <select class="form-control" id="lst-id_lang">
                                                        {foreach $paramsBack.LANGUAGES as $language}
                                                            <option value="{$language.iso_code|escape:'htmlall':'UTF-8'}" {if $paramsBack.id_lang == $language.id_lang} selected="selected" {/if}>
                                                                {$language.name|escape:'htmlall':'UTF-8'}
                                                            </option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-default" id="btn-save-translation" data-action ="save">
                                                        <i class="fa fa-floppy-o nohover"></i> {l s='Save' mod='onepagecheckoutps'}
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-default" id="btn-save-download-translation" data-action="save_download">
                                                        <i class="fa fa-download nohover"></i> {l s='Save and Download' mod='onepagecheckoutps'}
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-default" id="btn-share-translation">
                                                        <i class="fa fa-share nohover"></i> {l s='Share us your translation' mod='onepagecheckoutps'}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clear clearfix">&nbsp;</div>
                                        <div class="col-md-12 nopadding">
                                            <div class="alert alert-warning">
                                                {l s='Some expressions use the syntax' mod='onepagecheckoutps'}: %s. {l s='Not replace, don\'t modified this' mod='onepagecheckoutps'}.
                                            </div>
                                        </div>
                                        <div class="col-md-12 overlay-translate hidden">
                                            <img src="{$paramsBack.ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}loader.gif">
                                        </div>
                                        <div class="col-md-12 nopadding">
                                            <h4 class="title_manage_settings text-primary">
                                                {l s='Management settings' mod='onepagecheckoutps'}
                                            </h4>
                                        </div>
                                        <div class="col-md-12 nopadding" id="content_translations">
                                            <div class="panel-group">
                                                {foreach $paramsBack.array_label_translate as $key => $value}
                                                    {if $key !== 'translate_language'}
                                                        <div class="panel content_translations" data-file="{$key|escape:'htmlall':'UTF-8'}">
                                                            <div class="panel-heading" style="white-space: normal; padding: 0px;">
                                                                <h4 class="panel-title clearfix" style="text-transform: none; font-weight: bold;">
                                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" href="#collapse_{$key|escape:'htmlall':'UTF-8'}">
                                                                        <span>{l s='File' mod='onepagecheckoutps'}: {$key|escape:'htmlall':'UTF-8'}</span>
                                                                        <span><i class="indicator pull-right fa {if isset($value.empty_elements)} fa-minus {else} fa-plus {/if} fa-2x"></i></span>
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            <div id="collapse_{$key|escape:'htmlall':'UTF-8'}" class="panel-collapse collapse {if isset($value.empty_elements)} in {/if}">
                                                                <div class="panel-body">
                                                                    <div class="content_text-translation table-responsive">
                                                                        <table class="table">
                                                                            {foreach $value as $key_label => $label_translate}
                                                                                {if $key_label !== 'empty_elements'}
                                                                                    <tr>
                                                                                        <td>
                                                                                            <label for="{$key_label|escape:'htmlall':'UTF-8'}" class="control-label col-sm-12">
                                                                                                {$label_translate['en']|escape:'htmlall':'UTF-8'}
                                                                                            </label>
                                                                                        </td>
                                                                                        <td>=</td>
                                                                                        <td class="input_content_translation" width="60%">
                                                                                            <input type="hidden" value="{$key|escape:'htmlall':'UTF-8'}" name="{$key_label|escape:'htmlall':'UTF-8'}">
                                                                                            <input type="text" class="form-control {if empty($label_translate['lang_selected'])} input-error-translate {/if}" value="{$label_translate['lang_selected']|escape:'htmlall':'UTF-8'}" name="{$key_label|escape:'htmlall':'UTF-8'}">
                                                                                        </td>
                                                                                    </tr>
                                                                                {/if}
                                                                            {/foreach}
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="panel-footer">
                                                                <button class="btn btn-default pull-right" name="btn-save-translation-{$key|escape:'htmlall':'UTF-8'}" type="button" data-action="save">
                                                                    <i class="process-icon-save"></i> {l s='Save' mod='onepagecheckoutps'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-code_editors">
                                    <div class="col-md-12">
                                        {foreach $paramsBack.code_editors as $key => $row}
                                            <div class="col-md-12 nopadding">
                                                <h4>
                                                    {$key|escape:'htmlall':'UTF-8'}
                                                </h4>
                                                <div class="col-md-12">
                                                    {foreach $row as $value}
                                                        <form action="{$paramsBack.ACTION_URL|escape:'htmlall':'UTF-8'}" class="form-horizontal form_code_editors">
                                                            <h4>{$value.filename|escape:'htmlall':'UTF-8'}.{if $key === 'css'}css{else}js{/if}</h4>
                                                            <div class="form-group">
                                                                <textarea name="txt-{$key|escape:'htmlall':'UTF-8'}-{$value.filename|escape:'htmlall':'UTF-8'}" class="linedtextarea" rows="20" cols="60">{$value.content|escape:'htmlall':'UTF-8':false:true}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-default pull-right btn-save-code-editors" data-filepath="{$value.filepath|escape:'htmlall':'UTF-8'}" data-type="{$key|escape:'htmlall':'UTF-8'}" data-name="{$value.filename|escape:'htmlall':'UTF-8'}">
                                                                    {l s='Save' mod='onepagecheckoutps'}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                                <div id="tab-faqs" class="tab-pane"></div>
                                <div id="tab-suggestions" class="tab-pane">
                                    <div class="row">
                                        <div class="alert alert-info center-block clearfix">
                                            <div class="col-sm-12">
                                                <div class="col-sm-3 col-md-2">
                                                    <img src="{$paramsBack.ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}star.png" class="img-responsive">
                                                </div>
                                                <div class="col-sm-9 col-md-10 text-left content-text-suggestions">
                                                    {l s='Share with us your suggestions, functionalities and opinions' mod='onepagecheckoutps'}
                                                    <a id="suggestions-opinions">{l s='Here' mod='onepagecheckoutps'}</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-success center-block clearfix">
                                            <div class="col-sm-12">
                                                <div class="col-sm-3 col-md-2">
                                                    <img src="{$paramsBack.ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}support.png" class="img-responsive">
                                                </div>
                                                <div class="col-sm-9 col-md-10 text-left content-text-suggestions">
                                                    {l s='You have any questions or problems regarding our module' mod='onepagecheckoutps'}?
                                                    <a id="suggestions-contact">{l s='Contact us' mod='onepagecheckoutps'}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="col-xs">&nbsp;</div>
    <div class="col-xs-12">
        <div class="clearfix clear">
            <div class="panel panel-default" id="pts_content_credits">
                <div class="panel-heading">
                    <span class="panel-title">
                        <i class='fa fa-certificate fa-1x'></i>
                        User guides
                    </span>
                </div>
                <div class="panel-body nopadding">
                    <div class="">
                        <div class="clear clearfix">
                            <span>
                                <a target="_blank" href="{$paramsBack.ONEPAGECHECKOUTPS_DIR|escape:'htmlall':'UTF-8'}docs/index_es.html">
                                    <i class="fa fa-link"></i>
                                    User guide module (Spanish)
                                </a>
                            </span>
                            <br class="clear clearfix" />
                            <span>
                                <a target="_blank" href="{$paramsBack.ONEPAGECHECKOUTPS_DIR|escape:'htmlall':'UTF-8'}docs/index_en.html">
                                    <i class="fa fa-link"></i>
                                    User guide module (English)
                                </a>
                            </span>
                        </div>
                    </div>
                    <br class="clear clearfix" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xs">&nbsp;</div>