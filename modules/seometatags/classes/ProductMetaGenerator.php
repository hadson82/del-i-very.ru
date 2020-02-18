<?php

class ProductMetaGenerator
{
	public static $meta_vars = array();

	protected function __construct()
	{
	}

	/**
	 * @var ProductMetaGenerator
	 */
	protected static $instance = null;
	public static function getInstance()
	{
		if (is_null(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

	public function generateMetaTagsAndSave($id_product)
	{
		$product = new Product($id_product);
		if (Validate::isLoadedObject($product))
		{
			$product->meta_title = self::generateMetaTitle($id_product, true);
			$product->meta_description = self::generateMetaDescription($id_product, true);
			$product->meta_keywords = self::generateMetaKeywords($id_product, true);
			return $product->save();
		}
		return false;
	}

	public static function generateMetaTitle($id_product, $multi_lang = false)
	{
		return self::generateMeta(self::TYPE_META_TITLE, $id_product, 'product_meta_title', $multi_lang);
	}

	public static function generateMetaDescription($id_product, $multi_lang = false)
	{
		return self::generateMeta(self::TYPE_META_DESCRIPTION, $id_product, 'product_meta_description', $multi_lang);
	}

	public static function generateMetaKeywords($id_product, $multi_lang = false)
	{
		return self::generateMeta(self::TYPE_META_KEYWORDS, $id_product, 'product_meta_keywords', $multi_lang);
	}

	const TYPE_META_TITLE = 'meta_title';
	const TYPE_META_DESCRIPTION = 'meta_description';
	const TYPE_META_KEYWORDS = 'meta_keywords';

	public static function generateMeta($type, $id_product, $conf_name, $multi_lang = false)
	{
		$context = Context::getContext();
		$meta_vars = self::getMetaVarsProduct($id_product, $multi_lang);
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
		$length = Product::$definition['fields'][$type]['size'];
		return Tools::substr($string, 0, $length);
	}

	public function getMetaVarsProduct($id_product = null, $multi_lang = false)
	{
		if (is_null($id_product))
			return array(
				'{product_name}',
				'{product_reference}',
				'{product_description}',
				'{category_name}',
				'{category_title}',
				'{category_description}',
				'{manufacturer_name}',
				'{manufacturer_title}',
				'{supplier_name}',
				'{supplier_title}',
				'{tags}',
				'{id_product}',
				'{price}'
			);
		if (isset(self::$meta_vars['product_'.(int)$id_product]))
			return self::$meta_vars['product_'.(int)$id_product];
		$context = Context::getContext();
		$id_lang = ($multi_lang ? null : $context->language->id);

		$product = new Product($id_product, true, $id_lang);
		$category = new Category($product->id_category_default, $id_lang);
		$manufacturer = new Manufacturer($product->id_manufacturer, $id_lang);
		$supplier = new Supplier($product->id_supplier, $id_lang);
		if (!$multi_lang)
		{
			$vars = array(
				'{product_name}' => $product->name,
				'{product_reference}' => $product->reference,
				'{product_description}' => strip_tags(Tools::stripslashes($product->description_short)),
				'{category_name}' => $category->name,
				'{category_title}' => $category->meta_title,
				'{category_description}' => strip_tags(Tools::stripslashes($category->description)),
				'{manufacturer_name}' => $manufacturer->name,
				'{manufacturer_title}' => $manufacturer->meta_title,
				'{supplier_name}' => $supplier->name,
				'{supplier_title}' => $supplier->meta_title,
				'{tags}' => $product->getTags($context->language->id),
				'{id_product}' => $product->id,
				'{price}' => Tools::displayPrice($product->getPrice(), (int)Configuration::get('PS_CURRENCY_DEFAULT'))
			);
		}
		else
		{
			$vars = array();
			foreach (ToolsModuleSMT::getLanguages(false) as $l)
			{
				$vars[$l['id_lang']] = array(
					'{product_name}' => $product->name[$l['id_lang']],
					'{product_reference}' => $product->reference,
					'{product_description}' => strip_tags(Tools::stripslashes($product->description_short[$l['id_lang']])),
					'{category_name}' => $category->name[$l['id_lang']],
					'{category_title}' => $category->meta_title[$l['id_lang']],
					'{category_description}' => strip_tags(Tools::stripslashes($category->description[$l['id_lang']])),
					'{manufacturer_name}' => $manufacturer->name,
					'{manufacturer_title}' => $manufacturer->meta_title[$l['id_lang']],
					'{supplier_name}' => $supplier->name,
					'{supplier_title}' => $supplier->meta_title[$l['id_lang']],
					'{tags}' => $product->getTags($l['id_lang']),
					'{id_product}' => $product->id,
					'{price}' => Tools::displayPrice($product->getPrice(), (int)Configuration::get('PS_CURRENCY_DEFAULT'))
				);
			}
		}
		self::$meta_vars['product_'.(int)$id_product] = $vars;
		return self::$meta_vars['product_'.(int)$id_product];
	}

	public function getProductIds()
	{
		$available_categories = $this->getCategories();
		$products = Db::getInstance()->executeS('SELECT p.`id_product` FROM '._DB_PREFIX_.'product p
		WHERE (SELECT COUNT(cp.`id_category`) FROM '._DB_PREFIX_.'category_product cp
			WHERE p.`id_product` = cp.`id_product` AND cp.`id_category`
			IN('.(is_array($available_categories) && count($available_categories) ? pSQL(implode(',', $available_categories)) : 'NULL').'))');
		$ids_product = array();
		foreach ($products as $product)
			$ids_product[] = $product['id_product'];
		return $ids_product;
	}

	public function getCategories()
	{
		return (ConfSMT::getConf('categories')
			? explode(',', ConfSMT::getConf('categories')) : array());
	}
}