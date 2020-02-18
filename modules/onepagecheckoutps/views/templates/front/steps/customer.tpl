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

<div id="onepagecheckoutps_step_one_container" class="{if !$register_customer}{$classes|escape:'htmlall':'UTF-8'}{else}col-xs-12{/if}">
    <div class="loading_small"><i class="fa fa-spin fa-refresh fa-2x"></i></div>
    <div id="onepagecheckoutps_step_one">
        {include file="./../address.tpl"}

        {if $register_customer}
            <button type="button" id="btn_save_customer" class="btn btn-primary btn-block">
                <i class="fa fa-save fa-lg"></i>
                {l s='Save information' mod='onepagecheckoutps'}
            </button>
        {/if}
    </div>
</div>