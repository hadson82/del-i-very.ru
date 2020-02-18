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
<ol style="list-style-type: decimal">
	<li>
		{l s='Go to' mod='onepagecheckoutps'} <a href="https://code.google.com/apis/console/" target="_blank">{l s='Google Api Console' mod='onepagecheckoutps'}</a>
		{l s='link and log in with your Google account' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Click on "Create Project" and fill the field "PROJECT NAME" and hit "Create" button to save' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Go to "APIs" under "APIS & AUTH" and click search "Google+ API" and enable it' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Go to "CREDENTIALS" under "APIS & AUTH" and click on "Create new Client ID"' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Select' mod='onepagecheckoutps'} {l s='"APLICATION TYPE"' mod='onepagecheckoutps'}: <i>{l s='Web Application' mod='onepagecheckoutps'}</i>
		{l s='and type in the fields' mod='onepagecheckoutps'}:
		<br />
		{l s='"AUTHORIZED JAVASCRIPT ORIGINS"' mod='onepagecheckoutps'}:
		<input style="width: 100%;" type="text" onclick="this.focus();this.select();" value="{$paramsBack.SHOP_PROTOCOL|escape:'htmlall':'UTF-8'}{$paramsBack.SHOP->domain|escape:'htmlall':'UTF-8'}"></input>
		<br />
		{l s='"AUTHORIZED REDIRECT URI"' mod='onepagecheckoutps'}:
        <textarea style="width: 100%;" rows="4">{$paramsBack.LINK->getModuleLink('onepagecheckoutps', 'login', ['sv' => 'Google'])|escape:'htmlall':'UTF-8'}
        {if $paramsBack.LINK->getPageLink('index', true) != $paramsBack.LINK->getPageLink('index')}{$paramsBack.LINK->getModuleLink('onepagecheckoutps', 'login', ['sv' => 'Google'], true)|escape:'htmlall':'UTF-8'}{/if}
        </textarea>
		<br />
		{l s='and click on "Create Client ID"' mod='onepagecheckoutps'}
	</li>
	<li>
		{l s='Copy the Google generated "CLIENT ID" and "CLIENT SECRET" and insert them bellow' mod='onepagecheckoutps'}.
	</li>
</ol>