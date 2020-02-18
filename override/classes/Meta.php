<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 Goryachev Dmitry
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class Meta extends MetaCore
{
	/**
	 * Get product meta tags
	 *
	 * @since 1.5.0
	 * @param int $id_product
	 * @param int $id_lang
	 * @param string $page_name
	 * @return array
	 */
	/*
    * module: seometatags
    * date: 2016-12-03 23:49:20
    * version: 1.3.1
    */
    public static function getProductMetas($id_product, $id_lang, $page_name)
	{
		$meta_tags = parent::getProductMetas($id_product, $id_lang, $page_name);
		Hook::exec('actionSeoMetaTags', array(
			'type' => 'product',
			'id_product' => $id_product,
			'meta_title' => &$meta_tags['meta_title'],
			'meta_description' => &$meta_tags['meta_description'],
			'meta_keywords' => &$meta_tags['meta_keywords']
		));
		return $meta_tags;
	}
	/*
    * module: seometatags
    * date: 2016-12-03 23:49:20
    * version: 1.3.1
    */
    public static function getCategoryMetas($id_category, $id_lang, $page_name, $title = '')
	{
		$meta_tags = parent::getCategoryMetas($id_category, $id_lang, $page_name, $title);
		Hook::exec('actionSeoMetaTags', array(
			'type' => 'caregory',
			'id_category' => $id_category,
			'meta_title' => &$meta_tags['meta_title'],
			'meta_description' => &$meta_tags['meta_description'],
			'meta_keywords' => &$meta_tags['meta_keywords']
		));
		return $meta_tags;
	}
}
