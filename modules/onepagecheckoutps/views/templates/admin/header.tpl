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

<script>
    var remote_addr = '{$paramsBack.remote_addr|escape:'htmlall':'UTF-8'}';

    var module_dir = "{$paramsBack.ONEPAGECHECKOUTPS_DIR|escape:'htmlall':'UTF-8'}";
    var module_img = "{$paramsBack.ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}";
    var pts_static_token = '{$paramsBack.OPC_STATIC_TOKEN|escape:'htmlall':'UTF-8'}';
    var class_name = 'App{$paramsBack.MODULE_PREFIX|escape:'htmlall':'UTF-8'}';

    var ADDONS = {$paramsBack.ADDONS|intval}
    //status codes
    var ERROR_CODE = {$paramsBack.ERROR_CODE|intval};
    var SUCCESS_CODE = {$paramsBack.SUCCESS_CODE|intval};

    var onepagecheckoutps_dir = '{$paramsBack.ONEPAGECHECKOUTPS_DIR|escape:'htmlall':'UTF-8'}';
    var onepagecheckoutps_img = '{$paramsBack.ONEPAGECHECKOUTPS_IMG|escape:'htmlall':'UTF-8'}';
    var GLOBALS_JS = {$paramsBack.GLOBALS_JS|escape:'quotes':'UTF-8'};
    var id_language = Number({$paramsBack.DEFAULT_LENGUAGE|intval});
    var id_language_default = Number({$paramsBack.DEFAULT_LENGUAGE|intval});
    var iso_lang_backoffice_shop = '{$paramsBack.iso_lang_backoffice_shop|escape:'htmlall':'UTF-8'}';

    var languages = new Array();
    {foreach from=$paramsBack.LANGUAGES item=language name=f_languages}
        languages.push({$language.id_lang|intval});
    {/foreach}
    var static_token = '{$paramsBack.STATIC_TOKEN|escape:'htmlall':'UTF-8'}';
</script>

{foreach from=$paramsBack.JS_FILES item="file"}
    <script type="text/javascript" src="{$file|escape:'htmlall':'UTF-8'}"></script>
{/foreach}
{foreach from=$paramsBack.CSS_FILES item="file"}
    <link type="text/css" rel="stylesheet" href="{$file|escape:'htmlall':'UTF-8'}"/>
{/foreach}

<script>
    var Msg = {ldelim}
        update_ship_to_pay: {ldelim}
            off: "{l s='Updating association...' mod='onepagecheckoutps' js=1}",
            on: "{l s='Update' mod='onepagecheckoutps' js=1}"
        {rdelim},
        change: "{l s='Change' mod='onepagecheckoutps' js=1}",
        only_gif: "{l s='Only gif images are allowed.' mod='onepagecheckoutps' js=1}",
        select_file: "{l s='You must select one file.' mod='onepagecheckoutps' js=1}",
        edit_field: "{l s='Edit field.' mod='onepagecheckoutps' js=1}",
        new_field: "{l s='New field.' mod='onepagecheckoutps' js=1}",
        confirm_remove_field: "{l s='Are you sure to want remove this field?' mod='onepagecheckoutps' js=1}",
        cannot_remove_field: "{l s='Only custom fields can be removed' mod='onepagecheckoutps' js=1}",
        manage_field_options: "{l s='Manage field options' mod='onepagecheckoutps' js=1}",
        add_IP: "{l s='Add IP' mod='onepagecheckoutps' js=1}"
    {rdelim};
</script>

<div class="pts bootstrap row">
    {foreach from=$paramsBack.WARNINGS item='warning'}
        <div class="alert alert-warning">
            {$warning|escape:'htmlall':'UTF-8'}
        </div>
    {/foreach}
</div>