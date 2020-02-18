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

class DBlogAPIModuleFrontController extends BlogModuleFrontController
{
	public function convertCommentToTpl($comments, $ajax = true)
	{
		$tpl = array();
		$tpl['comments'] = '';
		$tpl['children'] = array();

		$this->context->smarty->assign(array(
			'limit_view' => BlogConf::getConf('LIMIT_COMMENTS'),
			'ajax' => $ajax
		));

		foreach ($comments as $key => $comment)
		{
			if (!isset($comment[BlogCommentArticle::getIdTable()]))
			{
				if (!isset($tpl['children']['children_'.$key]))
					$tpl['children']['children_'.$key] = '';

				$this->context->smarty->assign('child', true);
				if (is_array($comment['children']) && count($comment['children']))
				{
					$comment['children'] = array_reverse($comment['children']);
					foreach ($comment['children'] as $item)
					{
						$this->context->smarty->assign('comment', $item);
						$tpl['children']['children_'.$key] .= BlogTools::fetchTemplate('front/comment.tpl');
					}
				}
			}
			else
			{
				$this->context->smarty->assign('child', false);
				$this->context->smarty->assign('comment', $comment);
				$tpl['comments'] .= BlogTools::fetchTemplate('front/comment.tpl');
			}
		}
		return $tpl;
	}

	public function checkAndAuthCustomer($email, $firstname)
	{
		$errors = array();
		$customer = new Customer();

		if (empty($firstname))
			$errors[] = $this->module->l('Firstname empty', 'api');
		if (!empty($firstname) && !Validate::isName($firstname))
			$errors[] = $this->module->l('Firstname wrong', 'api');

		if (empty($email))
			$errors[] = $this->module->l('Email empty', 'api');
		if (!empty($email) && !Validate::isEmail($email))
			$errors[] = $this->module->l('Email wrong', 'api');

		if (!empty($email) && Validate::isEmail($email))
		{
			$customer->getByEmail($email);
			if (Validate::isLoadedObject($customer))
				$errors[] = $this->module->l('Customer already exists', 'api');
		}

		if (!count($errors))
		{
			$password = Tools::passwdGen();
			$customer->firstname = $firstname;
			$customer->lastname = ' ';
			$customer->email = $email;
			$customer->optin = 1;
			$customer->newsletter = 1;
			$customer->passwd = Tools::encrypt($password);
			$customer->save();

			$guest_avatar = BlogCommentArticle::getAvatarPath().BlogCommentArticle::getGuestAvatar();
			$customer_avatar = BlogCommentArticle::getAvatarPath().BlogCommentArticle::getCustomerAvatar($customer->id);

			if (file_exists($guest_avatar))
				rename($guest_avatar, $customer_avatar);

			$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ?
				$this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
			$this->context->cookie->id_customer = $customer->id;
			$this->context->cookie->customer_lastname = $customer->lastname;
			$this->context->cookie->customer_firstname = $customer->firstname;
			$this->context->cookie->logged = 1;
			$customer->logged = 1;
			$this->context->cookie->is_guest = $customer->isGuest();
			$this->context->cookie->passwd = $customer->passwd;
			$this->context->cookie->email = $customer->email;
			BlogTool::sendMail(array(
				'{firstname}' => $firstname,
				'{lastname}' => ' ',
				'{email}' => $email,
				'{password}' => $password
			), $this->module->l('New customer', 'api'), 'new_customer', $email);
		}
		else
			return $errors;

		return $errors;
	}

	public function displayAjaxUploadAvatar()
	{
		$errors = array();

		if (array_key_exists('avatar', $_FILES) && $_FILES['avatar']
		&& array_key_exists('tmp_name', $_FILES['avatar']) && $_FILES['avatar']['tmp_name'])
		{
			if (in_array(exif_imagetype($_FILES['avatar']['tmp_name']), array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF)))
			{
				$path_avatar = _PS_MODULE_DIR_.$this->module->name.'/views/img/avatar/';
				$local_path_avatar = _MODULE_DIR_.$this->module->name.'/views/img/avatar/';
				if ($this->context->cookie->logged)
					$avatar = 'customer_'.$this->context->customer->id.'.jpg';
				else
					$avatar = 'guest_'.$this->context->cookie->id_guest.'.jpg';

				BlogImageManager::resize($_FILES['avatar']['tmp_name'], $path_avatar.$avatar, 93, 93);

				die(Tools::jsonEncode(array(
					'hasError' => false,
					'avatar' => $local_path_avatar.$avatar
				)));
			}
			else
				$errors[] = $this->module->l('Image file type wrong, allow type .jpg, .png, .gif', 'api');
		}
		else
			$errors[] = $this->module->l('Image file not upload', 'api');

		die(Tools::jsonEncode(array(
			'hasError' => true,
			'errors' => $errors
		)));
	}

	public function displayAjaxAdd()
	{
		$errors = array();
		$id_article = Tools::getValue(BlogArticle::getIdTable());
		$parent_id = Tools::getValue('parent_id');
		$message = Tools::getValue('message');
		$answer_id_customer = Tools::getValue('answer_id_customer', 0);

		$create_account = false;
		if (!$this->context->cookie->logged)
		{
			$email = Tools::getValue('email');
			$firstname = Tools::getValue('firstname');
			$errors = $this->checkAndAuthCustomer($email, $firstname);

			if (!count($errors))
				$create_account = true;
		}

		if (empty($message))
			$errors[] = $this->module->l('Message empty!', 'api');

		if (!count($errors))
		{
			$errors = array();
			$article = new BlogArticle($id_article, $this->context->language->id);

			if (Validate::isLoadedObject($article))
			{
				$comment = new BlogCommentArticle();
				$comment->id_customer = $this->context->cookie->id_customer;
				$comment->{BlogArticle::getIdTable()} = $id_article;
				$comment->parent_id = $parent_id;
				$comment->message = htmlspecialchars($message);
				$comment->answer_id_customer = $answer_id_customer;
				$comment->is_active = true;
				$comment->is_moderated = ($article->is_only_verified_comments ? false : true);
				$comment->save();

				die(Tools::jsonEncode(array(
					'hasError' => false,
					'create_account' => $create_account
				)));
			}
			else
				$errors[] = $this->module->l('Article not exists');
		}

		die(Tools::jsonEncode(array(
			'hasError' => true,
			'errors' => $errors
		)));
	}

	public function displayAjaxUpdate()
	{
		$id_blog_article = Tools::getValue(BlogArticle::getIdTable());
		$article = new BlogArticle($id_blog_article, $this->context->language->id);

		if (Validate::isLoadedObject($article))
		{

			$id_last_comment = Tools::getValue('id_last_comment');

			$comments = BlogCommentArticle::getComments($id_blog_article, $article->is_only_verified_comments, true, 0, 0, $id_last_comment);

			$ids_parent = Tools::getValue('ids_parent');
			$ids_parent = explode('|', $ids_parent);
			if (!empty($ids_parent) && count($ids_parent))
			{
				foreach ($ids_parent as $item)
					$comments[$item]['children'] = BlogCommentArticle::getComments($id_blog_article,
						$article->is_only_verified_comments, true, 0, 0, $id_last_comment, (int)$item);
			}
			die(Tools::jsonEncode(array(
				'hasError' => false,
				'tpl' => $this->convertCommentToTpl($comments, $article)
			)));
		}
	}

	public function displayAjaxDelete()
	{
		$id_comment = Tools::getValue('comment');
		$comment = new BlogCommentArticle($id_comment);
		//if ($comment->id_customer == $this->context->cookie->id_customer)
		if (in_array($this->context->cookie->id_customer, $this->customers_have_admin_rights))
		{
			$comment->deleted = 1;
			$comment->save();
			Db::getInstance()->update(BlogCommentArticle::getTable(), array(
				'deleted' => 1
			), ' parent_id = '.(int)$comment->id);
		}

		die(Tools::jsonEncode(array(
			'message' => '<div class="comment_repair"><a onclick="Comment.repair(this);">'.$this->module->l('Repair comment', 'api').'</a></div>'
		)));
	}

	public function displayAjaxRepair()
	{
		$id_comment = Tools::getValue('comment');
		$comment = new BlogCommentArticle($id_comment);
		//if ($comment->id_customer == $this->context->cookie->id_customer)
		if (in_array($this->context->cookie->id_customer, $this->customers_have_admin_rights))
		{
			$comment->deleted = 0;
			$comment->save();
			Db::getInstance()->update(BlogCommentArticle::getTable(), array(
				'deleted' => 0
			), ' parent_id = '.(int)$comment->id);
		}
	}

	public function displayAjaxGetComment()
	{
		$offset = (int)Tools::getValue('offset');
		$id_article = (int)Tools::getValue('article');
		$parent_id = (int)Tools::getValue('parent_id');

		$article = new BlogArticle($id_article, $this->context->language->id);

		if (Validate::isLoadedObject($article))
		{
			$comments = BlogCommentArticle::getComments($article->id, $article->is_only_verified_comments, true, $offset, null, null, $parent_id);

			if ($parent_id)
				$comments = array_reverse($comments);

			die(Tools::jsonEncode(array(
				'tpl' => $this->convertCommentToTpl($comments, false),
				'nb_comment' => count($comments)
			)));
		}
	}

	public function displayAjaxLogin()
	{
		$email = Tools::getValue('email');
		$passwd = Tools::getValue('passwd');

		$errors = array();

		if (empty($email))
			$errors[] = $this->module->l('An email address required.', 'dblog');

		if (!empty($email) && !Validate::isEmail($email))
			$errors[] = $this->module->l('Invalid email address.', 'dblog');

		if (empty($passwd))
			$errors[] = $this->module->l('Password is required.', 'dblog');

		if (!empty($passwd) && !Validate::isPasswd($passwd))
			$errors[] = $this->module->l('Invalid password.', 'dblog');

		if (!count($errors))
		{
			$customer = new Customer();
			$authentication = $customer->getByEmail(trim($email), trim($passwd));

			if (isset($authentication->active) && !$authentication->active)
				$errors[] = $this->module->l('Your account isn\'t available at this time, please contact us', 'dblog');
			elseif (!$authentication || !$customer->id)
				$errors[] = $this->module->l('Authentication failed.', 'dblog');
			else
			{
				$guest_avatar = BlogCommentArticle::getAvatarPath().BlogCommentArticle::getGuestAvatar();
				$customer_avatar = BlogCommentArticle::getAvatarPath().BlogCommentArticle::getCustomerAvatar($customer->id);

				if (file_exists($guest_avatar))
				{
					if (file_exists($customer_avatar))
						unlink($customer_avatar);

					rename($guest_avatar, $customer_avatar);
				}

				$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ?
					$this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
				$this->context->cookie->id_customer = $customer->id;
				$this->context->cookie->customer_lastname = $customer->lastname;
				$this->context->cookie->customer_firstname = $customer->firstname;
				$this->context->cookie->logged = 1;
				$customer->logged = 1;
				$this->context->cookie->is_guest = $customer->isGuest();
				$this->context->cookie->passwd = $customer->passwd;
				$this->context->cookie->email = $customer->email;
			}
		}

		die(Tools::jsonEncode(array(
			'hasError' => (count($errors) ? true : false),
			'errors' => $errors,
			'success' => $this->module->l('Auth successfully!', 'api'),
			'path_avatar' => (isset($customer_avatar) ? BlogCommentArticle::getLocalAvatarPath().BlogCommentArticle::getCustomerAvatar($customer->id) : false)
		)));
	}
} 