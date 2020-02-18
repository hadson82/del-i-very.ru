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

require_once(dirname(__FILE__).'/classes/tools/config.php');
class SEOMetaTags extends ModuleSMT
{
	public function __construct()
	{
		$this->name = 'seometatags';
		$this->tab = 'front_office_features';
		$this->documentation = false;
		$this->version = '1.3.1';
		$this->author = 'DaRiuS';
		$this->need_instance = '0';
		$this->bootstrap = true;
		$this->module_key = 'bbf8e50b437ec59c806a9a4531b9db76';
		if (defined('_PS_ADMIN_DIR_') && Tools::isSubmit('ajax'))
			ToolsModuleSMT::createAjaxApiCall($this);
		parent::__construct();
		$this->displayName = $this->l('SEO Meta Tags');
		$this->description = $this->l('Generate meta data by rules');

		$this->hooks = array(
			'actionSeoMetaTags'
		);

		$this->config = array(
			'categories' => '',
			'enable_product_meta' => false,
			'product_meta_title' => array(),
			'product_meta_description' => array(),
			'product_meta_keywords' => array(),

			'enable_category_meta' => false,
			'category_meta_title' => array(),
			'category_meta_description' => array(),
			'category_meta_keywords' => array(),
		);

		foreach (ToolsModuleSMT::getLanguages(false) as $l)
		{
			$this->config['product_meta_title'][$l['id_lang']] = '{product_name}  - {category_name}';
			$this->config['product_meta_description'][$l['id_lang']] = '{product_description}';
			$this->config['product_meta_keywords'][$l['id_lang']] = '{manufacturer_name},{supplier_name}';

			$this->config['category_meta_title'][$l['id_lang']] = '{category_name}';
			$this->config['category_meta_description'][$l['id_lang']] = '{category_description}';
			$this->config['category_meta_keywords'][$l['id_lang']] = '{category_name}';
		}
	}

	public function postProcess()
	{
		if (Tools::isSubmit('saveSettingForProduct'))
		{
			ConfSMT::setConf('enable_product_meta', Tools::getValue(ConfSMT::formatConfName('enable_product_meta')));
			$meta_title = array();
			$meta_description = array();
			$meta_keywords = array();
			foreach (ToolsModuleSMT::getLanguages(false) as $l)
			{
				$meta_title[$l['id_lang']] = Tools::getValue(ConfSMT::formatConfName('product_meta_title').'_'.$l['id_lang']);
				$meta_description[$l['id_lang']] = Tools::getValue(ConfSMT::formatConfName('product_meta_description').'_'.$l['id_lang']);
				$meta_keywords[$l['id_lang']] = Tools::getValue(ConfSMT::formatConfName('product_meta_keywords').'_'.$l['id_lang']);
			}
			ConfSMT::setConf('product_meta_title', $meta_title);
			ConfSMT::setConf('product_meta_description', $meta_description);
			ConfSMT::setConf('product_meta_keywords', $meta_keywords);
			ConfSMT::setConf('categories', implode(',', Tools::getValue(ConfSMT::formatConfName('categories'))));
			Tools::redirectAdmin(ToolsModuleSMT::getModuleTabAdminLink());
		}

		if (Tools::isSubmit('saveSettingForCategory'))
		{
			ConfSMT::setConf('enable_category_meta', Tools::getValue(ConfSMT::formatConfName('enable_category_meta')));
			$meta_title = array();
			$meta_description = array();
			$meta_keywords = array();
			foreach (ToolsModuleSMT::getLanguages(false) as $l)
			{
				$meta_title[$l['id_lang']] = Tools::getValue(ConfSMT::formatConfName('category_meta_title').'_'.$l['id_lang']);
				$meta_description[$l['id_lang']] = Tools::getValue(ConfSMT::formatConfName('category_meta_description').'_'.$l['id_lang']);
				$meta_keywords[$l['id_lang']] = Tools::getValue(ConfSMT::formatConfName('category_meta_keywords').'_'.$l['id_lang']);
			}
			ConfSMT::setConf('category_meta_title', $meta_title);
			ConfSMT::setConf('category_meta_description', $meta_description);
			ConfSMT::setConf('category_meta_keywords', $meta_keywords);
			Tools::redirectAdmin(ToolsModuleSMT::getModuleTabAdminLink());
		}
	}

	public function getContent()
	{
		$this->postProcess();
		ToolsModuleSMT::registerSmartyFunctions();
		$this->context->controller->addCSS($this->_path.'views/css/admin.css');
		$this->context->controller->addJS($this->_path.'views/js/admin.js');

		$html = $this->renderProductMetaSettings();
		$html .= $this->renderCategoryMetaSettings();
		return $html;
	}

	public function renderProductMetaSettings()
	{
		$root_category = Category::getRootCategory();
		if (!$root_category->id)
		{
			$root_category->id = 0;
			$root_category->name = $this->l('Root');
		}

		$root_category = array('id_category' => (int)$root_category->id, 'name' => $root_category->name);

		$data_category = array(
			'Root' => $root_category,
			'selected' => $this->l('Selected'),
			'Check all' => $this->l('Check all'),
			'Check All' => $this->l('Check All'),
			'Uncheck All'  => $this->l('Uncheck All'),
			'Collapse All' => $this->l('Collapse All'),
			'Expand All' => $this->l('Expand All'),
			'search' => $this->l('Search a category')
		);

		$fields = array(
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Setting SEO generator for products')
					),
					'input' => array(
						array(
							'label' => '',
							'type' => 'html_smt',
							'name' => 'html',
							'html_content' => '<input form="generate_form" type="submit" value="'
								.$this->l('Run generate Meta Tags').'" name="generate_meta_tags" class="btn btn-default button">'
						),
						array(
							'label' => $this->l('Override default meta tags?'),
							'name' => ConfSMT::formatConfName('enable_product_meta'),
							'type' => 'switch',
							'class' => 't',
							'values' => array(
								array(
									'id' => ConfSMT::formatConfName('enable_product_meta').'_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => ConfSMT::formatConfName('enable_product_meta').'_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'desc' => $this->l('Warning! The base class Prestashop "Meta"
							was overloaded when you install the module, if you enable this option, the meta tags will be
							generated on the fly on the rules by our functions')
						),
						array(
							'label' => $this->l('Meta title'),
							'name' => ConfSMT::formatConfName('product_meta_title'),
							'type' => 'textarea',
							'lang' => true,
							'meta_vars' => ProductMetaGenerator::getInstance()->getMetaVarsProduct()
						),
						array(
							'label' => $this->l('Meta description'),
							'name' => ConfSMT::formatConfName('product_meta_description'),
							'type' => 'textarea',
							'lang' => true,
							'meta_vars' => ProductMetaGenerator::getInstance()->getMetaVarsProduct()
						),
						array(
							'label' => $this->l('Meta keywords'),
							'name' => ConfSMT::formatConfName('product_meta_keywords'),
							'type' => 'textarea',
							'lang' => true,
							'meta_vars' => ProductMetaGenerator::getInstance()->getMetaVarsProduct()
						),
						array(
							'label' => $this->l('Select categories where will be use generate meta tags'),
							'type' => 'categories',
							'name' => ConfSMT::formatConfName('categories'),
							'tree' => array(
								'id' => ConfSMT::formatConfName('categories'),
								'use_checkbox' => true,
								'selected_categories' => ProductMetaGenerator::getInstance()->getCategories(),
								'full_tree' => true
							),
							'values' => array(
								'trads' => $data_category,
								'input_name' => ConfSMT::formatConfName('categories').'[]',
								'use_radio' => false,
								'use_search' => false,
								'selected_cat' => ProductMetaGenerator::getInstance()->getCategories(),
								'disabled_categories' => array(4),
								'top_category' => Category::getTopCategory($this->context->language->id),
								'use_context' => true
							)
						)
					),
					'submit' => array(
						'title' => $this->l('Save')
					)
				)
			)
		);

		$meta_title = array();
		$meta_description = array();
		$meta_keywords = array();
		foreach (ToolsModuleSMT::getLanguages(false) as $l)
		{
			$meta_title[$l['id_lang']] = ConfSMT::getConf('product_meta_title', $l['id_lang']);
			$meta_description[$l['id_lang']] = ConfSMT::getConf('product_meta_description', $l['id_lang']);
			$meta_keywords[$l['id_lang']] = ConfSMT::getConf('product_meta_keywords', $l['id_lang']);
		}
		$helper_form = new HelperForm();
		$helper_form->fields_value[ConfSMT::formatConfName('enable_product_meta')] = ConfSMT::getConf('enable_product_meta');
		$helper_form->fields_value[ConfSMT::formatConfName('product_meta_title')] = $meta_title;
		$helper_form->fields_value[ConfSMT::formatConfName('product_meta_description')] = $meta_description;
		$helper_form->fields_value[ConfSMT::formatConfName('product_meta_keywords')] = $meta_keywords;

		$helper_form->submit_action = 'saveSettingForProduct';
		$helper_form->override_folder = 'seo_generator_for_product/';
		$helper_form->module = $this;
		$helper_form->table = 'product';
		$helper_form->languages = ToolsModuleSMT::getLanguages(false);
		$helper_form->allow_employee_form_lang = 0;
		$helper_form->default_form_language = Configuration::get('PS_LANG_DEFAULT');
		$helper_form->token = Tools::getValue('token');
		$helper_form->currentIndex = $_SERVER['REQUEST_URI'];
		$helper_form->show_toolbar = true;
		$helper_form->toolbar_scroll = false;

		$error = '';
		if (Tools::isSubmit('error'))
		{
			$error .= '<div class="alert alert-danger error">';
			if (Tools::getValue('error') == 1)
				$error .= $this->l('Has error');
			else
				Tools::redirectAdmin(ToolsModuleSMT::getModuleTabAdminLink());
			$error .= '</div><br>';
		}

		return $error.'<form id="generate_form" action="'.$_SERVER['REQUEST_URI'].'" method="POST"></form>'.$helper_form->generateForm($fields);
	}

	public function renderCategoryMetaSettings()
	{
		$fields = array(
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Setting SEO generator for categories')
					),
					'input' => array(
						array(
							'label' => '',
							'type' => 'html_smt',
							'name' => 'html',
							'html_content' => '<input form="generate_form_category" type="submit" value="'
								.$this->l('Run generate Meta Tags').'" name="category_generate_meta_tags" class="btn btn-default button">'
						),
						array(
							'label' => $this->l('Override default meta tags?'),
							'name' => ConfSMT::formatConfName('enable_category_meta'),
							'type' => 'switch',
							'class' => 't',
							'values' => array(
								array(
									'id' => ConfSMT::formatConfName('enable_category_meta').'_on',
									'value' => 1,
									'label' => $this->l('Yes')
								),
								array(
									'id' => ConfSMT::formatConfName('enable_category_meta').'_off',
									'value' => 0,
									'label' => $this->l('No')
								)
							),
							'desc' => $this->l('Warning! The base class Prestashop "Meta"
							was overloaded when you install the module, if you enable this option, the meta tags will be
							generated on the fly on the rules by our functions')
						),
						array(
							'label' => $this->l('Meta title'),
							'name' => ConfSMT::formatConfName('category_meta_title'),
							'type' => 'textarea',
							'lang' => true,
							'meta_vars' => CategoryMetaGenerator::getInstance()->getMetaVarsCategory()
						),
						array(
							'label' => $this->l('Meta description'),
							'name' => ConfSMT::formatConfName('category_meta_description'),
							'type' => 'textarea',
							'lang' => true,
							'meta_vars' => CategoryMetaGenerator::getInstance()->getMetaVarsCategory()
						),
						array(
							'label' => $this->l('Meta keywords'),
							'name' => ConfSMT::formatConfName('category_meta_keywords'),
							'type' => 'textarea',
							'lang' => true,
							'meta_vars' => CategoryMetaGenerator::getInstance()->getMetaVarsCategory()
						)
					),
					'submit' => array(
						'title' => $this->l('Save')
					)
				)
			)
		);

		$meta_title = array();
		$meta_description = array();
		$meta_keywords = array();
		foreach (ToolsModuleSMT::getLanguages(false) as $l)
		{
			$meta_title[$l['id_lang']] = ConfSMT::getConf('category_meta_title', $l['id_lang']);
			$meta_description[$l['id_lang']] = ConfSMT::getConf('category_meta_description', $l['id_lang']);
			$meta_keywords[$l['id_lang']] = ConfSMT::getConf('category_meta_keywords', $l['id_lang']);
		}
		$helper_form = new HelperForm();
		$helper_form->fields_value[ConfSMT::formatConfName('enable_category_meta')] = ConfSMT::getConf('enable_category_meta');
		$helper_form->fields_value[ConfSMT::formatConfName('category_meta_title')] = $meta_title;
		$helper_form->fields_value[ConfSMT::formatConfName('category_meta_description')] = $meta_description;
		$helper_form->fields_value[ConfSMT::formatConfName('category_meta_keywords')] = $meta_keywords;

		$helper_form->submit_action = 'saveSettingForCategory';
		$helper_form->override_folder = 'seo_generator_for_category/';
		$helper_form->module = $this;
		$helper_form->table = 'category';
		$helper_form->languages = ToolsModuleSMT::getLanguages(false);
		$helper_form->allow_employee_form_lang = 0;
		$helper_form->default_form_language = Configuration::get('PS_LANG_DEFAULT');
		$helper_form->token = Tools::getValue('token');
		$helper_form->currentIndex = $_SERVER['REQUEST_URI'];
		$helper_form->show_toolbar = true;
		$helper_form->toolbar_scroll = false;

		$error = '';
		if (Tools::isSubmit('error'))
		{
			$error .= '<div class="alert alert-danger error">';
			if (Tools::getValue('error') == 1)
				$error .= $this->l('Has error');
			else
				Tools::redirectAdmin(ToolsModuleSMT::getModuleTabAdminLink());
			$error .= '</div><br>';
		}

		return $error.'<form id="generate_form_category" action="'.$_SERVER['REQUEST_URI'].'" method="POST"></form>'.$helper_form->generateForm($fields);
	}

	public function ajaxProcessGenerateProductMetaTags()
	{
		$id_product = (int)Tools::getValue('id_product');
		$result = ProductMetaGenerator::getInstance()->generateMetaTagsAndSave($id_product);
		die(Tools::jsonEncode(array(
			'hasError' => (int)$result
		)));
	}

	public function ajaxProcessGetIdsProduct()
	{
		$ids_product = ProductMetaGenerator::getInstance()->getProductIds();
		die(Tools::jsonEncode($ids_product));
	}

	public function ajaxProcessGenerateCategoryMetaTags()
	{
		$id_category = (int)Tools::getValue('id_category');
		$result = CategoryMetaGenerator::getInstance()->generateMetaTagsAndSave($id_category);
		die(Tools::jsonEncode(array(
			'hasError' => (int)$result
		)));
	}

	public function ajaxProcessGetIdsCategory()
	{
		$ids_category = CategoryMetaGenerator::getInstance()->getCategoryIds();
		die(Tools::jsonEncode($ids_category));
	}

	public function hookActionSeoMetaTags($params)
	{
		switch ($params['type'])
		{
			case 'product':
				$available_categories = ProductMetaGenerator::getInstance()->getCategories();
				$product_categories = Product::getProductCategories($params['id_product']);
				$allow_override = false;
				foreach ($product_categories as $cat)
					if (in_array($cat, $available_categories))
						$allow_override = true;
				if (!ConfSMT::getConf('enable_product_meta') || !$allow_override)
					return false;

				$params['meta_title'] = ProductMetaGenerator::generateMetaTitle($params['id_product']);
				$params['meta_description'] = ProductMetaGenerator::generateMetaDescription($params['id_product']);
				$params['meta_keywords'] = ProductMetaGenerator::generateMetaKeywords($params['id_product']);
				break;
			case 'category':
				if (!ConfSMT::getConf('enable_category_meta'))
					return false;
				$params['meta_title'] = CategoryMetaGenerator::generateMetaTitle($params['id_category']);
				$params['meta_description'] = CategoryMetaGenerator::generateMetaDescription($params['id_category']);
				$params['meta_keywords'] = CategoryMetaGenerator::generateMetaKeywords($params['id_category']);
				break;
		}
	}
} 