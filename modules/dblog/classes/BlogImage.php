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

class BlogImage extends ObjectBlog
{
	public $id_blog_article;
	public $cover;

	const ARTICLE_IMG_PATH = '/views/img/a/';

	protected $source_index;
	protected static $access_rights = 0775;

	public static $definition = array(
		'table' => 'blog_image',
		'primary' => 'id_blog_image',
		'fields' => array(
			'id_blog_article' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'cover' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool')
		)
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		$this->source_index = _PS_PROD_IMG_DIR_.'index.php';
		parent::__construct($id, $id_lang, $id_shop);
	}


	public static function getImageBasePathArticle($id_image)
	{
		$string = (string)$id_image;
		$path = _PS_MODULE_DIR_._MODULE_NAME_.self::ARTICLE_IMG_PATH;
		$str_len = Tools::strlen($string);
		for ($i = 0; $i < $str_len; $i++)
			$path .= Tools::substr($string, $i, 1).'/';
		return $path;
	}

	public static function getImgPath($id_blog_image, $type = null)
	{
		return self::getImagePathArticle($id_blog_image).$id_blog_image.($type ? '_'.$type : '').'.jpg';
	}

	public static function getImagePathArticle($id_image)
	{
		$string = (string)$id_image;
		$path = _MODULE_DIR_._MODULE_NAME_.self::ARTICLE_IMG_PATH;
		$str_len = Tools::strlen($string);
		for ($i = 0; $i < $str_len; $i++)
			$path .= Tools::substr($string, $i, 1).'/';

		return $path;
	}

	public function createImgFolder()
	{
		if (!$this->id)
			return false;

		if (!file_exists($this->getImgFolder()))
		{
			// Apparently sometimes mkdir cannot set the rights, and sometimes chmod can't. Trying both.
			$success = @mkdir($this->getImgFolder(), self::$access_rights, true);
			$chmod = @chmod($this->getImgFolder(), self::$access_rights);

			// Create an index.php file in the new folder
			if (($success || $chmod)
				&& !file_exists($this->getImgFolder().'index.php')
				&& file_exists($this->source_index))
				return @copy($this->source_index, $this->getImgFolder().'index.php');
		}
		return true;
	}

	public function getImgFolder()
	{
		return $this->getImageBasePathArticle($this->id);
	}

	public static function getImagesByArticle($id_article, $order_by = null, $way = null)
	{
		return Db::getInstance()->executeS('SELECT * FROM '.self::getPrefixTable()
			.' WHERE '.BlogArticle::getIdTable().' = '.(int)$id_article.' AND cover = 0'.
			(!is_null($order_by) ? ' ORDER BY '.pSQL($order_by).' '.(!is_null($way) ? pSQL($way) : 'ASC')  : ''));

	}

	public static function getImages()
	{
		return Db::getInstance()->executeS('SELECT * FROM '.self::getPrefixTable());
	}

	public function delete()
	{
		$types = BlogImageType::getTypes();
		$file = $this->getImgFolder().$this->id.'.jpg';
		if (file_exists($file))
			unlink($file);
		foreach ($types as $type)
		{
			$file_type = $this->getImgFolder().$this->id.'_'.$type['name'].'.jpg';
			if (file_exists($file_type))
				unlink($file_type);
		}
		return parent::delete();
	}
}