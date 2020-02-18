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

class BlogDB
{
	public static function getPrefixTable($table)
	{
		return _DB_PREFIX_.$table;
	}

	public static function getPrefixTableLang($table)
	{
		return self::getPrefixTable($table).'_lang';
	}

	public static function getPrefixTableShop($table)
	{
		return self::getPrefixTable($table).'_shop';
	}

	public static function getTable($table)
	{
		return $table;
	}

	public static function getTableLang($table)
	{
		return self::getTable($table).'_lang';
	}

	public static function getTableShop($table)
	{
		return self::getTable($table).'_shop';
	}
}