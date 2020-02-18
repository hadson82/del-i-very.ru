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
		{l s='Go to' mod='onepagecheckoutps'} <a href="https://developers.facebook.com/apps/" target="_blank">{l s='Facebook Developer' mod='onepagecheckoutps'}</a>
		{l s='link and log in with your facebook credentials' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Click on' mod='onepagecheckoutps'} <b>"{l s='+ Add a New App' mod='onepagecheckoutps'}"</b> {l s='button' mod='onepagecheckoutps'}. {l s='A pop-up box will appear and choise ' mod='onepagecheckoutps'} <b>"{l s='Website' mod='onepagecheckoutps'}"</b>.
	</li>
    <li>
		{l s='Set name application and do click on ' mod='onepagecheckoutps'} <b>"{l s='Create New Facebook App ID' mod='onepagecheckoutps'}"</b> {l s='button' mod='onepagecheckoutps'}.
	</li>
    <li>
		{l s='Choise category' mod='onepagecheckoutps'} <b>"{l s='App for Pages' mod='onepagecheckoutps'}"</b> {l s='and do click on' mod='onepagecheckoutps'} <b>"{l s='Create App ID' mod='onepagecheckoutps'}"</b> {l s='button' mod='onepagecheckoutps'}.
	</li>
    <li>
		{l s='In the next page, do click on ' mod='onepagecheckoutps'} <b>"{l s='Skip Quick Start' mod='onepagecheckoutps'}"</b> {l s='button' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Select' mod='onepagecheckoutps'} <b>"{l s='Settings' mod='onepagecheckoutps'}"</b> {l s='from side menu' mod='onepagecheckoutps'}.
	</li>
	<li>
        {l s='Click on ' mod='onepagecheckoutps'} <b>"{l s='+ Add Platform' mod='onepagecheckoutps'}"</b> {l s='and choise' mod='onepagecheckoutps'} <b>"{l s='Website' mod='onepagecheckoutps'}"</b>.
		<br />
        {l s='Enter this in' mod='onepagecheckoutps'} <b>"{l s='Site URL' mod='onepagecheckoutps'}"</b>:
        <input class="disabled" style="width: 100%;" type="text" onclick="this.focus();this.select();" value="{$paramsBack.LINK->getPageLink('index', false)|escape:'htmlall':'UTF-8'}"></input>
		<br />
		{l s='Enter this in' mod='onepagecheckoutps'} <b>"{l s='App Domains' mod='onepagecheckoutps'}"</b>:
		<input class="disabled" style="width: 100%;" type="text" onclick="this.focus();this.select();" value="{$paramsBack.SHOP->domain|escape:'htmlall':'UTF-8'}"></input>
		<br />
		{l s='After that click on' mod='onepagecheckoutps'} <b>"{l s='Save Changes' mod='onepagecheckoutps'}"</b> {l s='button' mod='onepagecheckoutps'}.
		<br />
		<br />
		{l s='NOTE' mod='onepagecheckoutps'}: {l s='Enter your e-mail in' mod='onepagecheckoutps'} <b>"{l s='Contact Email' mod='onepagecheckoutps'}"</b> {l s='to make app availble to all user' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Select' mod='onepagecheckoutps'} <b>"{l s='Status & Review' mod='onepagecheckoutps'}"</b> {l s='menu at left sidebar and change' mod='onepagecheckoutps'} <b>"{l s='App status' mod='onepagecheckoutps'}"</b> {l s='to' mod='onepagecheckoutps'} <b>"{l s='Yes' mod='onepagecheckoutps'}"</b>. {l s='A pop-up box will appear for confirmation and hit' mod='onepagecheckoutps'} <b>"{l s='Confirm' mod='onepagecheckoutps'}"</b> {l s='button in the popup' mod='onepagecheckoutps'}.
	</li>
	<li>
		{l s='Select "Dashboard" menu from left sidebar. Add' mod='onepagecheckoutps'} <b>"{l s='API Key' mod='onepagecheckoutps'}"</b> {l s='and' mod='onepagecheckoutps'} <b>"{l s='Secret Key' mod='onepagecheckoutps'}"</b> {l s='to this form' mod='onepagecheckoutps'}.
	</li>
</ol>