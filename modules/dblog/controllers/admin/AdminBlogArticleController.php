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

class AdminBlogArticleController extends ModuleAdminController
{
	/**
	 * @var BlogArticle
	 */
	protected $object = null;

	public function __construct()
	{
		$this->table = BlogArticle::getTable();
		$this->identifier = BlogArticle::getIdTable();
		$this->className = 'BlogArticle';
		$this->lang = true;
		$this->bootstrap = true;
		$this->display = 'list';
		parent::__construct();

		$this->_select .= ' CONCAT(e.`firstname`," ",e.`lastname`) as employee, cl.`name` as category ';
		$this->_join .= ' LEFT JOIN '._DB_PREFIX_.'employee e ON e.`id_employee` = a.`id_employee` ';
		$this->_join .= ' LEFT JOIN '.BlogCategory::getPrefixTableLang().' cl
		ON  cl.`'.BlogCategory::getIdTable().'` = a.`'.BlogCategory::getIdTable().'` AND cl.`id_lang` = '.(int)$this->context->language->id;

		$this->fields_list = array(
			BlogArticle::getIdTable() => array(
				'title' => $this->l('ID'),
				'width' => 20,
				'align' => 'center'
			),
			'name' => array('title' => $this->l('Name'),
							'search' => true,
							'filter_key' => 'b!name'),
			'employee' => array(
				'title' => $this->l('Author'),
				'search' => false,
				'orderby' => false
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
			'category' => array(
				'title' => $this->l('Category'),
				'filter_key' => 'cl!name'
			),
			'date_upd' => array(
				'title' => $this->l('Date update'),
				'type' => 'datetime',
				'search' => true,
				'filter_key' => 'a!date_upd'
			),
			'date_add' => array(
				'title' => $this->l('Date add'),
				'type' => 'datetime',
				'search' => true,
				'filter_key' => 'a!date_add'
			)
		);

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
			'confirm' => $this->l('Would you like to delete the selected items?')));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJS($this->module->getPathUri().'views/js/vendor/select2.min.js');
		$this->addCSS($this->module->getPathUri().'views/css/vendor/select2.css');
		$this->addCSS($this->module->getPathUri().'views/css/vendor/css/select2-bootstrap.css');

		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('is_active'.BlogArticle::getTable()))
		{
			$id_article = Tools::getValue(BlogArticle::getIdTable());
			$article = new BlogArticle($id_article);
			if (Validate::isLoadedObject($article))
			{
				$article->is_active = ($article->is_active ? 0 : 1);
				$article->save();
			}
		}

		return parent::postProcess();
	}

	public function getFieldsValue($obj)
	{
		$fields_value = parent::getFieldsValue($obj);

		$fields_value['gallery'] = array();
		if ($obj->id)
			$fields_value['gallery'] = BlogImage::getImagesByArticle($obj->id, 'id_blog_image', 'DESC');
		$fields_value['tags'] = BlogTag::convertSelect2DataTags(BlogTag::getTagsByArticle($this->object->id, $this->context->language->id));
		$fields_value['available_tags'] = BlogTag::convertSelect2DataTags(BlogTag::getTags());
		$fields_value['products'] = BlogArticle::getSelect2Products($this->object->id, $this->context->language->id);

		return $fields_value;
	}

	public function renderForm()
	{
		$this->context->controller->addCSS($this->module->getPathUri().'views/css/admin_blog_article.css');
		$this->context->controller->addJS($this->module->getPathUri().'views/js/admin.js');

		$this->tpl_form_vars['PS_ALLOW_ACCENTED_CHARS_URL'] = (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
		$this->tpl_form_vars['ps_force_friendly_product'] = Configuration::get('PS_FORCE_FRIENDLY_PRODUCT');

		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Edit article')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'required' => true,
					'lang' => true,
					'class' => (!Tools::isSubmit('update'.BlogArticle::getTable()) ? 'copy2friendlyUrl' : '')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Friendly URL:'),
					'name' => 'link_rewrite',
					'required' => true,
					'lang' => true
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Content:'),
					'name' => 'content',
					'lang' => true,
					'autoload_rte' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);

		if ($this->object->id)
		{
			$input_file = array(
				array(
					'type' => 'blog_file',
					'label' => $this->l('Preview:'),
					'name' => 'preview',
					'hint' => $this->l('Upload a category logo from your computer.'),
				),
				array(
					'type' => 'blog_gallery',
					'label' => 'Gallery images',
					'name' => 'gallery'
				),
			);
			$this->fields_form['input'] = array_merge($this->fields_form['input'], $input_file);
		}
		else
		{
			$input_file = array(
				array(
					'type' => 'addAfter',
					'label' => 'Add image',
					'name' => 'free',
					'descError' => 'Will open after save article'
				)
			);
			$this->fields_form['input'] = array_merge($this->fields_form['input'], $input_file);
		}

		$categories = BlogCategory::getSimpleCategories($this->context->language->id);

		$input_fields = array(
			array(
				'type' => 'switch',
				'label' => $this->l('Active:'),
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
				'desc' => $this->l('Enable/Disable article')
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Only moderated comments:'),
				'name' => 'is_only_verified_comments',
				'values' => array(
					array(
						'id' => 'is_only_verified_comments_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'is_only_verified_comments_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
				'is_bool' => false,
				'desc' => $this->l('Enable/Disable only moderated comment')
			),
			array(
				'type' => 'switch',
				'label' => $this->l('View share btn:'),
				'name' => 'view_share_btn',
				'values' => array(
					array(
						'id' => 'view_share_btn_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'view_share_btn_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
				'is_bool' => true,
				'desc' => $this->l('Enable/Disable share btn')
			),
			array(
				'type' => 'switch',
				'label' => $this->l('Switch comment:'),
				'name' => 'is_comment',
				'values' => array(
					array(
						'id' => 'comment_on',
						'value' => 1,
						'label' => $this->l('Enabled')
					),
					array(
						'id' => 'comment_off',
						'value' => 0,
						'label' => $this->l('Disabled')
					)
				),
				'is_bool' => true,
				'desc' => $this->l('Enable/Disable comment in article')
			),
			array(
				'type' => 'select',
				'label' => $this->l('Category'),
				'name' => BlogCategory::getIdTable(),
				'options' => array(
					'query' => $categories,
					'id' => BlogCategory::getIdTable(),
					'name' => 'name'
				),
				'desc' => $this->l('Select category')
			),
		);
		$this->fields_form['input'] = array_merge($this->fields_form['input'], $input_fields);

		if ($this->object->id)
		{
			$input_tags = array(
				array(
					'type' => 'select2tags',
					'label' => $this->l('Tags'),
					'name' => 'tags',
				),
				array(
					'type' => 'select2products',
					'label' => $this->l('Products'),
					'name' => 'products',
					'desc' => $this->l('Select products and add in article')
				),
			);
			$this->fields_form['input'] = array_merge($this->fields_form['input'], $input_tags);
		}
		else
		{
			$input_tags = array(
				array(
					'type' => 'addAfter',
					'label' => $this->l('Add tags and products'),
					'name' => 'free',
					'descError' => $this->l('Will open after save article')
				)
			);
			$this->fields_form['input'] = array_merge($this->fields_form['input'], $input_tags);
		}

		$input_fields = array(
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
		);
		$this->fields_form['input'] = array_merge($this->fields_form['input'], $input_fields);
		return parent::renderForm();
	}

	public function processUpdate()
	{
		$this->processUploadPreview();
		$this->processSaveTags();
		$this->processSaveProducts();
		return parent::processUpdate();
	}

	public function processUploadPreview()
	{
		if (array_key_exists('preview', $_FILES))
		{
			if (array_key_exists('tmp_name', $_FILES['preview']) && $_FILES['preview']['tmp_name'])
			{
				$preview = $_FILES['preview']['tmp_name'];
				if (in_array(exif_imagetype($preview), array(IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG)))
				{
					$image = new BlogImage();
					$image->{BlogArticle::getIdTable()} = $this->object->id;
					$image->cover = 1;
					$image->save();
					if ($image->createImgFolder())
					{
						BlogImageManager::resize($preview, BlogImage::getImageBasePathArticle($image->id).$image->id.'.jpg', null, null, 'jpg', true);
						$types = BlogImageType::getTypes();
						if (count($types))
						{
							foreach ($types as $t)
								BlogImageManager::resize($_FILES['preview']['tmp_name'],
									BlogImage::getImageBasePathArticle($image->id).$image->id.'_'.$t['name'].'.jpg',
									(int)$t['width'], (int)$t['height'], 'jpg', true);
						}
					}
					${'_POST'}['preview'] = $image->id;
				}
				else
					$this->errors[] = $this->l('Preview image type wrong!');
			}
		}
	}

	public function processSaveTags()
	{
		$tags = explode('|', Tools::getValue('tags'));
		if (is_array($tags) && count($tags))
		{
			$this->object->deleteTags($this->object->id);
			foreach ($tags as $tag)
			{
				if ((int)$tag > 0 && $id_tag = BlogTag::existsTagId((int)$tag))
					$this->object->addTag($id_tag);
				elseif (!BlogTag::existsTagName($tag) && !empty($tag))
				{
					$tag_obj = new BlogTag();
					$tag_obj->name = $tag;
					$tag_obj->link_rewrite = Tools::link_rewrite($tag);
					$tag_obj->save();

					$this->object->addTag($tag_obj->id);
				}
				elseif ($id_tag = BlogTag::existsTagName($tag))
					$this->object->addTag($id_tag);
			}
		}
	}

	public function processSaveProducts()
	{
		$products = explode('|', Tools::getValue('products'));
		if (is_array($products) && count($products))
		{
			$this->object->deleteProducts();
			foreach ($products as $product)
			{
				$obj_product = new Product((int)$product);
				if (Validate::isLoadedObject($obj_product))
					$this->object->addProduct($obj_product->id);
			}
		}
	}

	public function ajaxProcessUploadGallery()
	{
		$article = new BlogArticle(Tools::getValue(BlogArticle::getIdTable()));
		$types = BlogImageType::getTypes();

		$errors = array();

		if (array_key_exists('gallery', $_FILES))
		{
			if (array_key_exists('tmp_name', $_FILES['gallery']) && $_FILES['gallery']['tmp_name'])
			{
				$gallery = $_FILES['gallery']['tmp_name'];
				if (in_array(exif_imagetype($gallery), array(IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG)))
				{
					$image = new BlogImage();
					$image->{BlogArticle::getIdTable()} = $article->id;
					$image->cover = 0;
					$image->save();
					$image->createImgFolder();
					BlogImageManager::resize($gallery, BlogImage::getImageBasePathArticle($image->id).$image->id.'.jpg', null, null, 'jpg', true);
					if (count($types))
					{
						foreach ($types as $type)
							BlogImageManager::resize($gallery, BlogImage::getImageBasePathArticle($image->id).$image->id.'_'.$type['name'].'.jpg',
								$type['width'], $type['height'], 'jpg', true);
					}

					$this->context->smarty->assign(array(
						'name' => 'gallery',
						'path' => BlogImage::getImgPath($image->id, 'preview_logo'),
						'id' => $image->id
					));

					die(Tools::jsonEncode(array(
						'hasError' => false,
						'image' => $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->module->name
							.'/views/templates/admin/blog_article/helpers/form/gallery_image.tpl')
					)));
				}
				else
					$errors[] = $this->l('Gallery image type wrong!');
			}
		}

		die(Tools::jsonEncode(array(
			'hasError' => (count($errors) ? true : false),
			'errors' => $errors
		)));
	}

	public function ajaxProcessDeleteGallery()
	{
		$image = new BlogImage((int)Tools::getValue('id'));
		if (Validate::isLoadedObject($image))
		{
			$image->delete();
			die(Tools::jsonEncode(array(
				'hasError' => false
			)));
		}
		else
			die(Tools::jsonEncode(array(
				'hasError' => true
			)));
	}

	public function ajaxProcessDeletePreview()
	{
		$image = new BlogImage((int)Tools::getValue('id'));
		if (Validate::isLoadedObject($image))
		{
			$article = new BlogArticle($image->{BlogArticle::getIdTable()});
			$article->preview = 0;
			$article->save();
			$image->delete();
			die(Tools::jsonEncode(array(
				'hasError' => false
			)));
		}
		else
			die(Tools::jsonEncode(array(
				'hasError' => true
			)));
	}

	public function ajaxProcessGetProducts()
	{
		$query = Tools::getValue('guery');
		$products = Db::getInstance()->executeS('SELECT id_product as id, name as text
			FROM '.BlogDB::getPrefixTableLang('product').'
			WHERE name LIKE "%'.pSQL($query).'%" AND id_lang = '.(int)$this->context->language->id);
		die(Tools::jsonEncode(array(
			'products' => $products
		)));
	}
} 