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

class BlogModuleFrontController extends ModuleFrontController
{

	/**
	 * @var BlogLink
	 */
	public $blog_link = null;
	/**
	 * @var BlogImage
	 */
	public $blog_image = null;
	/**
	 * @var BlogTool
	 */
	public $blog_tool;

	protected $post_parameters = array();
	protected $customers_have_admin_rights = array();

	public function __construct()
	{
		parent::__construct();
		$this->blog_link = new BlogLink();
		$this->blog_image = new BlogImage();
		$this->blog_tool = new BlogTool();
		BlogTool::registerBlogSmartyFunction();
		BlogTools::registerSmartyFunctions();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->context->controller->addCSS($this->module->getPathUri().'/views/css/admin-theme.css');
		$this->context->controller->addCSS($this->module->getPathUri().'/views/css/admin-theme-grid.css');
	}

	public function initContent()
	{
		parent::initContent();

		$this->customers_have_admin_rights = (BlogConf::getConf('CUSTOMERS_HAVE_ADMIN_RIGHTS') ?
			explode('|', BlogConf::getConf('CUSTOMERS_HAVE_ADMIN_RIGHTS')) : array());

		$this->context->smarty->assign(array(
			'blog_image' => $this->blog_image,
			'blog_tool' => $this->blog_tool,
			'blog_link' => $this->blog_link,
			'path_blog' => _PS_MODULE_DIR_._MODULE_NAME_.'/views/templates/front/',
			'customers_have_admin_rights' => $this->customers_have_admin_rights
		));
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function setPostParameters($name, $value)
	{
		$this->post_parameters[$name] = $value;
	}

	/**
	 * @return array
	 * @return void
	 */
	public function getPostParameters()
	{
		return $this->post_parameters;
	}

	/**
	 * @param $name
	 * @return void
	 */
	public function deletePostParameters($name)
	{
		unset($this->post_parameters[$name]);
	}
} 