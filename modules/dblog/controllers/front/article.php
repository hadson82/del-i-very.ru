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

class DBlogArticleModuleFrontController extends BlogModuleFrontController
{

	/**
	 * @var BlogArticle
	 */
	public $article;

	public function init()
	{
		if ($link_rewrite = Tools::getValue('link_rewrite'))
		{
			$article = BlogArticle::getInstanceByLinkRewrite($link_rewrite);
			if (Validate::isLoadedObject($article) && $article->is_active)
				$this->article = $article;
			else
				Tools::redirect($this->blog_link->getBlogLink());
		}
		parent::init();

		$meta = array();
		$meta['meta_title'] = (Tools::strlen($this->article->meta_title) ? $this->article->meta_title : BlogConf::getConf('META_TITLE'));
		$meta['meta_keywords'] = (Tools::strlen($this->article->meta_keyword) ? $this->article->meta_keyword : BlogConf::getConf('META_KEYWORDS'));
		$meta['meta_description'] = (Tools::strlen($this->article->meta_description) ?
			$this->article->meta_description : BlogConf::getConf('META_DESCRIPTION'));
		$this->context->smarty->assign($meta);
	}


	public function initContent()
	{
		parent::initContent();
		$category = new BlogCategory($this->article->{BlogCategory::getIdTable()}, $this->context->language->id);
		$this->context->smarty->assign(array(
			'article' => $this->article,
			'images' => $this->blog_image->getImagesByArticle($this->article->id),
			'path' => ($this->article->{BlogCategory::getIdTable()} ?
					'<a href="'.$this->blog_link->getCategoryLink($category->link_rewrite).'">'
					.$category->name
					.'</a><span class="navigation-pipe">></span>' : '').'<span class="navigation_page_blog">'
				.($this->article ? $this->article->name : BlogConf::getConf('META_TITLE')).'</span>',
			'employee' => new Employee($this->article->id_employee),
			'category' => $category,
			'link_share' => $this->blog_link->getArticleLink($this->article->link_rewrite),
			'products' => BlogArticle::getProductsFull($this->article->id, $this->context->language->id),
			'share' => array(
				'VK' => BlogConf::getConf('SHARE_VK'),
				'OD' => BlogConf::getConf('SHARE_OD'),
				'TW' => BlogConf::getConf('SHARE_TW'),
				'FB' => BlogConf::getConf('SHARE_FB'),
			),
			'tags' => BlogTag::getTagsByArticle($this->article->id, $this->context->language->id),
			'total_comment' => BlogCommentArticle::getTotalCommentsByArticle($this->article->id),
			'comments' => BlogCommentArticle::getComments($this->article->id, $this->article->is_only_verified_comments),
			'nb_comment' => BlogCommentArticle::getComments($this->article->id, $this->article->is_only_verified_comments, true, 0, 0, null, null, true),
			'limit_view' => BlogConf::getConf('LIMIT_COMMENTS'),
			'customer' => $this->context->customer
		));

		$this->setTemplate('article.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/front_article.css');
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/front.css');

		$this->context->controller->addJS($this->module->getPathUri().'views/js/vendor/fotorama.js');
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/vendor/fotorama.css');

		$this->context->controller->addJS($this->module->getPathUri().'views/js/comments.js');
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/comments.css');

		$this->context->controller->addJS($this->module->getPathUri().'views/js/vendor/owl.carousel.min.js');
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/vendor/owl.carousel.css');
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/vendor/owl.theme.css');
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/vendor/owl.transitions.css');
	}
} 