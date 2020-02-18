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

class BlogImageManager
{
	public static function resize($src_file, $dst_file, $dst_width = null, $dst_height = null, $file_type = 'jpg',
						$force_type = false, &$error = 0, &$tgt_width = null, &$tgt_height = null, $quality = 5,
						&$src_width = null, &$src_height = null)
	{
		if ((int)BlogConf::getConf('USE_SMART_CROP_IMAGE') && !is_null($dst_width) && !is_null($dst_height))
		{
			if (!file_exists($src_file))
				return false;

			$image = new GD($src_file);
			$image->setOptions(array(
				'resizeUp' => (int)BlogConf::getConf('SMART_CROP_IMAGE_RESIZE_UP')
			));
			$thumbnail = $image->adaptiveResize($dst_width, $dst_height);

			list($tmp_width, $tmp_height, $type) = getimagesize($src_file);
			unset($tmp_width, $tmp_height);
			if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
				|| (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$force_type)
				$file_type = 'png';

			if (file_exists($dst_file))
				unlink($dst_file);

			$thumbnail->save($dst_file, Tools::strtoupper($file_type));
		}
		else
		{
			if (file_exists($dst_file))
				unlink($dst_file);

			return ImageManager::resize($src_file, $dst_file, $dst_width, $dst_height, $file_type, $force_type,
				$error, $tgt_width, $tgt_height, $quality, $src_width, $src_height);
		}
	}
}