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

class AdminBlogImageTypeController extends ModuleAdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = BlogImageType::getTable();
		$this->identifier = BlogImageType::getIdTable();
		$this->className = 'BLogImageType';
		$this->bootstrap = true;
		$this->display = 'list';

		$this->fields_list = array(
			BlogImageType::getIdTable() => array('title' => $this->l('ID'),
				'width' => 20
			),
			'width' => array('title' => $this->l('Width'),
				'search' => false,
				'filter_key' => 'a!width'
			),
			'height' => array('title' => $this->l('Height'),
				'search' => false,
				'filter_key' => 'a!height'
			),
			'name' => array('title' => $this->l('Name'),
				'search' => false,
				'filter_key' => 'a!name'
			)
		);

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
			'confirm' => $this->l('Would you like to delete the selected items?')));
		BlogTools::registerSmartyFunctions();
		parent::__construct();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitSaveGenerateImage'))
		{
			$count_generate = 0;

			$type = Tools::getValue(BlogImageType::getIdTable());
			$continue = Tools::getValue('continue');
			$types_image = BlogImageType::getTypes();
			$images = BlogImage::getImages();

			foreach ($images as $image)
			{
				if ($type == 0)
				{
					if (count($types_image))
					{
						foreach ($types_image as $type_image)
						{
							if (file_exists(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()])
								.$image[BlogImage::getIdTable()].'_'.$type_image['name'].'.jpg'))
								unlink(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()]).$image[BlogImage::getIdTable()].'_'.$type_image['name'].'.jpg');
						}
					}
				}
				else
				{
					$type_image = $types_image[(int)$type];
					if (file_exists(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()])
						.$image[BlogImage::getIdTable()].'_'.$type_image['name'].'.jpg'))
						unlink(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()]).$image[BlogImage::getIdTable()].'_'.$type_image['name'].'.jpg');
				}
			}

			foreach ($images as $image)
			{
				if ($type == 0)
				{
					if (count($types_image))
					{
						foreach ($types_image as $type_image)
						{
							if ($continue && file_exists(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()])
									.$image[BlogImage::getIdTable()].'_'.$type_image['name'].'.jpg'))
								continue;
							BlogImageManager::resize(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()])
								.$image[BlogImage::getIdTable()].'.jpg',
								BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()]).$image[BlogImage::getIdTable()]
								.'_'.$type_image['name'].'.jpg', $type_image['width'], $type_image['height'], 'jpg');
							$count_generate++;
						}
					}
				}
				else
				{
					$type_image = $types_image[(int)$type];
					if ($continue && file_exists(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()])
							.$image[BlogImage::getIdTable()].'_'.$type_image['name'].'.jpg'))
						continue;
					BlogImageManager::resize(BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()]).$image[BlogImage::getIdTable()].'.jpg',
						BlogImage::getImageBasePathArticle($image[BlogImage::getIdTable()]).$image[BlogImage::getIdTable()]
						.'_'.$type_image['name'].'.jpg', $type_image['width'], $type_image['height'], 'jpg');
					$count_generate++;
				}
			}

			$this->tpl_list_vars['count_generate'] = $count_generate;
		}

		return parent::postProcess();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Edit image type article')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Width:'),
					'name' => 'width',
					'required' => true,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Height:'),
					'name' => 'height',
					'required' => true,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Name'),
					'name' => 'name'
				)
			),
			'submit' => array(
				'title' => $this->l('Save')
			)
		);
		return parent::renderForm();
	}


	public function renderList()
	{
		$fields_generate_images = array(
			array('form' => array(
						'legend' => array(
								'title' => $this->l('Generate images')
						),
						'input' => array(
							array(
							'type' => 'select',
							'label' => $this->l('Type image'),
							'name' => BlogImageType::getIdTable(),
							'options' => array(
								'query' => array_merge(array(array(BlogImageType::getIdTable() => 0, 'name' => $this->l('All'))), BlogImageType::getTypes()),
								'id' => BlogImageType::getIdTable(),
								'name' => 'name'
								)
							),
							array(
								'type' => 'switch',
								'label' => $this->l('Continue generate'),
								'name' => 'continue',
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
								'is_bool' => false,
								'desc' => $this->l('Enable/Disable continue')
							)
						),
						'submit' => array(
							'title' => $this->l('Generate Images'),
							'icon' => 'process-icon- icon-picture'
						)
					)
			)
		);

		$helper = new HelperForm();
		$helper->submit_action = 'submitSaveGenerateImage';
		$helper->bootstrap = true;
		$helper->fields_value = array(
			BlogImageType::getIdTable() => 0,
			'continue' => false
		);
		$helper->show_toolbar = true;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminBlogImageType', false);
		$helper->token = Tools::getAdminTokenLite('AdminBlogImageType');
		$helper->title = $this->l('Generate images');
		$helper->override_folder = 'generate_images/';
		$form_generate_images = $helper->generateForm($fields_generate_images);
		$this->tpl_list_vars['form_generate_images'] = $form_generate_images;
		return parent::renderList();
	}
} 