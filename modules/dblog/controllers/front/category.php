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

class DBlogCategoryModuleFrontController extends BlogModuleFrontController
{
	/**
	 * @var BlogCategory
	 */
	protected $category = null;

	public function init()
	{
		$link_rewrite = Tools::getValue('link_rewrite');
		if (Validate::isLinkRewrite($link_rewrite))
		{
			$category = BlogCategory::getInstanceCategoryByLinkRewrite($link_rewrite);
			$this->category = $category;
			if (Validate::isLoadedObject($category))
				$this->post_parameters['id_category'] = $category->id;
			else
				Tools::redirect($this->blog_link->getBlogLink());
		}
		else
			Tools::redirect($this->blog_link->getBlogLink());
		parent::init();

		if (!is_null($this->category))
		{
			$meta = array();
			$meta['meta_title'] = (Tools::strlen($this->category->meta_title) ? $this->category->meta_title : BlogConf::getConf('META_TITLE'));
			$meta['meta_keywords'] = (Tools::strlen($this->category->meta_keyword) ? $this->category->meta_keyword : BlogConf::getConf('META_KEYWORDS'));
			$meta['meta_description'] = (Tools::strlen($this->category->meta_description)
				? $this->category->meta_description : BlogConf::getConf('META_DESCRIPTION'));
			$this->context->smarty->assign($meta);
		}
	}


	public function initContent()
	{
		parent::initContent();
		$this->context->smarty->assign(BlogArticle::getDataArticles($this->getPostParameters()));
		$this->context->smarty->assign(array(
			'category' => $this->category,
			'path' => ($this->category ? $this->category->name : BlogConf::getConf('META_TITLE')),
			'request_link' => $this->blog_link->getCategoryLink($this->category->link_rewrite)
		));
		$this->setTemplate('category.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->context->controller->addCSS(_MODULE_DIR_._MODULE_NAME_.'/views/css/front.css');
	}


}