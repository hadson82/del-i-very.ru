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

class BlogTag extends ObjectBlog
{
	public $name;
	public $link_rewrite;

	public static $definition = array(
		'table' => 'blog_tag',
		'primary' => 'id_blog_tag',
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'size' => 512 , 'validate' => 'isCatalogName', 'required' => true),
			'link_rewrite' =>	array(
				'type' => self::TYPE_STRING,
				'validate' => 'isLinkRewrite',
				'required' => true,
				'size' => 128
			)
		)
	);

	public static function getTags()
	{
		return Db::getInstance()->executeS('SELECT * FROM '.self::getPrefixTable());
	}

	public static function convertSelect2DataTags($tags)
	{
		if (!is_array($tags))
			array();
		$select2_tags = array();
		if (count($tags))
		{
			foreach ($tags as $tag)
			{
				$select2_tags[] = array(
					'id' => $tag[self::getIdTable()],
					'text' => $tag['name']
				);
			}
		}
		return $select2_tags;
	}

	public static function getTagsByArticle($id_article, $id_lang)
	{
		$article = new BlogArticle($id_article, $id_lang);
		if (!Validate::isLoadedObject($article))
			return array();

		$query = new DbQuery();
		$query->select('t.*');
		$query->from(_BLOG_PREFIX_.'article_tag', 'at');
		$query->leftJoin(self::getTable(), 't', 't.`'.self::getIdTable().'` = at.`'.self::getIdTable().'`');
		$query->where('at.`'.BlogArticle::getIdTable().'` = '.(int)$id_article);

		return DB::getInstance()->executeS($query->build());
	}

	public static function existsTagName($name)
	{
		$result = Db::getInstance()->getRow('SELECT * FROM '.self::getPrefixTable().' WHERE name = "'.pSQL($name).'"');
		return (count($result) ? $result[self::getIdTable()] : false);
	}
	public static function existsTagId($id_tag)
	{
		$result = Db::getInstance()->getRow('SELECT * FROM '.self::getPrefixTable().' WHERE '.self::getIdTable().' = '.(int)$id_tag);
		return (count($result) ? $result[self::getIdTable()] : false);
	}

	public static function getInstanceTagByLinkRewrite($link_rewrite)
	{
		if (!Validate::isLinkRewrite($link_rewrite))
			return new self();
		$id_tag = Db::getInstance()->getValue('SELECT '.self::getIdTable()
			.' FROM '.self::getPrefixTable().' WHERE link_rewrite = "'.pSQL($link_rewrite).'"');
		if (!$id_tag)
			return new self();
		return new self($id_tag);
	}
} 