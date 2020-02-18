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
	//extra definitions
	Inputmask.extendDefinitions({
		"A": {
			validator: "[A-Za-z\u0410-\u044F\u0401\u0451\u00C0-\u00FF\u00B5]",
			cardinality: 1,
			casing: "upper" //auto uppercasing
		},
		"&": { //alfanumeric uppercasing
			validator: "[0-9A-Za-z\u0410-\u044F\u0401\u0451\u00C0-\u00FF\u00B5]",
			cardinality: 1,
			casing: "upper"
		},
		"#": { //hexadecimal
			validator: "[0-9A-Fa-f]",
			cardinality: 1,
			casing: "upper"
		}
	});
	Inputmask.extendAliases({
		"url": {
			definitions: {
				"i": {
					validator: ".",
					cardinality: 1
				}
			},
			mask: "(\\http://)|(\\http\\s://)|(ftp://)|(ftp\\s://)i{+}",
			insertMode: false,
			autoUnmask: false,
			inputmode: "url",
		},
		"ip": { //ip-address mask
			mask: "i[i[i]].i[i[i]].i[i[i]].i[i[i]]",
			definitions: {
				"i": {
					validator: function (chrs, maskset, pos, strict, opts) {
						if (pos - 1 > -1 && maskset.buffer[pos - 1] !== ".") {
							chrs = maskset.buffer[pos - 1] + chrs;
							if (pos - 2 > -1 && maskset.buffer[pos - 2] !== ".") {
								chrs = maskset.buffer[pos - 2] + chrs;
							} else chrs = "0" + chrs;
						} else chrs = "00" + chrs;
						return new RegExp("25[0-5]|2[0-4][0-9]|[01][0-9][0-9]").test(chrs);
					},
					cardinality: 1
				}
			},
			onUnMask: function (maskedValue, unmaskedValue, opts) {
				return maskedValue;
			},
			inputmode: "numeric",
		},
		"email": {
			//https://en.wikipedia.org/wiki/Domain_name#Domain_name_space
			//https://en.wikipedia.org/wiki/Hostname#Restrictions_on_valid_host_names
			//should be extended with the toplevel domains at the end
			mask: "*{1,64}[.*{1,64}][.*{1,64}][.*{1,63}]@-{1,63}.-{1,63}[.-{1,63}][.-{1,63}]",
			greedy: false,
			onBeforePaste: function (pastedValue, opts) {
				pastedValue = pastedValue.toLowerCase();
				return pastedValue.replace("mailto:", "");
			},
			definitions: {
				"*": {
					validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~\-]",
					cardinality: 1,
					casing: "lower"
				},
				"-": {
					validator: "[0-9A-Za-z\-]",
					cardinality: 1,
					casing: "lower"
				}
			},
			onUnMask: function (maskedValue, unmaskedValue, opts) {
				return maskedValue;
			},
			inputmode: "email",
		},
		"mac": {
			mask: "##:##:##:##:##:##"
		},
		//https://en.wikipedia.org/wiki/Vehicle_identification_number
		// see issue #1199
		"vin": {
			mask: "V{13}9{4}",
			definitions: {
				'V': {
					validator: "[A-HJ-NPR-Za-hj-npr-z\\d]",
					cardinality: 1,
					casing: "upper"
				}
			},
			clearIncomplete: true,
			autoUnmask: true
		}
	});
	return Inputmask;
}));
