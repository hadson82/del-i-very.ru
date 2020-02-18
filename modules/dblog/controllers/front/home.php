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

class DBlogHomeModuleFrontController extends BlogModuleFrontController
{
	public function init()
	{
		parent::init();
		$meta = array();
		$meta['meta_title'] = BlogConf::getConf('META_TITLE');
		$meta['meta_keywords'] = BlogConf::getConf('META_KEYWORDS');
		$meta['meta_description'] = BlogConf::getConf('META_DESCRIPTION');
		$this->context->smarty->assign($meta);
	}


	public function initContent()
	{
		parent::initContent();
		$this->context->smarty->assign(BlogArticle::getDataArticles($this->post_parameters));
		$this->context->smarty->assign(array(
			'path' => BlogConf::getConf('META_TITLE'),
			'request_link' => $this->blog_link->getBlogLink()
		));
		$this->setTemplate('category.tpl');
	}

	public function setMedia()
	{
		$this->context->controller->addCSS(_MODULE_DIR_._MODULE_NAME_.'/views/css/front.css');
		return parent::setMedia();
	}

} 