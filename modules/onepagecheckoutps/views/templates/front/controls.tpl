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
{math assign='num_col' equation='12/a' a=$cant_fields}

<div id="field_{if $field->object neq ''}{$field->object|escape:'htmlall':'UTF-8'}_{/if}{$field->name|escape:'htmlall':'UTF-8'}"
     class="form-group col-xs-{$num_col|intval} {if $field->required}required{/if} {if $cant_fields == 1}clear clearfix{/if}">
    {if $field->type_control eq $OPC_GLOBALS->type_control->textbox}
        <label for="{$field->name_control|escape:'htmlall':'UTF-8'}">
            {$field->description|escape:'htmlall':'UTF-8'}:
            <sup>{if $field->required}*{/if}</sup>
        </label>
        <input
            id="{$field->id_control|escape:'htmlall':'UTF-8'}"
            name="{$field->name_control|escape:'htmlall':'UTF-8'}"
            type="{if $OPC_GLOBALS->type->{$field->type} eq 'password' or $field->name == 'conf_passwd'}password{else}text{/if}"
            class="{$field->classes|escape:'htmlall':'UTF-8'} form-control input-sm not_unifrom not_uniform {if $field->is_custom}custom_field{/if}"
            data-field-name="{$field->name|escape:'htmlall':'UTF-8'}"
            data-validation="{$field->type|escape:'htmlall':'UTF-8'}{if $field->size neq 0 and $OPC_GLOBALS->type->{$field->type} eq 'string'},length{/if}"
            data-default-value="{$field->default_value|escape:'htmlall':'UTF-8'}"
            data-required="{$field->required|intval}"
            {if $field->name == 'address' && $CONFIGS.OPC_AUTOCOMPLETE_GOOGLE_ADDRESS}autocomplete="off"{/if}
            {if !$field->required}data-validation-optional="true"{/if}
            {if isset($field->error_message) && $field->error_message neq ''}data-validation-error-msg="{$field->error_message|escape:'htmlall':'UTF-8'}"{/if}
            {if $OPC_GLOBALS->type->{$field->type} eq 'string'}data-validation-length="max{$field->size|intval}"{/if}
            {*if $field->size neq 0}maxlength="{$field->size}"{/if*}
        />
    {elseif $field->type_control eq $OPC_GLOBALS->type_control->select}
        <label for="{$field->name_control|escape:'htmlall':'UTF-8'}">
            {$field->description|escape:'htmlall':'UTF-8'}:
            <sup>{if $field->required}*{/if}</sup>
        </label>
        <select
            id="{$field->id_control|escape:'htmlall':'UTF-8'}"
            name="{$field->name_control|escape:'htmlall':'UTF-8'}"
            class="{$field->classes|escape:'htmlall':'UTF-8'} form-control input-sm not_unifrom not_uniform {if $field->is_custom}custom_field{/if}"
            data-field-name="{$field->name|escape:'htmlall':'UTF-8'}"
            data-default-value="{$field->default_value|escape:'htmlall':'UTF-8'}"
            data-required="{$field->required|intval}"
            {if isset($field->error_message) && $field->error_message neq ''}data-validation-error-msg="{$field->error_message|escape:'htmlall':'UTF-8'}"{/if}>
            {if isset($field->options.empty_option) && $field->options.empty_option}
                <option value="" {if $field->default_value eq ''}selected{/if}>
                    {if $field->name_control eq 'delivery_id' or $field->name_control eq 'invoice_id'}
                        {l s='Create a new address' mod='onepagecheckoutps'}....
                    {else}
                        --
                    {/if}
                </option>
            {/if}
            {if isset($field->options.data)}
                {foreach from=$field->options.data item='item' name='f_options'}
                    <option data-text="{$item[$field->options.description]|escape:'htmlall':'UTF-8'}"
                            value="{$item[$field->options.value]|escape:'htmlall':'UTF-8'}" {if $field->default_value eq $item[$field->options.value]}selected{/if}>
                        {$item[$field->options.description]|escape:'htmlall':'UTF-8'}
                    </option>
                {/foreach}
            {/if}
        </select>
    {elseif $field->type_control eq $OPC_GLOBALS->type_control->checkbox}
        <label for="{$field->name_control|escape:'htmlall':'UTF-8'}">
            <input
                id="{$field->id_control|escape:'htmlall':'UTF-8'}"
                name="{$field->name_control|escape:'htmlall':'UTF-8'}"
                type="checkbox"
                class="{$field->classes|escape:'htmlall':'UTF-8'} not_unifrom not_uniform {if $field->is_custom}custom_field{/if}"
                {if $field->default_value}checked{/if}
                data-field-name="{$field->name|escape:'htmlall':'UTF-8'}"
                data-default-value="{$field->default_value|escape:'htmlall':'UTF-8'}"
                data-required="{$field->required|intval}"
                {if !$field->required}data-validation-optional="true"{/if}
                {if isset($field->error_message) && $field->error_message neq ''}data-validation-error-msg="{$field->error_message|escape:'htmlall':'UTF-8'}"{/if}
            />
            {$field->description|escape:'htmlall':'UTF-8'}
        </label>
    {elseif $field->type_control eq $OPC_GLOBALS->type_control->radio}
        <label>
            {$field->description|escape:'htmlall':'UTF-8'}:
            <sup>{if $field->required}*{/if}</sup>
        </label>
        <div class="row">
            {foreach from=$field->options.data item='item' name='f_options'}
                {math assign='num_col_option' equation='12/a' a=$smarty.foreach.f_options.total}
                <div class="col-xs-{$num_col_option|intval}">
                    <label for="{$field->name_control|escape:'htmlall':'UTF-8'}">
                        <input
                            id="{$field->id_control|escape:'htmlall':'UTF-8'}_{$item[$field->options.value]|escape:'htmlall':'UTF-8'}"
                            name="{$field->name|escape:'htmlall':'UTF-8'}"
                            type="radio"
                            class="{$field->classes|escape:'htmlall':'UTF-8'} not_unifrom not_uniform {if $field->is_custom}custom_field{/if}"
                            value="{$item[$field->options.value]|escape:'htmlall':'UTF-8'}"
                            {if $field->default_value eq $item[$field->options.value]}checked{/if}
                            data-field-name="{$field->name|escape:'htmlall':'UTF-8'}"
                            data-required="{$field->required|intval}"
                        />
                        {$item[$field->options.description]|escape:'htmlall':'UTF-8'}
                    </label>
                </div>
            {/foreach}
        </div>
    {elseif $field->type_control eq $OPC_GLOBALS->type_control->textarea}
        <label for="{$field->name_control|escape:'htmlall':'UTF-8'}">
            {$field->description|escape:'htmlall':'UTF-8'}:
            <sup>{if $field->required}*{/if}</sup>
        </label>
        <textarea
            id="{$field->id_control|escape:'htmlall':'UTF-8'}"
            name="{$field->name_control|escape:'htmlall':'UTF-8'}"
            class="{$field->classes|escape:'htmlall':'UTF-8'} form-control input-sm not_unifrom not_uniform {if $field->is_custom}custom_field{/if}"
            data-field-name="{$field->name|escape:'htmlall':'UTF-8'}"
            data-validation="{$field->type|escape:'htmlall':'UTF-8'}{if $field->size neq 0},length{/if}"
            data-default-value="{$field->default_value|escape:'htmlall':'UTF-8'}"
            data-required="{$field->required|intval}"
            {if !$field->required}data-validation-optional="true"{/if}
            {if isset($field->error_message) && $field->error_message neq ''}data-validation-error-msg="{$field->error_message|escape:'htmlall':'UTF-8'}"{/if}
            {if $OPC_GLOBALS->type->{$field->type} eq 'text'}data-validation-length="max{$field->size|intval}"{/if}
            ></textarea>
    {/if}
</div>