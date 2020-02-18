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

<br class="clear" />
<div class="col-xs-12 col-md-7">
    <div class="panel">
        <div class="panel-heading">
            {l s='Extra information order' mod='onepagecheckoutps'}
        </div>
        <div class="panel-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{l s='Field' mod='onepagecheckoutps'}</th>
                        <th>{l s='Value' mod='onepagecheckoutps'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$field_options item='option'}
                        <tr>
                            <td>{$option.field_description|escape:'htmlall':'UTF-8'}</td>
                            <td>
                                {if not empty($option.option_description) and not is_null($option.option_description)}
                                    {$option.option_description|escape:'htmlall':'UTF-8'}
                                {elseif not empty($option.value) and not is_null($option.value)}
                                    {$option.value|escape:'htmlall':'UTF-8'}
                                {else}--{/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">&nbsp;</div>