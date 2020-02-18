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
 * Class BlogTool
 */
class BlogTool
{
	public function dateFormatTranslate($date, $format = null)
	{
		if (is_null($format))
			$format = BlogConf::getConf('DATE_FORMAT');
		$l = BlogTranslate::getInstance();
		$months = explode('|', 'January|February|March|April|May|June|July|August|September|October|November|December');
		$mons = explode('|', 'Jan|Feb|Mar|Apr|May|June|July|Aug|Sept|Oct|Nov|Dec');
		$weekdays = explode('|', 'Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday');
		$weeks = explode('|', 'Mon|Tue|Wed|Thu|Fri|Sat|Sun');
		$date_data = array_merge($months, $mons, $weekdays, $weeks);

		$date = date($format, strtotime($date));
		$date = str_replace($date_data, array_map(array($l, 'ld'), $date_data), $date);
		return $date;
	}

	public static function sendMail($vars, $subject, $template, $to, $to_name = null, $from = null, $from_name = null, $files = null)
	{
		$context = Context::getContext();
		$path_mail = _PS_MODULE_DIR_._MODULE_NAME_.'/mails/';
		if (!file_exists($path_mail.$context->language->iso_code.'/'.$template.'.html'))
		{
			mkdir($path_mail.$context->language->iso_code.'/', 0755);
			copy($path_mail.'en/'.$template.'.html', $path_mail.$context->language->iso_code.'/'.$template.'.html');
			copy($path_mail.'en/'.$template.'.txt', $path_mail.$context->language->iso_code.'/'.$template.'.txt');
		}
		return Mail::send($context->language->id, $template, $subject, $vars, $to, $to_name, $from, $from_name, $files, null, $path_mail);
	}

	public static function registerBlogSmartyFunction()
	{
		if (!array_key_exists('customerAvatar', Context::getContext()->smarty->registered_plugins['function']))
			smartyRegisterFunction(Context::getContext()->smarty, 'function', 'customerAvatar', array('BlogCommentArticle', 'smartyGetPathAvatarByCustomer'));

		if (!array_key_exists('linkWrapper', Context::getContext()->smarty->registered_plugins['modifier']))
			smartyRegisterFunction(Context::getContext()->smarty, 'modifier', 'linkWrapper', array('BlogCommentArticle', 'smartyLinkWrapper'));
	}
} 