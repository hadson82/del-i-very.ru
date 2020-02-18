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

class ModuleSMT extends Module
{
	/**
	 * @var array
	 */
	public $hooks = array();

	/**
	 * @var array
	 */
	public $classes = array();

	/**
	 * @var array
	 */
	public $config = array();

	/**
	 * @var array
	 */
	public $tabs = array();

	public $documentation = true;
	public $documentation_type = null;

	const DOCUMENTATION_TYPE_TAB = 'tab';
	const DOCUMENTATION_TYPE_SIMPLE = 'simple';

	public function __construct()
	{
		$this->name = ToolsModuleSMT::getModNameForPath(__FILE__);
		$this->documentation_type = self::DOCUMENTATION_TYPE_SIMPLE;
		$this->bootstrap = true;
		parent::__construct();
	}

	/**
	 * @return bool
	 */
	public function registerHooks()
	{
		foreach ($this->hooks as $hook)
			$this->registerHook($hook);
		return true;
	}

	/**
	 * @return bool
	 */
	public function installClasses()
	{
		foreach ($this->classes as $class)
			HelperDbSMT::loadClass($class)->installDb();
		return true;
	}

	/**
	 * @return bool
	 */
	public function uninstallClasses()
	{
		foreach ($this->classes as $class)
			HelperDbSMT::loadClass($class)->uninstallDb();
		return true;
	}

	/**
	 * @return bool
	 */
	public function installConfig()
	{
		foreach ($this->config as $name => $value)
			ConfSMT::setConf($name, $value);
		return true;
	}

	/**
	 * @return bool
	 */
	public function uninstallConfig()
	{
		foreach (array_keys($this->config) as $name)
			ConfSMT::deleteConf($name);
		return true;
	}

	/**
	 * @return bool
	 */
	public function installTabs()
	{
		foreach ($this->tabs as $tab)
			ToolsModuleSMT::createTab($this->name, $tab['tab'], $tab['parent'], $tab['name']);
		return true;
	}

	/**
	 * @return bool
	 */
	public function uninstallTabs()
	{
		foreach ($this->tabs as $tab)
			ToolsModuleSMT::deleteTab($tab['tab']);
		return true;
	}

	/**
	 * @return bool
	 */
	public function install()
	{
		return parent::install()
		&& $this->registerHooks()
		&& $this->installClasses()
		&& $this->installConfig()
		&& $this->installTabs();
	}

	/**
	 * @return bool
	 */
	public function uninstall()
	{
		return parent::uninstall()
		&& $this->uninstallClasses()
		&& $this->uninstallConfig()
		&& $this->uninstallTabs();
	}

	public function getDocumentation()
	{
		DocumentationSMT::assignDocumentation();
		$return_back_link = '#';
		if (count($this->tabs))
			$return_back_link = $this->context->link->getAdminLink($this->tabs[0]['tab']);

		$this->context->smarty->assign('return_back_link', $return_back_link);
		return ToolsModuleSMT::fetchTemplate('admin/documentation.tpl');
	}

	public function getContent()
	{
		if (!$this->documentation)
			return $this->getContentTab();
		else
		{
			if ($this->documentation_type == self::DOCUMENTATION_TYPE_SIMPLE)
				return $this->getDocumentation();
			ToolsModuleSMT::registerSmartyFunctions();
			$this->context->smarty->assign(array(
				'content_tab' => $this->getContentTab(),
				'documentation' => $this->getDocumentation()
			));
			return ToolsModuleSMT::fetchTemplate('admin/content.tpl');
		}
	}

	public function getContentTab()
	{
	}
}