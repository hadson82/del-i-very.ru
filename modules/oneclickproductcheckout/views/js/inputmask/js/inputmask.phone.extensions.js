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
		define(["inputmask.dependencyLib", "inputmask"], factory);
	} else if (typeof exports === "object") {
		module.exports = factory(require("./inputmask.dependencyLib"), require("./inputmask"));
	} else {
		factory(window.dependencyLib || jQuery, window.Inputmask);
	}
}
(function ($, Inputmask) {
	Inputmask.extendAliases({
		"abstractphone": {
			countrycode: "",
			phoneCodes: [],
			mask: function (opts) {
				opts.definitions = {"#": opts.definitions["9"]};
				var masks = opts.phoneCodes.sort(function (a, b) {
					var maska = (a.mask || a).replace(/#/g, "9").replace(/[\+\(\)#-]/g, ""),
						maskb = (b.mask || b).replace(/#/g, "9").replace(/[\+\(\)#-]/g, ""),
						maskas = (a.mask || a).split("#")[0],
						maskbs = (b.mask || b).split("#")[0];

					return maskbs.indexOf(maskas) === 0 ? -1 : (maskas.indexOf(maskbs) === 0 ? 1 : maska.localeCompare(maskb));
				});
				//console.log(JSON.stringify(masks));
				return masks;
			},
			keepStatic: true,
			onBeforeMask: function (value, opts) {
				var processedValue = value.replace(/^0{1,2}/, "").replace(/[\s]/g, "");
				if (processedValue.indexOf(opts.countrycode) > 1 || processedValue.indexOf(opts.countrycode) === -1) {
					processedValue = "+" + opts.countrycode + processedValue;
				}

				return processedValue;
			},
			onUnMask: function (maskedValue, unmaskedValue, opts) {
				//implement me
				return unmaskedValue;
			},
			inputmode: "tel",
		}
	});
	return Inputmask;
}));
