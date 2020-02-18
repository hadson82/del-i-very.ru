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

class PaymentClass extends ObjectModel
{
    public $id;
    public $id_module;
    public $name;
    public $title;
    public $description;
    public $force_display;

    public static $definition = array(
        'table'          => 'opc_payment',
        'primary'        => 'id_payment',
        'multilang'      => true,
        'multilang_shop' => true,
        'fields'         => array(
            'id_module' => array('type' => self::TYPE_INT),
            'name'      => array('type' => self::TYPE_STRING),
            'force_display' => array('type' => self::TYPE_BOOL),
            /* Lang fields */
            'title'       => array('type' => self::TYPE_STRING, 'lang' => true),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true)
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        //create multishop assoc
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getPaymentByName($name)
    {
        $query = new DbQuery();
        $query->select(self::$definition['primary']);
        $query->from(self::$definition['table']);
        $query->where('name = \''.$name.'\'');

        $id_payment = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        if (!empty($id_payment)) {
            return $id_payment;
        }

        return null;
    }

    public static function getIdPaymentBy($field, $value)
    {
        $query = new DbQuery();
        $query->select(self::$definition['primary']);
        $query->from(self::$definition['table']);
        $query->where($field.' = '.$value);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
    }
}
