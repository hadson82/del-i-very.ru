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

require_once(_PS_MODULE_DIR_.basename(dirname(__FILE__)).'/config.php');

class DBlog extends Module
{
	private $install_classes = array(
		'BlogArticle',
		'BlogCategory',
		'BlogCommentArticle',
		'BlogImage',
		'BlogImageType',
		'BlogTag');

	public $config = null;

	public function __construct()
	{
		$this->name = 'dblog';
		$this->tab = 'front_office_features';
		$this->version = '1.1.14';
		$this->author = 'DaRiuS';
		$this->need_instance = '0';
		parent::__construct();
		$this->displayName = $this->l('Blog');
		$this->description = $this->l('Best articles management');
		$this->blog_link = new BlogLink();
		$this->blog_image = new BlogImage();
		$this->blog_tool = new BlogTool();
		$this->module_key = '1ecace65cff1078e7e04682640ebb971';

		$this->config = array(
			'CUSTOMERS_HAVE_ADMIN_RIGHTS' => '',
			'LIMIT_ARTICLES' => 15,
			'LIMIT_COMMENTS' => 10,
			'INTERVAL_UPDATE' => 5000,
			'DATE_FORMAT' => 'd F Y',
			'META_TITLE' => 'Blog Prestashop',
			'META_KEYWORDS' => 'Blog Prestashop',
			'META_DESCRIPTION' => 'Blog Prestashop',
			'ROUTE_NAME' => 'blog',
			'EMAIL' => Configuration::get('PS_SHOP_EMAIL'),
			'SHOW_WHO_CREATE_ARTICLE' => 1,
			'SHOW_BLOCK_CATEGORIES' => 1,
			'SHOW_BLOCK_TAGS' => 1,
			'USE_SMART_CROP_IMAGE' => 1,
			'SMART_CROP_IMAGE_RESIZE_UP' => 1,
			'SHARE_VK' => '1',
			'SHARE_OD' => '1',
			'SHARE_TW' => '1',
			'SHARE_FB' => '1',
		);
	}

	public function install()
	{
		$this->installClasses();
		$this->installSQLRelationTables();
		$this->installTabs();
		BlogConf::installConf($this->config);
		$this->installDefaultContent();

		if (!parent::install() ||
			!$this->registerHook('displayHome') ||
			!$this->registerHook('displayLeftColumn') ||
			!$this->registerHook('displayLeftColumn') ||
			!$this->registerHook('moduleRoutes') ||
			!$this->registerHook('displayBackOfficeHeader'))
			return false;

		return true;
	}

	public function uninstall()
	{
		$this->uninstallClasses();
		$this->uninstallSQLRelationTables();
		$this->uninstallTabs();
		BlogConf::uninstallConf($this->config);

		if (!parent::uninstall())
			return false;

		return true;
	}

	public function installClasses()
	{
		foreach ($this->install_classes as $class)
			BlogHelperDb::loadClass($class)->installDb();
	}

	public function uninstallClasses()
	{
		foreach ($this->install_classes as $class)
			BlogHelperDb::loadClass($class)->uninstallDb();
	}

	public function installSQLRelationTables()
	{
		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_BLOG_PREFIX_.'article_tag`
		(
		  `'.BlogArticle::getIdTable().'` int(11) NOT NULL,
		  `'.BlogTag::getIdTable().'` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

		Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_BLOG_PREFIX_.'article_product` (
		  `'.BlogArticle::getIdTable().'` int(11) NOT NULL,
		  `id_product` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;');
	}
	public function uninstallSQLRelationTables()
	{
		Db::getInstance()->execute('DROP TABLE '._DB_BLOG_PREFIX_.'article_tag');
		Db::getInstance()->execute('DROP TABLE '._DB_BLOG_PREFIX_.'article_product');
	}

	public function installDefaultContent()
	{
		Db::getInstance()->insert(BlogImageType::getTable(), array(
			array(BlogImageType::getIdTable() => 1, 'width' => 900, 'height' => 300, 'name' => 'category'),
			array(BlogImageType::getIdTable() => 2, 'width' => 900, 'height' => 400, 'name' => 'article'),
			array(BlogImageType::getIdTable() => 4, 'width' => 20, 'height' => 20, 'name' => 'small'),
			array(BlogImageType::getIdTable() => 5, 'width' => 200, 'height' => 200, 'name' => 'preview_logo'),
			array(BlogImageType::getIdTable() => 6, 'width' => 370, 'height' => 205, 'name' => 'home'),
			array(BlogImageType::getIdTable() => 7, 'width' => 84, 'height' => 56, 'name' => 'gallery_thumb')
		));
	}

	public function installTabs()
	{
		$this->createTab('AdminBlogParent', null, array(
			'en' => 'Blog',
			'ru' => 'Блог'
		));

		$this->createTab('AdminBlogSetting', 'AdminBlogParent', array(
			'en' => 'Setting blog',
			'ru' => 'Настройки блога'
		));

		$this->createTab('AdminBlogArticle', 'AdminBlogParent', array(
			'en' => 'Articles',
			'ru' => 'Статьи'
		));

		$this->createTab('AdminBlogCategory', 'AdminBlogParent', array(
			'en' => 'Categories articles',
			'ru' => 'Категории статей'
		));

		$this->createTab('AdminBlogComment', 'AdminBlogParent', array(
			'en' => 'Comments articles',
			'ru' => 'Комментарии статей'
		));

		$this->createTab('AdminBlogTag', 'AdminBlogParent', array(
			'en' => 'Tags',
			'ru' => 'Теги'
		));

		$this->createTab('AdminBlogImageType', 'AdminBlogParent', array(
			'en' => 'Image type articles',
			'ru' => 'Типы изображений статей'
		));
	}

	public function uninstallTabs()
	{
		$this->deleteTab('AdminBlogSetting');
		$this->deleteTab('AdminBlogArticle');
		$this->deleteTab('AdminBlogCategory');
		$this->deleteTab('AdminBlogComment');
		$this->deleteTab('AdminBlogTag');
		$this->deleteTab('AdminBlogImageType');
		$this->deleteTab('AdminBlogParent');
	}

	public function preInitHook()
	{
		$this->context->smarty->assign(array(
			'blog_image' => new BlogImage(),
			'blog_tool' => new BlogTool(),
			'blog_link' => new BlogLink(),
			'path_blog' => _PS_MODULE_DIR_.$this->name.'/views/templates/front/'
		));
	}

	public function hookDisplayHome()
	{
		$this->context->controller->addCSS($this->_path.'views/css/front.css');
		$this->context->controller->addCSS($this->_path.'views/css/vendor/owl.carousel.css');
		$this->context->controller->addCSS($this->_path.'views/css/vendor/owl.transitions.css');
		$this->context->controller->addCSS($this->_path.'views/css/vendor/owl.theme.css');
		$this->context->controller->addJS($this->_path.'views/js/vendor/owl.carousel.min.js');
		$this->context->smarty->assign(BlogArticle::getDataArticles());
		$this->preInitHook();
		return $this->display(__FILE__, 'home.tpl');
	}

	public function hookDisplayLeftColumn()
	{
	    $html = '';
        $this->context->controller->addCSS($this->getPathUri().'/views/css/left.css');
        $this->context->smarty->assign(BlogArticle::getDataArticles(array(
            'n' => 2
        )));
        $this->preInitHook();
        $html .= $this->display(__FILE__, 'left_column.tpl');

		if (Tools::getValue('module') == $this->name)
		{
			if (!BlogConf::getConf('SHOW_BLOCK_TAGS') && !BlogConf::getConf('SHOW_BLOCK_CATEGORIES'))
				return '';
			$this->preInitHook();
			if (BlogConf::getConf('SHOW_BLOCK_CATEGORIES'))
				$this->context->smarty->assign(array(
					'categories' => BlogCategory::getCategories($this->context->language->id)
				));

			if (BlogConf::getConf('SHOW_BLOCK_TAGS'))
				$this->context->smarty->assign(array(
					'tags' => BlogTag::getTags()
				));
			$html .= $this->display(__FILE__, 'column.tpl');
		}
		return $html;
	}

	public function hookDisplayRightColumn()
	{
		return $this->hookDisplayLeftColumn();
	}

	public function hookModuleRoutes()
	{
		$root = BlogConf::getConf('ROUTE_NAME');

		$my_routes = array(
			'module-'.$this->name.'-home' => array(
				'controller' => 'home',
				'rule' => $root.'/',
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => $this->name,
				),
			),
			'module-'.$this->name.'-category' => array(
				'controller' => 'category',
				'rule' => $root.'/{link_rewrite}/',
				'keywords' => array(
					'link_rewrite' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'link_rewrite'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => $this->name,
				),
			),
			'module-'.$this->name.'-article' => array(
				'controller' => 'article',
				'rule' => $root.'/{category:/}{link_rewrite}.html',
				'keywords' => array(
					'link_rewrite' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'link_rewrite'),
					'category' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'category'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => $this->name,
				),
			),
			'module-'.$this->name.'-api' => array(
				'controller' => 'api',
				'rule' => $root.'/api',
				'keywords' => array(),
				'params' => array(
					'fc' => 'module',
					'module' => $this->name,
				),
			),
			'module-'.$this->name.'-tag' => array(
				'controller' => 'tag',
				'rule' => $root.'/tag/{link_rewrite}/',
				'keywords' => array(
					'link_rewrite' =>        array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'link_rewrite'),
				),
				'params' => array(
					'fc' => 'module',
					'module' => $this->name,
				),
			)
		);

		return $my_routes;
	}

	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->getPathUri().'views/css/admin_header.css');
	}

	public function createTab($class_name, $parent = null, $name)
	{
		if (!is_array($name))
			$name = array('en' => $name);
		elseif (is_array($name) && !count($name))
			$name = array('en' => $class_name);
		elseif (is_array($name) && count($name) && !isset($name['en']))
			$name['en'] = current($name);

		$tab = new Tab();
		$tab->class_name = $class_name;
		$tab->module = $this->name;
		$tab->id_parent = (!is_null($parent) ? Tab::getIdFromClassName($parent) : 0);
		$tab->active = true;
		foreach ($this->getLanguages() as $l)
			$tab->name[$l['id_lang']] = (isset($name[$l['iso_code']]) ? $name[$l['iso_code']] : $name['en']);
		$tab->save();
	}

	public function deleteTab($class_name)
	{
		$tab = Tab::getInstanceFromClassName($class_name);
		$tab->delete();
	}

	public $languages = array();
	public function getLanguages($active = true)
	{
		$cache_id = md5($active);
		if (array_key_exists($cache_id, $this->languages))
			return $this->languages[$cache_id];
		$languages = Language::getLanguages($active);
		foreach ($languages as &$l)
			$l['is_default'] = (Configuration::get('PS_LANG_DEFAULT') == $l['id_lang']);
		$this->languages[$cache_id] = $languages;
		return $languages;
	}
} 