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
 * @author    SeoSA <885588@bk.ru>
 * @copyright 2012-2017 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

(function (factory) {
	if (typeof define === "function" && define.amd) {
		define([
			"./inputmask.dependencyLib",
			"./inputmask",
			"./inputmask.extensions",
			"./inputmask.date.extensions",
			"./inputmask.numeric.extensions",
			"./inputmask.phone.extensions",
			"./inputmask.regex.extensions",
			"./jquery.inputmask"
		], factory);
	} else if (typeof exports === "object") {
		module.exports = factory(
			require("./inputmask.dependencyLib"),
			require("./inputmask"),
			require("./inputmask.extensions"),
			require("./inputmask.date.extensions"),
			require("./inputmask.numeric.extensions"),
			require("./inputmask.phone.extensions"),
			require("./inputmask.regex.extensions"),
			require("./jquery.inputmask")
		);
	} else {
		window.InputmaskLoader = jQuery.Deferred();
		jQuery.getScript("./js/inputmask.dependencyLib.js").done(function () {
			jQuery.getScript("./js/inputmask.js").done(function () {
				jQuery.getScript("./js/inputmask.extensions.js").done(function () {
					jQuery.getScript("./js/inputmask.date.extensions.js").done(function () {
						jQuery.getScript("./js/inputmask.numeric.extensions.js").done(function () {
							jQuery.getScript("./js/inputmask.phone.extensions.js").done(function () {
								jQuery.getScript("./js/inputmask.regex.extensions.js").done(function () {
									jQuery.getScript("./js/jquery.inputmask.js").done(function () {
										window.InputmaskLoader.resolve();
									})
								})
							})
						})
					})
				})
			})
		});
	}
}(function ($, Inputmask) {
	return Inputmask;
}));
