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

class BlogCommentArticle extends ObjectBlog
{
	public $message;
	public $id_customer;
	public $answer_id_customer;
	public $date_add;
	public $id_blog_article;
	public $parent_id = 0;
	public $is_moderated;
	public $is_active = 1;
	public $deleted;

	public static $definition = array(
		'table' => 'blog_comment_article',
		'primary' => 'id_blog_comment_article',
		'fields' => array(
			'message' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'answer_id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'parent_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_blog_article' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'is_moderated' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'is_active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	public function add($autodate = true, $null_values = false)
	{
		$this->date_add = ($this->date_add ? $this->date_add : date('Y-m-d H:i:s'));
		return parent::add($autodate, $null_values);
	}

	public static function getComments($id_article, $is_moderated = false,
									$is_active = true, $start = 0, $limit = null,
									$id_last_comment = null, $parent_id = null, $get_count = null)
	{
		if (is_null($limit))
			$limit = (BlogConf::getConf('LIMIT_COMMENTS') ? (int)BlogConf::getConf('LIMIT_COMMENTS') : 10);

		$context = Context::getContext();

		$query = new DbQuery();
		$query->from(self::getTable(), 'ca');
		$query->where('ca.`'.BlogArticle::getIdTable().'` = '.(int)$id_article.' AND ca.`deleted` = 0 ');

		if ($is_moderated && !$context->cookie->logged)
			$query->where('ca.`is_moderated` = 1 ');
		if ($is_moderated && $context->cookie->logged)
			$query->where('(ca.`is_moderated` = 1 OR ca.`id_customer` = '.(int)$context->cookie->id_customer.') ');
		if ($is_active)
			$query->where('ca.`is_active` = 1 ');
		if (!is_null($id_last_comment))
			$query->where('ca.`'.self::getIdTable().'` > '.(int)$id_last_comment);

		$query->where('ca.`parent_id` = '.(is_null($parent_id) ? 0 : (int)$parent_id));

		if (!is_null($get_count))
		{
			$query->select('COUNT(ca.`'.self::getIdTable().'`)');
			$result = Db::getInstance()->getValue($query->build());
			return $result;
		}

		$query->leftJoin(BlogDB::getTable('customer'), 'c', 'c.`id_customer` = ca.`id_customer`');
		$query->leftJoin(BlogDB::getTable('customer'), 'answ_c', 'answ_c.`id_customer` = ca.`answer_id_customer`');
		$query->select('ca.*, c.`firstname`, c.`lastname`');
		$query->select('answ_c.`firstname` as answer_firstname, answ_c.`lastname` as answer_lastname');
		$query->orderBy('ca.`'.self::getIdTable().'` DESC');
		if ($limit != 0)
			$query->limit($limit, $start);

		$result = Db::getInstance()->executeS($query->build());
		$comments = array();

		if (!count($result))
			return array();

		foreach ($result as $item)
		{
			$comments[$item[self::getIdTable()]] = $item;
			if ($item['parent_id'] != 0)
				continue;
			if (!$parent_id)
			{
				$children = self::getComments($id_article, $is_moderated, $is_active, 0, $limit, $id_last_comment, $item[self::getIdTable()]);
				$children = array_reverse($children);
				$comments[$item[self::getIdTable()]]['children'] = $children;
				$comments[$item[self::getIdTable()]]['nb_children'] = self::getComments($id_article, $is_moderated,
					$is_active, 0, 0, $id_last_comment, $item[self::getIdTable()], true);
			}
			else
			{
				$comments[$item[self::getIdTable()]]['children'] = array();
				$comments[$item[self::getIdTable()]]['nb_children'] = 0;
			}
		}
		return $comments;
	}

	public static function getCustomersSelect2FormatByIds($ids_customer)
	{
		if (!is_array($ids_customer) || !count($ids_customer))
			return array();

		if (count($ids_customer))
			return Db::getInstance()->executeS('SELECT id_customer as id,
 				CONCAT("№", id_customer, " ", firstname, " ", lastname) as text
			FROM '.BlogDB::getPrefixTable('customer').'
			WHERE id_customer IN('.implode(',', array_map('intval', $ids_customer)).')');
	}

	public static function smartyGetPathAvatarByCustomer($params)
	{
		$default_avatar = BlogTools::getModNameForPath(__FILE__).'/views/img/man.jpg';

		if (array_key_exists('id_customer', $params))
		{
			$type = 'customer';
			$id = (int)$params['id_customer'];
		}
		elseif (array_key_exists('id_guest', $params))
		{
			$type = 'guest';
			$id = (int)$params['id_guest'];
		}
		else
			return $default_avatar;

		$path_avatar = BlogTools::getModNameForPath(__FILE__).'/views/img/avatar/'.$type.'_'.$id.'.jpg';
		if (file_exists(_PS_MODULE_DIR_.$path_avatar))
			return _MODULE_DIR_.$path_avatar;
		return _MODULE_DIR_.$default_avatar;
	}

	public static function smartyLinkWrapper($value)
	{
		$wrapper = '<a target="_blank" href="\\0">\\0</a>';
		if (!Context::getContext()->cookie->logged)
			$wrapper = '<span class="link_not_available">['.BlogTranslate::getInstance()->l('link available only registered', __FILE__).']</span>';
		return preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#", $wrapper, $value);
	}

	public static function getAvatarPath()
	{
		return _PS_MODULE_DIR_.BlogTools::getModNameForPath(__FILE__).'/views/img/avatar/';
	}

	public static function getLocalAvatarPath()
	{
		return _MODULE_DIR_.BlogTools::getModNameForPath(__FILE__).'/views/img/avatar/';
	}

	public static function getGuestAvatar()
	{
		return 'guest_'.Context::getContext()->cookie->id_guest.'.jpg';
	}

	public static function getCustomerAvatar($id = null)
	{
		return 'customer_'.($id ? (int)$id : Context::getContext()->customer->id).'.jpg';
	}

	public static function setModeratedByArticle($id_blog_article)
	{
		Db::getInstance()->execute('UPDATE '.self::getPrefixTable()
			.' SET `is_moderated` = 1 WHERE `'.BlogArticle::getIdTable().'` = '.(int)$id_blog_article);
	}

	public static function getTotalCommentsByArticle($id_blog_article)
	{
		return (int)Db::getInstance()->getValue('SELECT COUNT(ca.`'.BlogCommentArticle::getIdTable().'`)
				FROM '.BlogCommentArticle::getPrefixTable().' ca
				WHERE ca.`'.BlogArticle::getIdTable().'` = '.(int)$id_blog_article.'
				AND ca.`is_moderated` = 1
				AND ca.`is_active` = 1
				AND ca.`deleted` = 0');
	}
}