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
* @author    Goryachev Dmitry    <dariusakafest@gmail.com>
* @copyright 2007-2016 Goryachev Dmitry
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<div class="item_file row">
    <div class="col-lg-3">
        <img class="img-responsive" src="{$path|escape:'quotes':'UTF-8'}">
    </div>
    <div class="col-lg-2">
        <button class="btn btn-danger" data-delete-file="{$name|escape:'quotes':'UTF-8'}" data-file-id="{$id|escape:'quotes':'UTF-8'}" type="button">
            <i class="icon-remove"></i>
            {l s='Delete' mod='dblog'}
        </button>
    </div>
</div>