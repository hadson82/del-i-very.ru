<?php

class CategoryMetaGenerator
{
	public static $meta_vars = array();

	protected function __construct()
	{
	}

	/**
	 * @var CategoryMetaGenerator
	 */
	protected static $instance = null;
	public static function getInstance()
	{
		if (is_null(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

	public function generateMetaTagsAndSave($id_category)
	{
		$category = new Category($id_category);
		if (Validate::isLoadedObject($category))
		{
			$category->meta_title = self::generateMetaTitle($id_category, true);
			$category->meta_description = self::generateMetaDescription($id_category, true);
			$category->meta_keywords = self::generateMetaKeywords($id_category, true);
			return $category->save();
		}
		return false;
	}

	public static function generateMetaTitle($id_category, $multi_lang = false)
	{
		return self::generateMeta(self::TYPE_META_TITLE, $id_category, 'category_meta_title', $multi_lang);
	}

	public static function generateMetaDescription($id_category, $multi_lang = false)
	{
		return self::generateMeta(self::TYPE_META_DESCRIPTION, $id_category, 'category_meta_description', $multi_lang);
	}

	public static function generateMetaKeywords($id_category, $multi_lang = false)
	{
		return self::generateMeta(self::TYPE_META_KEYWORDS, $id_category, 'category_meta_keywords', $multi_lang);
	}

	const TYPE_META_TITLE = 'meta_title';
	const TYPE_META_DESCRIPTION = 'meta_description';
	const TYPE_META_KEYWORDS = 'meta_keywords';

	public static function generateMeta($type, $id_category, $conf_name, $multi_lang = false)
	{
		$context = Context::getContext();
		$meta_vars = self::getMetaVarsCategory($id_category, $multi_lang);
		if (!$multi_lang)
		{
			$meta = str_replace(array_keys($meta_vars), $meta_vars, ConfSMT::getConf($conf_name, $context->language->id));
			return self::truncateByTypeMeta($type, $meta);
		}
		else
		{
			$meta_l = array();
			foreach (ToolsModuleSMT::getLanguages(false) as $l)
			{
				$meta = str_replace(array_keys($meta_vars[$l['id_lang']]),
					$meta_vars[$l['id_lang']], ConfSMT::getConf($conf_name, $l['id_lang']));
				$meta_l[$l['id_lang']] = self::truncateByTypeMeta($type, $meta);
			}
			return $meta_l;
		}
	}

	public static function truncateByTypeMeta($type, $string)
	{
		$length = Category::$definition['fields'][$type]['size'];
		return Tools::substr($string, 0, $length);
	}

	public function getMetaVarsCategory($id_category = null, $multi_lang = false)
	{
		if (is_null($id_category))
			return array(
				'{category_name}',
				'{category_description}',
			);
		if (isset(self::$meta_vars['category_'.(int)$id_category]))
			return self::$meta_vars['category_'.(int)$id_category];
		$context = Context::getContext();
		$id_lang = ($multi_lang ? null : $context->language->id);

		$category = new Category($id_category, $id_lang);
		if (!$multi_lang)
		{
			$vars = array(
				'{category_name}' => $category->name,
				'{category_description}' => strip_tags(Tools::stripslashes($category->description))
			);
		}
		else
		{
			$vars = array();
			foreach (ToolsModuleSMT::getLanguages(false) as $l)
			{
				$vars[$l['id_lang']] = array(
					'{category_name}' => $category->name[$l['id_lang']],
					'{category_description}' => strip_tags(Tools::stripslashes($category->description[$l['id_lang']]))
				);
			}
		}
		self::$meta_vars['category_'.(int)$id_category] = $vars;
		return self::$meta_vars['category_'.(int)$id_category];
	}

	public function getCategoryIds()
	{
		$categories = Category::getSimpleCategories(Context::getContext()->language->id);
		$ids_categories = array();
		foreach ($categories as $product)
			$ids_categories[] = $product['id_category'];
		return $ids_categories;
	}
}