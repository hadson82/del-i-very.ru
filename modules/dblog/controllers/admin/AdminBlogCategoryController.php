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

class AdminBlogCategoryController extends ModuleAdminController
{

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = BlogCategory::getTable();
		$this->identifier = BlogCategory::getIdTable();
		$this->className = 'BlogCategory';
		$this->lang = true;
		$this->bootstrap = true;
		$this->display = 'list';

		$this->fields_list = array(
			BlogCategory::getIdTable() => array('title' => $this->l('ID'),
				'width' => 20
			),
			'name' => array('title' => $this->l('Name'),
				'search' => true,
				'filter_key' => 'b!name'
			)
		);

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
			'confirm' => $this->l('Would you like to delete the selected items?')));
		parent::__construct();
	}

	public function renderForm()
	{
		$this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		$this->tpl_form_vars['ps_force_friendly_product'] = Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Edit category')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'required' => true,
					'lang' => true,
					'class' => (!Tools::isSubmit('update'.BlogCategory::getTable()) ? 'copy2friendlyUrl' : '')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Friendly URL:'),
					'name' => 'link_rewrite',
					'required' => true,
					'lang' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title (SEO)'),
					'name' => 'meta_title'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta keyword (SEO)'),
					'name' => 'meta_keyword'
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Meta description (SEO)'),
					'name' => 'meta_description'
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		$this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		$this->tpl_form_vars['ps_force_friendly_product'] = Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

		return parent::renderForm();
	}
} 