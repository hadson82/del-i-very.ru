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

class BlogArticle extends ObjectBlog
{
	public $name;
	public $content;
	public $preview;
	public $date_add = '0000-00-00';
	public $date_upd = '0000-00-00';
	public $id_employee;
	public $id_blog_category = 0;
	public $is_active = 1;
	public $is_comment = 1;
	public $is_only_verified_comments;
	public $meta_title;
	public $meta_keyword;
	public $meta_description;
	public $view_share_btn = 1;
	public $link_rewrite;

	public static $definition = array(
		'table' => 'blog_article',
		'primary' => 'id_blog_article',
		'multilang' => true,
		'fields' => array(
			'name' => array('type' => self::TYPE_STRING, 'size' => 512 , 'lang' => true, 'validate' => 'isCatalogName', 'required' => true),
			'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
			'preview' => array('type' => self::TYPE_STRING, 'size' => 512, 'validate' => 'isString'),
			'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt','required' => true),
			'id_blog_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'meta_title' => array('type' => self::TYPE_HTML, 'size' => 512,'validate' => 'isCleanHtml'),
			'meta_keyword' => array('type' => self::TYPE_HTML, 'size' => 512,'validate' => 'isCleanHtml'),
			'meta_description' => array('type' => self::TYPE_HTML,'validate' => 'isCleanHtml'),
			'is_comment' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'view_share_btn' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'is_active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'is_only_verified_comments' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'link_rewrite' =>	array(
				'type' => self::TYPE_STRING,
				'lang' => true,
				'validate' => 'isLinkRewrite',
				'required' => true,
				'size' => 128
			)
		)
	);

	public function add($autodate = true, $null_values = false)
	{
		$context = Context::getContext();
		$this->date_add = date('Y-m-d H:i:s');
		$this->id_employee = $context->employee->id;
		return parent::add($autodate, $null_values);
	}

	public function update($null_values = false)
	{
		$this->date_upd = date('Y-m-d H:i:s');
		if (!$this->is_only_verified_comments)
			BlogCommentArticle::setModeratedByArticle($this->id);
		return parent::update($null_values);
	}

	public function deleteProducts()
	{
		return Db::getInstance()->delete(_BLOG_PREFIX_.'article_product', self::getIdTable().' = '.(int)$this->id);
	}

	public function addProduct($id_product)
	{
		Db::getInstance()->insert(_BLOG_PREFIX_.'article_product', array(
			array(
				self::getIdTable() => $this->id,
				'id_product' => $id_product
			)
		));
	}

	public static function getSelect2Products($id_article, $id_lang)
	{
		$query = new DbQuery();
		$query->select('pl.`id_product` as id, pl.`name` as text');
		$query->from(_BLOG_PREFIX_.'article_product', 'a');
		$query->leftJoin(BlogDB::getTableLang('product'), 'pl', 'pl.`id_product` = a.`id_product` AND pl.`id_lang` = '.(int)$id_lang);
		$query->where('a.`'.self::getIdTable().'` = '.(int)$id_article);
		return Db::getInstance()->executeS($query->build());
	}

	public static function getSimpleArticles($id_lang)
	{
		$query = new DbQuery();
		$query->select('al.`'.self::getIdTable().'`, al.`name`');
		$query->from(self::getTable(), 'a');
		$query->leftJoin(self::getTableLang(), 'al', 'al.`'.self::getIdTable().'` = a.`'.self::getIdTable().'` AND al.`id_lang` = '.(int)$id_lang);
		return Db::getInstance()->executeS($query->build());
	}

	public static function getArticlesByTag($id_lang = null, $return_count = null, $id_tag, $start = 0, $limit = null, $active = true)
	{
		$query = new DbQuery();
		$query->from(self::getTable(), 'a');
			$query_article_tag = new DbQuery();
			$query_article_tag->select('at.`'.self::getIdTable().'`');
			$query_article_tag->from(_BLOG_PREFIX_.'article_tag', 'at');
			$query_article_tag->where('at.`'.self::getIdTable().'` = a.`'.self::getIdTable().'`'.
				(!is_null($id_tag) ? ' AND at.`'.BlogTag::getIdTable().'` = '.(int)$id_tag : ''));

		$query->where('EXISTS ('.$query_article_tag->build().') AND a.`is_active` = '.($active ? '1' : '0'));

		if (!is_null($return_count))
		{
			$query->select('COUNT(a.`'.self::getIdTable().'`)');
			return Db::getInstance()->getValue($query->build());
		}
		$context = Context::getContext();
		if (is_null($id_lang))
			$id_lang = $context->language->id;
		if (is_null($limit))
			$limit = BlogConf::getConf('LIMIT_ARTICLES');

		$query->select('a.`'.self::getIdTable().'`');
		$query->orderBy('a.`date_add` DESC');
		$query->limit((int)$start, (int)$limit);

		$result = Db::getInstance()->executeS($query->build());

		if (!count($result))
			return array();

		$ids_articles = array();
		foreach ($result as $item)
			$ids_articles[] = $item[self::getIdTable()];

		return self::getPropertiesArticlesByIds($id_lang, $ids_articles);
	}

	public static function getArticles($id_lang = null, $get_count = null, $id_category = null, $start = 0, $limit = null, $active = true)
	{
		$query = new DbQuery();
		$query->from(self::getTable(), 'a');
		$query->where('a.`is_active` = '.($active ? '1' : '0')
			.(!is_null($id_category) ? ' AND a.`'.BlogCategory::getIdTable().'` = '.(int)$id_category : ''));

		if (!is_null($get_count))
		{
			$query->select('COUNT(a.`'.self::getIdTable().'`)');
			return Db::getInstance()->getValue($query->build());
		}
		$context = Context::getContext();
		if (is_null($id_lang))
			$id_lang = $context->language->id;
		if (is_null($limit))
			$limit = BlogConf::getConf('LIMIT_ARTICLES');

		$query->select('a.`'.self::getIdTable().'`');
		$query->orderBy('a.`date_add` DESC');
		$query->limit((int)$limit, (int)$start);
		$result = Db::getInstance()->executeS($query->build());

		if (!count($result))
			return array();

		$ids_article = array();
		foreach ($result as $item)
			$ids_article[] = $item[self::getIdTable()];
		return self::getPropertiesArticlesByIds($id_lang, $ids_article);
	}

	public static function getPropertiesArticlesByIds($id_lang, $ids_article)
	{
		$query = new DbQuery();
		$query->select('a.*, al.*, cl.`name` as category, cl.`link_rewrite` as cat_link_rewrite,
			(SELECT COUNT(ca.`'.BlogCommentArticle::getIdTable().'`)
				FROM '.BlogCommentArticle::getPrefixTable().' ca
				WHERE ca.`'.self::getIdTable().'` = a.`'.self::getIdTable().'`
				AND ca.`is_moderated` = 1
				AND ca.`is_active` = 1
				AND ca.`deleted` = 0) as count_comment,
			CONCAT(e.`firstname`," ",e.`lastname`) as employee');
		$query->from(self::getTable(), 'a');
		$query->leftJoin(self::getTableLang(), 'al', 'al.`'.self::getIdTable().'` = a.`'.self::getIdTable().'` AND al.`id_lang` = '.(int)$id_lang);
		$query->leftJoin(BlogCategory::getTableLang(), 'cl', 'cl.`'.BlogCategory::getIdTable()
			.'` = a.`'.BlogCategory::getIdTable().'` AND cl.`id_lang` = '.(int)$id_lang);
		$query->leftJoin(BlogDB::getTable('employee'), 'e', 'e.`id_employee` = a.`id_employee`');
		$query->where('a.`'.self::getIdTable().'` IN ('.implode(',', array_map('intval', $ids_article)).')');
		$query->groupBy('a.`'.self::getIdTable().'`');
		$query->orderBy('a.`'.self::getIdTable().'` DESC');
		$result = Db::getInstance()->executeS($query->build());

		$query_tag = new DbQuery();
		$query_tag->select('at.*, t.*');
		$query_tag->from(_BLOG_PREFIX_.'article_tag', 'at');
		$query_tag->leftJoin(BlogTag::getTable(), 't', 't.`'.BlogTag::getIdTable().'` = at.`'.BlogTag::getIdTable().'`');
		$query_tag->where('at.`'.self::getIdTable().'` IN ('.implode(',', array_map('intval', $ids_article)).')');
		$tags = Db::getInstance()->executeS($query_tag->build());

		$articles = array();
		foreach ($result as $item)
			$articles[$item[self::getIdTable()]] = $item;
		foreach ($tags as $tag)
			$articles[$tag[self::getIdTable()]]['tags'][] = $tag;
		return $articles;
	}

	public static function getDataArticles($parameters = array())
	{
		$context = Context::getContext();
		$id_category = (isset($parameters['id_category']) ? $parameters['id_category'] : null);
		$id_tag = (isset($parameters['id_tag']) ? $parameters['id_tag'] : null);
		$p = (Tools::getValue('p') ? Tools::getValue('p') : 1);
		$n = (isset($parameters['n']) ? (int)$parameters['n'] : (int)BlogConf::getConf('LIMIT_ARTICLES'));

		$articles = array();
		$nb_articles = 0;
		if (is_null($id_tag))
		{
			$nb_articles = self::getArticles(null, true, $id_category);
			$articles = self::getArticles($context->language->id, null, $id_category, ($p - 1) * $n, $n);
		}
		if (!is_null($id_tag))
		{
			$nb_articles = self::getArticlesByTag(null, true, $id_tag);
			$articles = self::getArticlesByTag($context->language->id, null, $id_tag, ($p - 1) * $n, $n);
		}

		$nb_pages = ceil($nb_articles / (int)BlogConf::getConf('LIMIT_ARTICLES'));
		$range = 3;

		$start = ($p - $range);
		if ($start < 1)
			$start = 1;

		$stop = ($p + $range);

		if ($stop > $nb_pages)
			$stop = (int)$nb_pages;

		return array(
			'p' => $p,
			'n' => $n,
			'nb_articles' => $nb_articles,
			'nb_pages' => $nb_pages,
			'range' => $range,
			'start' => $start,
			'stop' => $stop,
			'articles' => $articles
		);
	}

	public static function getProductsFull($id_article, $id_lang)
	{
		$query = new DbQuery();
		$query->select('p.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name, MAX(image_shop.`id_image`) id_image');
		$query->from(_BLOG_PREFIX_.'article_product', 'ap');
		$query->leftJoin(BlogDB::getTable('product'), 'p', 'ap.`id_product` = p.`id_product`');
		$query->leftJoin(BlogDB::getTable('image'), 'i', '(i.`id_product` = p.`id_product`)');
		$query->join(Shop::addSqlAssociation('product', 'p'));
		$query->join(Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1'));
		$query->leftJoin(BlogDB::getTableLang('image'), 'il', '(image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')');
		$query->leftJoin(BlogDB::getTableLang('product'), 'pl', '(p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')');
		$query->leftJoin(BlogDB::getTable('manufacturer'), 'm', '(m.`id_manufacturer` = p.`id_manufacturer`)');
		$query->leftJoin(BlogDB::getTable('supplier'), 's', '(s.`id_supplier` = p.`id_supplier`)');
		$query->where('pl.`id_lang` = '.(int)$id_lang.' AND `'.self::getIdTable().'` = '.(int)$id_article);
		$query->groupBy('ap.`id_product`');

		$products = Db::getInstance()->executeS($query->build());
		if (!count($products))
			return array();
		return Product::getProductsProperties($id_lang, $products);
	}

	public static function getInstanceByLinkRewrite($link_rewrite)
	{
		$context = Context::getContext();
		if (!Validate::isLinkRewrite($link_rewrite))
			return new self();
		$id_article = Db::getInstance()->getValue('SELECT '.self::getIdTable()
			.' FROM '.self::getPrefixTableLang().' WHERE link_rewrite = "'.pSQL($link_rewrite).'"');
		if (!$id_article)
			return new self();
		return new self($id_article, $context->language->id);
	}

	public function deleteTags()
	{
		return Db::getInstance()->delete(_BLOG_PREFIX_.'article_tag', self::getIdTable().' = '.(int)$this->id);
	}

	public function addTag($id_tag)
	{
		Db::getInstance()->insert(_BLOG_PREFIX_.'article_tag', array(
			array(
				self::getIdTable() => $this->id,
				BlogTag::getIdTable() => $id_tag
			)
		));
	}
} 