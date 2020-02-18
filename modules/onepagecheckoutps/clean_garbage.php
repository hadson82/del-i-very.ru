<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
 */

include dirname(__FILE__).'/../../config/config.inc.php';
include dirname(__FILE__).'/../../init.php';

$query = 'DELETE FROM '._DB_PREFIX_.'address WHERE id_customer = '.Configuration::get('OPC_ID_CUSTOMER');
Db::getInstance()->execute($query);

$query = new DbQuery();
$query->select('*');
$query->from('cart');
$query->where('id_cart NOT IN (SELECT id_cart FROM '._DB_PREFIX_.'orders)');

$carts = Db::getInstance()->executeS($query);

if (count($carts) > 0) {
    foreach ($carts as $cart) {
        $query = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.$cart['id_address_delivery'];
        $result = Db::getInstance()->executeS($query);
        
        if ((int)$cart['id_customer'] == (int)Configuration::get('OPC_ID_CUSTOMER') || !$result) {
            Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cart WHERE id_cart = '.(int) $cart['id_cart']);
            Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart = '.(int) $cart['id_cart']);
            Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int) $cart['id_cart']);
        }
    }
}

die('OK');
