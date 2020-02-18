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

class BlogCategory extends ObjectBlog
{
	public $name;
	public $meta_title;
	public $meta_keyword;
	public $meta_description;
	public $link_rewrite;

	public static $definition = array(
		'table' => 'blog_category',
		'primary' => 'id_blog_category',
		'multilang' => true,
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'size' => 512 , 'lang' => true, 'validate' => 'isCatalogName', 'required' => true),
			'meta_title' => array('type' => self::TYPE_HTML, 'size' => 512, 'validate' => 'isCleanHtml'),
			'meta_keyword' => array('type' => self::TYPE_HTML, 'size' => 512 , 'validate' => 'isCleanHtml'),
			'meta_description' => array('type' => self::TYPE_HTML,'validate' => 'isCleanHtml'),
			'link_rewrite' =>	array(
				'type' => self::TYPE_STRING,
				'lang' => true,
				'validate' => 'isLinkRewrite',
				'required' => true,
				'size' => 128
			)
		)
	);
	public static function getSimpleCategories($id_lang)
	{
		$query = new DbQuery();
		$query->select('cl.`'.self::getIdTable().'`, cl.`name`');
		$query->from(self::getTable(), 'c');
		$query->leftJoin(self::getTableLang(), 'cl', 'cl.`'.self::getIdTable().'` = c.`'.self::getIdTable().'` AND cl.`id_lang` = '.(int)$id_lang);

		return Db::getInstance()->executeS($query->build());
	}

	public static function getCategories($id_lang)
	{
		$query = new DbQuery();
		$query->select('c.* , cl.*');
		$query->from(self::getTable(), 'c');
		$query->leftJoin(self::getTableLang(), 'cl', 'cl.`'.self::getIdTable().'` = c.`'.self::getIdTable().'` AND cl.`id_lang` = '.(int)$id_lang);

		return Db::getInstance()->executeS($query->build());
	}
	public static function getInstanceCategoryByLinkRewrite($link_rewrite)
	{
		$context = Context::getContext();
		if (!Validate::isLinkRewrite($link_rewrite))
			return new self();
		$id_category = Db::getInstance()->getValue('SELECT '.self::getIdTable()
			.' FROM '.self::getPrefixTableLang().' WHERE link_rewrite = "'.pSQL($link_rewrite).'"');
		if (!$id_category)
			return new self();
		return new self($id_category, $context->language->id);
	}
} 