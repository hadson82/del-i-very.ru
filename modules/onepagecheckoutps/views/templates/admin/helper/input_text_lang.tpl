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

{foreach from=$languages item="language" name="f_languages"}
    <div class="translatable-field lang_{$language.id_lang|intval} {if $language.id_lang ne $paramsBack.DEFAULT_LENGUAGE}hide{/if} row">
        <div class="col-xs-12 col-md-9 nopadding input-group-md translatable-input">
            <input autocomplete="off" class="form-control" type="text" id="{$input_name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}"
                   name="{$input_name|escape:'htmlall':'UTF-8'}_{$language.id_lang|intval}"
               {if isset($input_value) and isset($input_value[$language.id_lang])}
                   value="{$input_value[$language.id_lang]|escape:'htmlall':'UTF-8'}"
               {/if}
               />
        </div>
        <div class="col-xs-3 translatable-flags nopadding-right">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                <i class="fa fa-flag nohover"></i>
                {$language.iso_code|escape:'htmlall':'UTF-8'}
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                {foreach from=$languages item=flag_language}
                    <li>
                        <a class="change-language" for="lang_{$flag_language.id_lang|intval}">
                            {$flag_language.name|escape:'htmlall':'UTF-8'}
                            {if $flag_language.id_lang eq $language.id_lang}
                                <i class="fa fa-flag-checkered nohover"></i>
                            {/if}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/foreach}