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

class AdminBlogCommentController extends ModuleAdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = BlogCommentArticle::getTable();
		$this->identifier = BlogCommentArticle::getIdTable();
		$this->className = 'BlogCommentArticle';
		$this->bootstrap = true;
		$this->display = 'list';

		$this->_select .= ' CONCAT(c.`firstname`,c.`lastname`) as `customer`, al.`name` as article, al.`'.BlogArticle::getIdTable().'`';
		$this->_join .= ' LEFT JOIN '._DB_PREFIX_.'customer c ON c.`id_customer` = a.`id_customer`';
		$this->_join .= ' LEFT JOIN '.BlogArticle::getPrefixTableLang().' al ON al.`'.BlogArticle::getIdTable().'` = a.`'.BlogArticle::getIdTable()
			.'` AND al.`id_lang` = '.(int)$this->context->language->id;
		$this->_where .= ' AND a.`is_moderated` = 1 AND a.`deleted` = 0';

		$this->fields_list = array(
			BlogCommentArticle::getIdTable() => array('title' => $this->l('ID'),
				'width' => 20,
				'align' => 'center'
			),
			'customer' => array('title' => $this->l('Customer'),
				'search' => true,
				'filter_key' => 'customer'
			),
			'id_blog_article' => array('title' => $this->l('ID Article'),
				'search' => true,
				'filter_key' => 'al!'.BlogArticle::getIdTable(),
				'align' => 'center'
			),
			'article' => array('title' => $this->l('Article'),
				'search' => true,
				'filter_key' => 'al!name'
			),
			'parent_id' => array(
				'title'  => $this->l('Parent ID'),
				'search' => true,
				'filter_key' => 'parent_id',
				'align' => 'center'
			),
			'is_moderated' => array(
				'title' => 'Moderated',
				'type' => 'bool',
				'active' => 'is_moderated',
				'search' => true,
				'filter_key' => 'a!is_moderated',
				'icon' => array(
					0 => 'icon-remove',
					1 => 'icon-check',
					'default' => 'icon-check'
				),
				'align' => 'center'
			),
			'is_active' => array(
				'title' => 'Active',
				'type' => 'bool',
				'active' => 'is_active',
				'search' => true,
				'filter_key' => 'a!is_active',
				'icon' => array(
					0 => 'icon-remove',
					1 => 'icon-check',
					'default' => 'icon-check'
				),
				'align' => 'center'
			),
			'date_add' => array(
				'title' => $this->l('Date add'),
				'type' => 'datetime',
				'search' => true,
				'filter_key' => 'a!date_add'
			)
		);

		$this->addRowAction('edit');
		$this->addRowAction('view');
		$this->addRowAction('delete');

		BlogTool::registerBlogSmartyFunction();
		BlogTools::registerSmartyFunctions();

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
			'confirm' => $this->l('Would you like to delete the selected items?')));
		parent::__construct();
	}

	public function displayModerateLink($token = null, $id, $name = null)
	{
		$tpl = $this->createTemplate('list_action_moderate.tpl');
		if (!array_key_exists('Moderate', self::$cache_lang))
			self::$cache_lang['Moderate'] = $this->l('Moderate', 'Helper');

		$tpl->assign(array_merge($this->tpl_delete_link_vars, array(
			'href' => Tools::safeOutput('index.php?controller='.Tools::getValue('controller')).'&'
				.Tools::safeOutput($this->identifier).'='.(int)$id.'&moderate'.Tools::safeOutput($this->table)
				.'&token='.Tools::safeOutput(($token != null ? $token : $this->token)),
			'action' => self::$cache_lang['Moderate'],
			'name' => Tools::safeOutput($name),
		)));

		return $tpl->fetch();
	}

	public function renderList()
	{
		$query = new DbQuery();
		$query->select('a.*, CONCAT(c.`firstname`,c.`lastname`) as `customer`, al.`name` as article');
		$query->from(BlogCommentArticle::getTable(), 'a');
		$query->leftJoin('customer', 'c', 'c.`id_customer` = a.`id_customer`');
		$query->leftJoin(BlogArticle::getTableLang(), 'al', 'al.`'.BlogArticle::getIdTable().'` = a.`'.BlogArticle::getIdTable().'`');
		$query->where('a.`is_moderated` = 0 AND a.`deleted` = 0');
		$sql = $query->build();
		$list = Db::getInstance()->executeS($sql);

		$list_comment_moderate = new HelperList();
		$list_comment_moderate->shopLinkType = false;
		$list_comment_moderate->currentIndex = 'index.php?controller='.Tools::getValue('controller');
		$list_comment_moderate->table = BlogCommentArticle::getTable();
		$list_comment_moderate->token = Tools::getAdminTokenLite('AdminBlogComment');
		$list_comment_moderate->context->controller = $this;
		$list_comment_moderate->override_folder = 'list_comment_moderate/';
		$list_comment_moderate->actions = array(
			'moderate',
			'delete',
			'edit',
			'view'
		);

		$list_comment_moderate->force_show_bulk_actions = true;
		$list_comment_moderate->title = $this->l('Comment on moderated');
		$list_comment_moderate->listTotal = count($list);
		$list_comment_moderate->identifier = BlogCommentArticle::getIdTable();
		$this->tpl_list_vars['list_comment_moderate'] = $list_comment_moderate->generateList($list, $this->fields_list);

		return parent::renderList();
	}


	public function postProcess()
	{
		if (Tools::isSubmit('is_active'.BlogCommentArticle::getTable()))
		{
			$id_comment_article = Tools::getValue(BlogCommentArticle::getIdTable());
			$comment_article = new BlogCommentArticle($id_comment_article);
			if (Validate::isLoadedObject($comment_article))
			{
				$comment_article->is_active = ($comment_article->is_active ? false : true);
				$comment_article->save();
			}
		}
		if (Tools::isSubmit('is_moderated'.BlogCommentArticle::getTable()))
		{
			$id_comment_article = Tools::getValue(BlogCommentArticle::getIdTable());
			$comment_article = new BlogCommentArticle($id_comment_article);
			if (Validate::isLoadedObject($comment_article))
			{
				$comment_article->is_moderated = ($comment_article->is_moderated ? false : true);
				$comment_article->save();
			}
		}
		if (Tools::isSubmit('moderate'.BlogCommentArticle::getTable()))
		{
			$id_comment_article = Tools::getValue(BlogCommentArticle::getIdTable());
			$comment_article = new BlogCommentArticle($id_comment_article);
			if (Validate::isLoadedObject($comment_article))
			{
				$comment_article->is_moderated = ($comment_article->is_moderated ? false : true);
				$comment_article->save();
			}
		}
		return parent::postProcess();
	}

	public function renderView()
	{
		$query = new DbQuery();
		$query->select('a.*, CONCAT(c.`firstname`,c.`lastname`) as `customer`, al.`name` as article, al.`link_rewrite` as article_link_rewrite');
		$query->from(BlogCommentArticle::getTable(), 'a');
		$query->leftJoin('customer', 'c', 'c.`id_customer` = a.`id_customer`');
		$query->leftJoin(BlogArticle::getTableLang(), 'al', 'al.`'.BlogArticle::getIdTable().'` = a.`'.BlogArticle::getIdTable().'`');
		$query->where('a.`'.BlogCommentArticle::getIdTable().'` ='.(int)Tools::getValue(BlogCommentArticle::getIdTable()));
		$sql = $query->build();
		$row = Db::getInstance()->getRow($sql);

		$this->tpl_view_vars['comment'] = $row;
		$this->tpl_view_vars['blog_link'] = new BlogLink();
		return parent::renderView();
	}


	public function renderForm()
	{
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Edit comment')
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Customer'),
					'name' => 'id_customer',
					'options' => array(
						'query' => $this->getSimpleCustomers(),
						'id' => 'id_customer',
						'name' => 'name'
					),
					'desc' => $this->l('Select customer')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Article'),
					'name' => BlogArticle::getIdTable(),
					'options' => array(
						'query' => BlogArticle::getSimpleArticles($this->context->language->id),
						'id' => BlogArticle::getIdTable(),
						'name' => 'name'
					),
					'desc' => $this->l('Select customer')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Message'),
					'name' => 'message'
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Moderated'),
					'name' => 'is_moderated',
					'values' => array(
						array(
							'id' => 'moderated_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'moderated_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true,
					'desc' => $this->l('Enable/Disable moderated comment')
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Active'),
					'name' => 'is_active',
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'is_bool' => true,
					'desc' => $this->l('Enable/Disable comment')
				),
				array(
					'type' => 'datetime',
					'label' => $this->l('Date add'),
					'name' => 'date_add'
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		return parent::renderForm();
	}

	public function getSimpleCustomers()
	{
		return Db::getInstance()->executeS('SELECT `id_customer`, CONCAT(`firstname`,`lastname`) as `name` FROM '._DB_PREFIX_.'customer');
	}
} 