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

class FieldClass extends ObjectModel
{
    public $id;
    public $name;
    public $object;
    public $description;
    public $type;
    public $size;
    public $type_control;
    public $default_value;
    public $group;
    public $row;
    public $col;
    public $required;
    public $is_custom = 0;
    public $active;

    public static $definition = array(
        'table'          => 'opc_field',
        'primary'        => 'id_field',
        'multilang'      => true,
        'multishop'      => true,
        'multilang_shop' => true,
        'fields'         => array(
            'object'       => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
            'name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 50
            ),
            'type'         => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
            'size'         => array('type' => self::TYPE_INT, 'required' => true),
            'type_control' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
            'is_custom'    => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            /* Shop fields */
            'default_value' => array('type' => self::TYPE_STRING, 'shop' => 'true', 'required' => false, 'size' => 255),
            'group'         => array('type' => self::TYPE_STRING, 'shop' => 'true', 'required' => true),
            'row'           => array('type' => self::TYPE_INT, 'shop' => 'true', 'required' => true),
            'col' => array('type' => self::TYPE_INT, 'shop' => 'true', 'required' => true),
            'required' => array(
                'type' => self::TYPE_BOOL,
                'shop' => 'true',
                'validate' => 'isBool',
                'required' => true
            ),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => 'true', 'validate' => 'isBool', 'required' => true),
            /* Lang fields */
            'description' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => false,
                'size' => 255
            )
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        //create multishop assoc
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    /**
     * Prepare fields for ObjectModel class (add, update)
     * All fields are verified (pSQL, intval...)
     *
     * @return array All object fields
     */
    public function getFields()
    {
        $this->validateFields();
        $fields = $this->formatFields(self::FORMAT_COMMON);

        // Ensure that we get something to insert
        if (!$fields && isset($this->id) && Validate::isUnsignedId($this->id)) {
            $fields[$this->def['primary']] = $this->id;
        }

        return $fields;
    }

    public static function getAllFields(
        $id_lang = null,
        $id_shop = null,
        $object = null,
        $required = null,
        $active = null,
        $name_fields = array(),
        $order_by = 'fs.group, fs.row, fs.col',
        $is_custom = false
    ) {
        if (is_null($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

        $order_by = 'fs.group, fs.row, fs.col';

        //get fields
        $query = new DbQuery();
        $query->select('f.id_field');
        $query->from('opc_field', 'f');
        $query->innerJoin('opc_field_shop', 'fs', 'f.id_field = fs.id_field AND fs.id_shop = '.$id_shop);
        $query->where(!empty($object) ? 'f.object = "'.$object.'"' : '');
        $query->where(count($name_fields) ? 'f.name IN ("'.implode('","', $name_fields).'")' : '');
        $query->where(!empty($required) ? 'fs.required = '.(int) $required : '');
        $query->where(!empty($active) ? 'fs.active = '.(int) $active : '');

        if ($is_custom) {
            $query->where('f.is_custom = 1');
            $query->where('f.type_control in ("radio", "select")');
        }

        $query->orderBy($order_by);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $fields = array();

        if (is_array($result) && !empty($result)) {
            foreach ($result as $row) {
                $id_lang_tmp = $id_lang;

                if (!empty($id_lang)) {
                    $query = new DbQuery();
                    $query->from('opc_field_lang');
                    $query->where('id_field = '.$row['id_field']);
                    $query->where('id_lang = '.$id_lang);
                    $query->where('id_shop = '.$id_shop);
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

                    if (!$result) {
                        $id_lang_tmp = Configuration::get('PS_LANG_DEFAULT');
                    }
                }

                $fields[] = new FieldControl($row['id_field'], $id_lang_tmp, $id_shop);
            }
        }

        return $fields;
    }

    public static function getField($id_lang, $id_shop, $object, $name_field)
    {
        if (is_null($id_shop)) {
            $id_shop = ContextCore::getContext()->shop->id;
        }

        $query = new DbQuery();
        $query->select('id_field');
        $query->from('opc_field');
        $query->where('object = "'.$object.'"');
        $query->where('name = "'.$name_field.'"');
        $id_field = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        return new FieldControl($id_field, $id_lang, $id_shop);
    }

    public static function getNameFields($object, $required, $active)
    {
        $tmp_fields = array();

        $query = new DbQuery();
        $query->select('name');
        $query->from('opc_field');

        if (!empty($object)) {
            $query->where('object = "'.$object.'"');
        }
        if (!empty($required)) {
            $query->where('required = '.$required);
        }
        if (!empty($active)) {
            $query->where('active = '.$active);
        }

        $fields = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);

        if (count($fields)) {
            foreach ($fields as $field) {
                $tmp_fields[] = $field['name'];
            }
        }

        return $tmp_fields;
    }

    public static function getCustomFields($id = null)
    {
        $query = new DbQuery();
        $query->select('id_field');
        $query->from('opc_field');
        $query->where('is_custom = 1');
        if ($id) {
            $query->where('id_field = '.$id);
        }
        $result = Db::getInstance()->executeS($query);
        if ($result) {
            $id_lang       = Context::getContext()->cookie->id_lang;
            $custom_fields = array();
            foreach ($result as $id) {
                $field           = new FieldClass($id['id_field']);
                $field->options  = FieldOptionClass::getOptionsByIdField($id['id_field'], $id_lang);
                $custom_fields[] = $field;
            }

            return $custom_fields;
        }

        return $result;
    }

    public static function getDefaultValue($object, $name_field)
    {
        $query = new DbQuery();
        $query->select('id_field');
        $query->from('opc_field');
        $query->where('object = "'.$object.'"');
        $query->where('name = "'.$name_field.'"');
        $id_field = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        $field = new FieldClass($id_field);

        if ($name_field == 'id_country' && Configuration::get('PS_GEOLOCATION_ENABLED')) {
            return Context::getContext()->country->id;
        }

        return $field->default_value;
    }

    /**
     * Get Las row by group
     */
    public static function getLastRowByGroup($group)
    {
        $query = new DbQuery();
        $query->select('MAX(`row`)')->from('opc_field_shop')->where('`group` = \''.$group.'\'');

        return Db::getInstance()->getValue($query);
    }
}
