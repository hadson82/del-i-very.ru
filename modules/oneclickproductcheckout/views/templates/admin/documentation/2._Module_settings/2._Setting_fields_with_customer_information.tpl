{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    SeoSA <885588@bk.ru>
*  @copyright 2012-2017 SeoSA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h2 class="text-center">{l s='Setting fields with customer information' mod='oneclickproductcheckout'}</h2>
<hr>
{l s='Adjust each field. Make mandatory or optional. Display the field or not' mod='oneclickproductcheckout'}
{l s='Enter hint for field. Enter the text for each language. This hint for users on front-office.' mod='oneclickproductcheckout'}
<div class="alert alert-warning">
    {l s='If you make the field inactive, the data on it is generated randomly when ordering.' mod='oneclickproductcheckout'}
</div>
{get_image_lang path = '8.jpg'}
{l s='For fields' mod='oneclickproductcheckout'} <strong>{l s='"Home phone" "Mobile phone"' mod='oneclickproductcheckout'}</strong> {l s='enter mask.' mod='oneclickproductcheckout'}
{l s='This is a common format of the phone number in your country. For example +9(999)-99-99-99' mod='oneclickproductcheckout'}
<div class="alert alert-warning">
    {l s='Important: Be sure to use number "9" for the mask.' mod='oneclickproductcheckout'}
</div>
{get_image_lang path = '9.jpg'}
{l s='You can change the order of the fields. To do this, click on the field and hold, drag to the right place' mod='oneclickproductcheckout'}
{get_image_lang path = '10.jpg'}