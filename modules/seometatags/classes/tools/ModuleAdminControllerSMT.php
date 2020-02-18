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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2012-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class ModuleAdminControllerSMT extends ModuleAdminController
{
	public $redirect_to_controller = false;

	public function __construct()
	{
		if ($this->redirect_to_controller)
		{
			$this->context = Context::getContext();
			$this->table = 'configuration';
			$this->identifier = 'id_configuration';
			$this->className = 'Configuration';
			$this->lang = false;
			$this->bootstrap = true;
			$this->display = 'list';
		}

		parent::__construct();

		if ($this->redirect_to_controller)
			Tools::redirectAdmin($this->context->link->getAdminLink($this->redirect_to_controller, true));
		ToolsModuleSMT::registerSmartyFunctions();
		ToolsModuleSMT::globalAssignVar();
		ToolsModuleSMT::convertJSONRequestToPost();
	}

	public function assignModuleTabAdminLink()
	{
		$this->context->smarty->assign('link_to_documentation', ToolsModuleSMT::getModuleTabAdminLink());
	}

	public function renderList()
	{
		if ($this->module->documentation)
		{
			$this->assignModuleTabAdminLink();
			return ToolsModuleSMT::fetchTemplate('admin/documentation_row.tpl').parent::renderList();
		}
		else
			return parent::renderList();
	}

	public function renderView()
	{
		if ($this->module->documentation)
		{
			$this->assignModuleTabAdminLink();
			return ToolsModuleSMT::fetchTemplate('admin/documentation_row.tpl').parent::renderView();
		}
		else
			return parent::renderView();
	}

	public function renderForm()
	{
		if ($this->module->documentation)
		{
			$this->assignModuleTabAdminLink();
			return ToolsModuleSMT::fetchTemplate('admin/documentation_row.tpl').parent::renderForm();
		}
		else
			return parent::renderForm();
	}

	public function initAngular()
	{
		ToolsModuleSMT::autoloadCSS($this->module->getPathUri().'views/css/autoload/');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/lib/angular/vendor/jquery.fileStyle.js');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/lib/angular/vendor/jquery.binarytransport.js');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/lib/angular/vendor/angular.js');
		AngularAppSMT::getInstance($this->module->getPathUri().'views/js/lib/angular/vendor/packages/lazy-load/')->autoloadApp();
		AngularAppSMT::getInstance($this->module->getPathUri().'views/js/lib/angular/')->autoloadApp();
	}

	public $return = array();
	public function ajaxProcessApi()
	{
		ToolsModuleSMT::setErrorHandler();
		ToolsModuleSMT::createAjaxApiCall($this);
	}

	protected function assignAngularFiles()
	{
		$angular_templates_folder = $this->module->getLocalPath().'views/templates/admin/angular-templates';
		$angular_templates = ToolsModuleGC::globRecursive($angular_templates_folder.'/**.tpl');

		foreach ($angular_templates as &$path)
			$path = str_replace($angular_templates_folder.'/', '', $path);
		unset($path);

		$this->context->smarty->assign('angular_templates', $angular_templates);
		$this->context->smarty->assign('path_angular', _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/');
	}
}