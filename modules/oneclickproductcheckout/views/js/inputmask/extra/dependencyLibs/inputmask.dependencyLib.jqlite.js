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
		define("inputmask.dependencyLib", ["jqlite"], factory);
	} else if (typeof exports === "object") {
		module.exports = factory(require("jqlite"));
	} else {
		factory(jqlite);
	}
}
(function ($) {
	// Use a stripped-down indexOf as it's faster than native
	// http://jsperf.com/thor-indexof-vs-for/5
	function indexOf(list, elem) {
		var i = 0,
			len = list.length;
		for (; i < len; i++) {
			if (list[i] === elem) {
				return i;
			}
		}
		return -1;
	}

	var class2type = {},
		classTypes = "Boolean Number String Function Array Date RegExp Object Error".split(" ");
	for (var nameNdx = 0; nameNdx < classTypes.length; nameNdx++) {
		class2type["[object " + classTypes[nameNdx] + "]"] = classTypes[nameNdx].toLowerCase();
	}

	function type(obj) {
		if (obj == null) {
			return obj + "";
		}
		// Support: Android<4.0, iOS<6 (functionish RegExp)
		return typeof obj === "object" || typeof obj === "function" ?
		class2type[class2type.toString.call(obj)] || "object" :
			typeof obj;
	}

	function isWindow(obj) {
		return obj != null && obj === obj.window;
	}

	function isArraylike(obj) {
		// Support: iOS 8.2 (not reproducible in simulator)
		// `in` check used to prevent JIT error (gh-2145)
		// hasOwn isn't used here due to false negatives
		// regarding Nodelist length in IE
		var length = "length" in obj && obj.length,
			ltype = type(obj);

		if (ltype === "function" || isWindow(obj)) {
			return false;
		}

		if (obj.nodeType === 1 && length) {
			return true;
		}

		return ltype === "array" || length === 0 ||
			typeof length === "number" && length > 0 && (length - 1) in obj;
	}

	$.inArray = function (elem, arr, i) {
		return arr == null ? -1 : indexOf(arr, elem, i);
	};
	$.isFunction = function (obj) {
		return type(obj) === "function";
	};
	$.isArray = Array.isArray;
	$.isPlainObject = function (obj) {
		// Not plain objects:
		// - Any object or value whose internal [[Class]] property is not "[object Object]"
		// - DOM nodes
		// - window
		if (type(obj) !== "object" || obj.nodeType || isWindow(obj)) {
			return false;
		}

		if (obj.constructor && !class2type.hasOwnProperty.call(obj.constructor.prototype, "isPrototypeOf")) {
			return false;
		}

		// If the function hasn't returned already, we're confident that
		// |obj| is a plain object, created by {} or constructed with new Object
		return true;
	};
	$.extend = function () {
		var options, name, src, copy, copyIsArray, clone,
			target = arguments[0] || {},
			i = 1,
			length = arguments.length,
			deep = false;

		// Handle a deep copy situation
		if (typeof target === "boolean") {
			deep = target;

			// Skip the boolean and the target
			target = arguments[i] || {};
			i++;
		}

		// Handle case when target is a string or something (possible in deep copy)
		if (typeof target !== "object" && !$.isFunction(target)) {
			target = {};
		}

		// Extend jQuery itself if only one argument is passed
		if (i === length) {
			target = this;
			i--;
		}

		for (; i < length; i++) {
			// Only deal with non-null/undefined values
			if ((options = arguments[i]) != null) {
				// Extend the base object
				for (name in options) {
					src = target[name];
					copy = options[name];

					// Prevent never-ending loop
					if (target === copy) {
						continue;
					}

					// Recurse if we're merging plain objects or arrays
					if (deep && copy && ($.isPlainObject(copy) || (copyIsArray = $.isArray(copy)))) {
						if (copyIsArray) {
							copyIsArray = false;
							clone = src && $.isArray(src) ? src : [];

						} else {
							clone = src && $.isPlainObject(src) ? src : {};
						}

						// Never move original objects, clone them
						target[name] = $.extend(deep, clone, copy);

						// Don't bring in undefined values
					} else if (copy !== undefined) {
						target[name] = copy;
					}
				}
			}
		}

		// Return the modified object
		return target;
	};
	$.each = function (obj, callback) {
		var value, i = 0;

		if (isArraylike(obj)) {
			for (var length = obj.length; i < length; i++) {
				value = callback.call(obj[i], i, obj[i]);
				if (value === false) {
					break;
				}
			}
		} else {
			for (i in obj) {
				value = callback.call(obj[i], i, obj[i]);
				if (value === false) {
					break;
				}
			}
		}

		return obj;
	};
	$.map = function (elems, callback) {
		var value,
			i = 0,
			length = elems.length,
			isArray = isArraylike(elems),
			ret = [];

		// Go through the array, translating each of the items to their new values
		if (isArray) {
			for (; i < length; i++) {
				value = callback(elems[i], i);

				if (value != null) {
					ret.push(value);
				}
			}

			// Go through every key on the object,
		} else {
			for (i in elems) {
				value = callback(elems[i], i);

				if (value != null) {
					ret.push(value);
				}
			}
		}

		// Flatten any nested arrays
		return [].concat(ret);
	};
	$.data = function (elem, name, data) {
		return $(elem).data(name, data);
	};
	$.Event = $.Event || function CustomEvent(event, params) {
			params = params || {
					bubbles: false,
					cancelable: false,
					detail: undefined
				};
			var evt = document.createEvent("CustomEvent");
			evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
			return evt;
		};
	$.Event.prototype = window.Event.prototype;

	window.dependencyLib = $;
	return $;
}));