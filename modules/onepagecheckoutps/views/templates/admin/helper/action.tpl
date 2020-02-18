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

{if isset($form.actions) and is_array($form.actions) and count($form.actions)}
    {foreach from=$form.actions item='action'}
        <button type="{if isset($form.method) and $form.method eq 'post'}submit{else}button{/if}"
                {if isset($action.name)}
                    name="{$action.name|escape:'htmlall':'UTF-8'}" id="btn-{$action.name|escape:'htmlall':'UTF-8'}"
                {else}
                    name="form-{$key|escape:'htmlall':'UTF-8'}"
                {/if}
                class="btn btn-primary pull-right has-action {if isset($action.class)}btn-{$action.class|escape:'htmlall':'UTF-8'}{/if}">
            <i class="fa fa-save nohover"></i>
            {$action.label|escape:'htmlall':'UTF-8'}
        </button>
    {/foreach}
{/if}