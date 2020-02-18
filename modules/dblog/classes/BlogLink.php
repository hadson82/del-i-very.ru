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

class BlogLink
{

	public function getCategoryLink($link_rewrite, $id_shop = null, $ssl = null)
	{
		if ($this->isBlogLink())
			return $this->getBaseLinkBlog($id_shop, $ssl).$link_rewrite.'/';
		else
			return $this->getModuleBlogLink('category', array(
				'link_rewrite' => $link_rewrite
			), $ssl);
	}

	public function getArticleLink($link_rewrite, $category = null, $id_shop = null, $ssl = null)
	{
		if (is_null($category))
		{
			$article = BlogArticle::getInstanceByLinkRewrite($link_rewrite);
			if (Validate::isLoadedObject($article))
			{
				$obj_category = new BlogCategory($article->{BlogCategory::getIdTable()}, Context::getContext()->language->id);
				$category = $obj_category->link_rewrite;
			}
		}

		if ($this->isBlogLink())
			return $this->getBaseLinkBlog($id_shop, $ssl).($category ? $category.'/' : '').$link_rewrite.'.html';
		else
			return $this->getModuleBlogLink('article', array(
				'category' => $category,
				'link_rewrite' => $link_rewrite
			), $ssl);
	}

	public function getTagLink($link_rewrite, $id_shop = null, $ssl = null)
	{
		if ($this->isBlogLink())
			return $this->getBaseLinkBlog($id_shop, $ssl).'tag/'.$link_rewrite.'/';
		else
			return $this->getModuleBlogLink('tag', array(
				'link_rewrite' => $link_rewrite
			), $ssl);
	}

	public function getBlogLink($id_shop = null, $ssl = null)
	{
		if ($this->isBlogLink())
			return $this->getBaseLinkBlog($id_shop, $ssl);
		else
			return $this->getModuleBlogLink('home');
	}

	public function getAPILink($id_shop = null, $ssl = null)
	{
		if ($this->isBlogLink())
			return $this->getBaseLinkBlog($id_shop, $ssl).'api';
		else
			return $this->getModuleBlogLink('api');
	}

	protected static $module = null;

	protected function getBaseLinkBlog($id_shop = null, $ssl = null)
	{
		static $force_ssl = null;

		if ($ssl === null)
		{
			if ($force_ssl === null)
				$force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
			$ssl = $force_ssl;
		}

		if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null)
			$shop = new Shop($id_shop);
		else
			$shop = Context::getContext()->shop;

		$base = (($ssl && $this->ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);

		if (is_null(self::$module))
			self::$module = Module::getInstanceByName(_MODULE_NAME_);

		$root = BlogConf::getConf('ROUTE_NAME');

		$iso_code = null;
		if (Language::isMultiLanguageActivated())
			$iso_code = Context::getContext()->language->iso_code;

		return $base.$shop->getBaseURI().(!is_null($iso_code) ? $iso_code.'/' : '').$root.'/';
	}

	public function getPaginationLink($request_link, $page)
	{
		return $request_link.(strpos($request_link, '?') !== false ? '&' : '?').'p='.(int)$page;
	}

	public $is_blog_link = null;
	public function isBlogLink()
	{
		if (!is_null($this->is_blog_link))
			return $this->is_blog_link;

		$module = Module::getInstanceByName(BlogTools::getModNameForPath(__FILE__));
		$is_blog_link = $module->isRegisteredInHook('moduleRoutes') && Configuration::get('PS_REWRITING_SETTINGS');
		$this->is_blog_link = $is_blog_link;

		return $this->is_blog_link;
	}

	public function getModuleBlogLink($controller = 'default', $params = array(), $ssl = null)
	{
		return Context::getContext()->link->getModuleLink(BlogTools::getModNameForPath(__FILE__), $controller, $params, $ssl);
	}
} 