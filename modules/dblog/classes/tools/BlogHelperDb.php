<?php
/**
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
 */

class BlogHelperDb
{
	const _VERSION_  = '1.0.2';
	private $class;
	private $instance;
	private $definition;
	public function __construct($class_name)
	{
		$this->class = $class_name;
		$this->instance = new $class_name();
		$class = new ReflectionClass($class_name);
		$this->definition = $class->getStaticPropertyValue('definition');
		return $this;
	}
	public function installDb()
	{
		$sql = array();
		$exists_fields_shop = false;
		$exists_fields_lang = false;

		$sql['default'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->definition['table'].'`
			(%fields%) ENGINE='._MYSQL_ENGINE_.'  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		$sql['lang'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->definition['table'].'_lang`
			(%fields%) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$sql['shop'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->definition['table'].'_shop`
			(%fields%) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
		$fields = array(
			'default' => array(
				$this->definition['primary'] => '`'.$this->definition['primary'].'` int(10) signed NOT NULL AUTO_INCREMENT'
			),
			'lang' => array(
				$this->definition['primary'] => '`'.$this->definition['primary'].'` int(10) signed NOT NULL',
				'`id_lang` int(10) signed NOT NULL'
			),
			'shop' => array(
				$this->definition['primary'] => '`'.$this->definition['primary'].'` int(10) signed NOT NULL',
				'`id_shop` int(11) signed NOT NULL'
			)
		);
		if (isset($this->definition['multilang_shop']) && $this->definition['multilang_shop'])
			$fields['lang'][] = '`id_shop` int(11) unsigned NOT NULL';
		foreach ($this->definition['fields'] as $key => $field)
		{
			$sql_type = $this->getSQLType($field['type'], (isset($field['size']) ? $field['size'] : null));
			$field_sql = '`'.pSQL($key).'` '.$sql_type
				.' '.($sql_type != 'text' ? $this->getSQLDefaultVal($key,
											(isset($field['validate']) ? $field['validate'] : 'isAnything'),
											(isset($field['required']) && $field['required'])) : '');
			if (isset($field['lang']) && $field['lang'])
			{
				$fields['lang'][] = $field_sql;
				$exists_fields_lang = true;
			}
			if (isset($field['shop']) && $field['shop'])
			{
				$fields['shop'][] = $field_sql;
				$exists_fields_shop = true;
			}
			if (!isset($field['lang']) || (isset($field['lang']) && !$field['lang']))
				$fields['default'][] = $field_sql;
		}
		$fields['default'][] = 'PRIMARY KEY (`'.$this->definition['primary'].'`)';
		foreach ($sql as $type => $s)
		{
			if ($type == 'lang' && !$exists_fields_lang || $type == 'shop' && !$exists_fields_shop)
				continue;
			$this->execute($s, $fields[$type]);
		}
	}
	public function uninstallDb()
	{
		$sql = array();
		$exists_fields_shop = false;
		$exists_fields_lang = false;
		foreach ($this->definition['fields'] as $field)
		{
			if (isset($field['lang']) && $field['lang'])
				$exists_fields_lang = true;
			if (isset($field['shop']) && $field['shop'])
				$exists_fields_shop = true;
		}
		$sql['default'] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$this->definition['table'].'`';
		if ($exists_fields_lang)
			$sql['lang'] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$this->definition['table'].'_lang`';
		if ($exists_fields_shop)
			$sql['shop'] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.$this->definition['table'].'_shop`';
		foreach ($sql as $s)
			Db::getInstance()->execute($s);
	}
	public function getSQLType($field_type, $size = null)
	{
		if ($field_type == ObjectModel::TYPE_STRING || $field_type == ObjectModel::TYPE_HTML || $field_type == ObjectModel::TYPE_NOTHING)
		{
			if (is_null($size))
				return 'text';
			else
				return 'varchar('.(int)$size.')';
		}
		elseif ($field_type == ObjectModel::TYPE_FLOAT)
			return 'decimal(20,6)';
		elseif ($field_type == ObjectModel::TYPE_INT)
			return 'int('.(is_null($size) ? 10 : (int)$size).') signed';
		elseif ($field_type == ObjectModel::TYPE_DATE)
			return 'datetime';
		elseif ($field_type == ObjectModel::TYPE_BOOL)
			return 'tinyint(1)';
		return 'text';
	}
	public function getSQLDefaultVal($field, $validate, $required = false)
	{
		$default_val = ($required ? 'NOT NULL' : '');
		switch ($validate)
		{
			case 'isPrice':
				return $default_val.' DEFAULT "0.000000"';
			case 'isBool':
				return $default_val.' DEFAULT "'.(isset($this->instance->{$field}) && $this->instance->{$field} ? pSQL($this->instance->{$field}) : '0').'"';
			case 'isDateFormat':
				return $default_val.' DEFAULT "'
				.(isset($this->instance->{$field}) && $this->instance->{$field} ? pSQL($this->instance->{$field}) : '0000-00-00 00:00:00').'"';
			default:
				return ($required ?
					$default_val.(isset($this->instance->{$field}) && $this->instance->{$field} ? ' DEFAULT "'.pSQL($this->instance->{$field}).'"' : '')
					: (isset($this->instance->{$field}) && $this->instance->{$field} ? ' DEFAULT "'.pSQL($this->instance->{$field}).'"' : 'DEFAULT NULL'));
		}
	}
	public function execute($sql, $fields)
	{
		$sql = str_replace('%fields%', implode(',', $fields), $sql);
		Db::getInstance()->execute($sql);
	}
	public function getAll()
	{
		return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.$this->definition['table'].'`');
	}
	public function getById($id)
	{
		return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$this->definition['table'].'` WHERE `'.$this->definition['primary'].'` = '.(int)$id);
	}
	public function getByFieldAndValue($field, $value)
	{
		return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$this->definition['table'].'` WHERE `'.pSQL($field).'` = "'.pSQL($value).'"');
	}
	/**
	 * @return $this
	 */
	public static function loadClass($class_name)
	{
		return new self($class_name);
	}
}