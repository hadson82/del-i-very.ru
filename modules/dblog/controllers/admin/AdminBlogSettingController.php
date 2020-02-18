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

require_once(dirname(__FILE__).'/../../config.php');

class AdminBlogSettingController extends ModuleAdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->bootstrap = true;
		$this->display = 'edit';
		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJS($this->module->getPathUri().'views/js/vendor/select2.min.js');
		$this->addCSS($this->module->getPathUri().'views/css/vendor/select2.css');
		$this->addCSS($this->module->getPathUri().'views/css/vendor/css/select2-bootstrap.css');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAddconfiguration'))
		{
			foreach (array_keys($this->module->config) as $config)
				BlogConf::setConf($config, Tools::getValue(BlogConf::formatConfName($config)));

			Tools::redirectAdmin($this->context->link->getAdminLink('AdminBlogSetting'));
		}

		return parent::postProcess();
	}

	public function getFieldsValue($obj)
	{
		$fields_value = parent::getFieldsValue($obj);
		if ($fields_value[BlogConf::formatConfName('CUSTOMERS_HAVE_ADMIN_RIGHTS')])
		{
			$fields_value[BlogConf::formatConfName('CUSTOMERS_HAVE_ADMIN_RIGHTS')] =
				BlogCommentArticle::getCustomersSelect2FormatByIds(explode('|', $fields_value[BlogConf::formatConfName('CUSTOMERS_HAVE_ADMIN_RIGHTS')]));
		}
		return $fields_value;
	}

	public function renderForm()
	{
		foreach (array_keys($this->module->config) as $config)
			$this->fields_value[BlogConf::formatConfName($config)] = BlogConf::getConf($config);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Settings blog')
			),
			'input' => array(
				array(
					'label' => $this->l('Select customers, which have admin rights'),
					'name' => BlogConf::formatConfName('CUSTOMERS_HAVE_ADMIN_RIGHTS'),
					'type' => 'select2customer'
				),
				array(
					'label' => $this->l('Limit articles in category'),
					'name' => BlogConf::formatConfName('LIMIT_ARTICLES'),
					'type' => 'text'
				),
				array(
					'label' => $this->l('Limit count comments in article'),
					'name' => BlogConf::formatConfName('LIMIT_COMMENTS'),
					'type' => 'text'
				),
				array(
					'label' => $this->l('Interval update comments'),
					'name' => BlogConf::formatConfName('INTERVAL_UPDATE'),
					'type' => 'text',
					'desc' => $this->l('value is in milliseconds, not less than 5000')
				),
				array(
					'label' => $this->l('Format date'),
					'name' => BlogConf::formatConfName('DATE_FORMAT'),
					'type' => 'radio',
					'values' => array(
						array(
							'id' => 'id_date_format_four',
							'value' => 'd F Y',
							'label' => $this->l('01 January 2014')
						),
						array(
							'id' => 'id_date_format_one',
							'value' => 'd.m.Y',
							'label' => $this->l('day.month.year')
						),
						array(
							'id' => 'id_date_format_two',
							'value' => 'd/m/Y',
							'label' => $this->l('day/month/year')
						),
						array(
							'id' => 'id_date_format_three',
							'value' => 'd M Y',
							'label' => $this->l('01 Jan 2014')
						)
					)
				),
				array(
					'label' => $this->l('Meta title'),
					'name' => BlogConf::formatConfName('META_TITLE'),
					'type' => 'text'
				),
				array(
					'label' => $this->l('Meta keywords'),
					'name' => BlogConf::formatConfName('META_KEYWORDS'),
					'type' => 'text'
				),
				array(
					'label' => $this->l('Meta description'),
					'name' => BlogConf::formatConfName('META_DESCRIPTION'),
					'type' => 'textarea'
				),
				array(
					'label' => $this->l('Root route name'),
					'name' => BlogConf::formatConfName('ROUTE_NAME'),
					'type' => 'text'
				),
				array(
					'label' => $this->l('Email'),
					'name' =>  BlogConf::formatConfName('EMAIL'),
					'type' => 'text'
				),
				array(
					'label' => $this->l('Show who create article?'),
					'name' => BlogConf::formatConfName('SHOW_WHO_CREATE_ARTICLE'),
					'type' => 'switch',
					'values' => array(
						array(
							'id' => 'SHOW_WHO_CREATE_ARTICLE_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'SHOW_WHO_CREATE_ARTICLE_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Show block categories in column?'),
					'name' => BlogConf::formatConfName('SHOW_BLOCK_CATEGORIES'),
					'type' => 'switch',
					'values' => array(
						array(
							'id' => 'SHOW_BLOCK_CATEGORIES_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'SHOW_BLOCK_CATEGORIES_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Show block tags in column?'),
					'name' => BlogConf::formatConfName('SHOW_BLOCK_TAGS'),
					'type' => 'switch',
					'values' => array(
						array(
							'id' => 'SHOW_BLOCK_TAGS_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'SHOW_BLOCK_TAGS_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Use smart crop image?'),
					'name' => BlogConf::formatConfName('USE_SMART_CROP_IMAGE'),
					'type' => 'switch',
					'values' => array(
						array(
							'id' => 'USE_SMART_CROP_IMAGE_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'USE_SMART_CROP_IMAGE_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Smart crop resize up?'),
					'name' => BlogConf::formatConfName('SMART_CROP_IMAGE_RESIZE_UP'),
					'type' => 'switch',
					'values' => array(
						array(
							'id' => 'SMART_CROP_IMAGE_RESIZE_UP_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'SMART_CROP_IMAGE_RESIZE_UP_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Share Vkontakte'),
					'name' => BlogConf::formatConfName('SHARE_VK'),
					'type' => 'switch',
					'values' => array(                                   // This is only useful if type == radio
						array(
							'id' => 'vk_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'vk_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Share Odnoklassniki'),
					'name' => BlogConf::formatConfName('SHARE_OD'),
					'type' => 'switch',
					'values' => array(                                   // This is only useful if type == radio
						array(
							'id' => 'od_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'od_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Share Twitter'),
					'name' => BlogConf::formatConfName('SHARE_TW'),
					'type' => 'switch',
					'values' => array(                                   // This is only useful if type == radio
						array(
							'id' => 'tw_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'tw_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
				array(
					'label' => $this->l('Share Facebook'),
					'name' => BlogConf::formatConfName('SHARE_FB'),
					'type' => 'switch',
					'values' => array(                                   // This is only useful if type == radio
						array(
							'id' => 'fb_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'fb_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true
				),
			),
			'submit' => array(
				'title' => $this->l('Save')
			)
		);

		return parent::renderForm();
	}

	public function ajaxProcessGetCustomers()
	{
		$query = Tools::getValue('guery');
		$customers = Db::getInstance()->executeS('SELECT id_customer as id, CONCAT("№", id_customer, " ", firstname, " ", lastname) as text
			FROM '.BlogDB::getPrefixTable('customer').'
			WHERE CONCAT(id_customer, " ", firstname, " ", lastname) LIKE "%'.pSQL($query).'%"');
		die(Tools::jsonEncode(array(
			'customers' => $customers
		)));
	}
} 