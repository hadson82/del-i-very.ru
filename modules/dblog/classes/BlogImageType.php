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

class BlogImageType extends ObjectBlog
{
	public $width;
	public $height;
	public $name;

	public static $definition = array(
		'table' => 'blog_image_type',
		'primary' => 'id_blog_image_type',
		'fields' => array(
			'width' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'height' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'name' => array('type' => self::TYPE_STRING, 'size' => 512 ,'validate' => 'isCatalogName', 'required' => true)
		)
	);

	public static function getTypes()
	{
		$result = Db::getInstance()->executeS('SELECT * FROM '.self::getPrefixTable());
		$tmp_items = array();
		if (count($result))
			foreach ($result as $item)
				$tmp_items[$item[self::getIdTable()]] = $item;
		return $tmp_items;
	}
} 