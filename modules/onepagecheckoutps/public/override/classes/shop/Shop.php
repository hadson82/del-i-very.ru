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

class Shop extends ShopCore
{
    public static function addTableAssociation($table_name, $table_details)
    {
        if (!isset(Shop::$asso_tables[$table_name])) {
            Shop::$asso_tables[$table_name] = $table_details;
        } else {
            return false;
        }

        return true;
    }
}
