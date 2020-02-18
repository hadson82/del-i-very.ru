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

/**
 * Class MailModBlog
 */
class MailModBlog
{
	public function getMailTemplatePath()
	{
		return _PS_MODULE_DIR_.ToolsModuleDGC::getModNameForPath(__FILE__).'/mails/';
	}

	public function sendMail($template, $email_to, $theme, $template_vars = array())
	{
		$context = Context::getContext();
		$this->checkAndFixEmailTemplateForLang($context->language, $template);
		Mail::Send($context->language->id, $template, $theme,
			$template_vars,
			$email_to,
			null,
			Configuration::get('PS_SHOP_EMAIL'),
			Configuration::get('PS_SHOP_NAME'),
			null,
			null,
			$this->getMailTemplatePath()
		);
	}

	public function fixEmailTemplateForLang($lang, $template_filename)
	{
		if (!file_exists($template_path = $this->getMailTemplatePath().$lang->iso_code))
			mkdir($template_path = $this->getMailTemplatePath().$lang->iso_code);
		$default_template_path = $this->getMailTemplatePath().'en/';
		$template_path = $this->getMailTemplatePath().$lang->iso_code.'/'.$template_filename;
		if (file_exists($default_template_path.$template_filename))
			call_user_func_array('copy', array(
				$default_template_path.$template_filename,
				$template_path
			));
	}

	public function checkAndFixEmailTemplateForLang($lang, $template)
	{
		$template_path = $this->getMailTemplatePath().$lang->iso_code.'/'.$template;
		if (!file_exists($template_path.'.txt'))
			$this->fixEmailTemplateForLang($lang, $template.'.txt');
		if (!file_exists($template_path.'.html'))
			$this->fixEmailTemplateForLang($lang, $template.'.html');
	}
}