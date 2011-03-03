/*!
 * Piwik - Web Analytics
 *
 * JavaScript tracking client
 *
 * @link http://piwik.org
 * @source http://dev.piwik.org/trac/browser/trunk/js/piwik.js
 * @license http://www.opensource.org/licenses/bsd-license.php Simplified BSD
 */

// + patch http://dev.piwik.org/trac/changeset/3981 for SPIP integration

// Refer to README for build instructions when minifying this file for distribution.

/*
 * Browser [In]Compatibility
 * - minimum required ECMAScript: ECMA-262, edition 3
 *
 * Incompatible with these (and earlier) versions of:
 * - IE4 - try..catch and for..in introduced in IE5
 * - IE5 - named anonymous functions, array.push, encodeURIComponent, and decodeURIComponent introduced in IE5.5
 * - Firefox 1.0 and Netscape 8.x - FF1.5 adds array.indexOf, among other things
 * - Mozilla 1.7 and Netscape 6.x-7.x
 * - Netscape 4.8
 * - Opera 6 - Error object (and Presto) introduced in Opera 7
 * - Opera 7
 */

/************************************************************
 * JSON - public domain reference implementation by Douglas Crockford
 * @link http://www.JSON.org/json2.js
 ************************************************************/
/*jslint evil:true, strict:true, regexp:false, forin:true */
/*global JSON2 */
if (!this.JSON2) {
	this.JSON2 = {};
}

(function () {
    "use strict";

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return isFinite(this.valueOf()) ?
                this.getUTCFullYear()     + '-' +
                f(this.getUTCMonth() + 1) + '-' +
                f(this.getUTCDate())      + 'T' +
                f(this.getUTCHours())     + ':' +
                f(this.getUTCMinutes())   + ':' +
                f(this.getUTCSeconds())   + 'Z' : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function (key) {
                return this.valueOf();
            };
    }

    var cx = new RegExp('[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]', 'g'),
        escapable = new RegExp('[\\\\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]', 'g'),
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string' ? c :
                '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0 ? '[]' : gap ?
                    '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' :
                    '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0 ? '{}' : gap ?
                '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' :
                '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON2.stringify !== 'function') {
        JSON2.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON2.parse !== 'function') {
        JSON2.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if ((new RegExp('^[\\],:{}\\s]*$'))
                    .test(text.replace(new RegExp('\\\\(?:["\\\\/bfnrt]|u[0-9a-fA-F]{4})', 'g'), '@')
                        .replace(new RegExp('"[^"\\\\\n\r]*"|true|false|null|-?\\d+(?:\\.\\d*)?(?:[eE][+\\-]?\\d+)?', 'g'), ']')
                        .replace(new RegExp('(?:^|:|,)(?:\\s*\\[)+', 'g'), ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function' ?
                    walk({'': j}, '') : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());
/************************************************************
 * end JSON
 ************************************************************/

/*jslint browser:true, plusplus:false, onevar:false, strict:true, evil:true */
/*global window unescape ActiveXObject _paq:true */
var
	// asynchronous tracker (or proxy)
	_paq = _paq || [],

	// Piwik singleton and namespace
	Piwik =	Piwik || (function () {
		"use strict";

		/************************************************************
		 * Private data
		 ************************************************************/

		var expireDateTime,

			/* plugins */
			plugins = {},

			/* alias frequently used globals for added minification */
			documentAlias = document,
			navigatorAlias = navigator,
			screenAlias = screen,
			windowAlias = window,

			/* DOM Ready */
			hasLoaded = false,
			registeredOnLoadHandlers = [],

			/* encode */
			encodeWrapper = windowAlias.encodeURIComponent,

			/* decode */
			decodeWrapper = windowAlias.decodeURIComponent,

			/* asynchronous tracker */
			asyncTracker,

			/* iterator */
			i;

		/************************************************************
		 * Private methods
		 ************************************************************/

		/*
		 * Is property defined?
		 */
		function isDefined(property) {
			return typeof property !== 'undefined';
		}

		/*
		 * Is property a function?
		 */
		function isFunction(property) {
			return typeof property === 'function';
		}

		/*
		 * Is property an object?
		 *
		 * @return bool Returns true if property is null, an Object, or subclass of Object (i.e., an instanceof String, Date, etc.)
		 */
		function isObject(property) {
			return typeof property === 'object';
		}

		/*
		 * Is property a string?
		 */
		function isString(property) {
			return typeof property === 'string' || property instanceof String;
		}

		/*
		 * apply wrapper
		 *
		 * @param array parameterArray An array comprising either:
		 *      [ 'methodName', optional_parameters ]
		 * or:
		 *      [ functionObject, optional_parameters ]
		 */
		function apply(parameterArray) {
			var f = parameterArray.shift();

			if (isString(f)) {
				asyncTracker[f].apply(asyncTracker, parameterArray);
			} else {
				f.apply(asyncTracker, parameterArray);
			}
		}

		/*
		 * Cross-browser helper function to add event handler
		 */
		function addEventListener(element, eventType, eventHandler, useCapture) {
			if (element.addEventListener) {
				element.addEventListener(eventType, eventHandler, useCapture);
				return true;
			}
			if (element.attachEvent) {
				return element.attachEvent('on' + eventType, eventHandler);
			}
			element['on' + eventType] = eventHandler;
		}

		/*
		 * Call plugin hook methods
		 */
		function executePluginMethod(methodName, callback) {
			var result = '',
				i,
				pluginMethod;

			for (i in plugins) {
				pluginMethod = plugins[i][methodName];
				if (isFunction(pluginMethod)) {
					result += pluginMethod(callback);
				}
			}

			return result;
		}

		/*
		 * Handle beforeunload event
		 *
		 * Subject to Safari's "Runaway JavaScript Timer" and
		 * Chrome V8 extension that terminates JS that exhibits
		 * "slow unload", i.e., calling getTime() > 1000 times
		 */
		function beforeUnloadHandler() {
			var now;

			executePluginMethod('unload');

			/*
			 * Delay/pause (blocks UI)
			 */
			if (expireDateTime) {
				// the things we do for backwards compatibility...
				// in ECMA-262 5th ed., we could simply use:
				//     while (Date.now() < expireDateTime) { }
				do {
					now = new Date();
				} while (now.getTime() < expireDateTime);
			}
		}

		/*
		 * Handler for onload event
		 */
		function loadHandler() {
			var i;

			if (!hasLoaded) {
				hasLoaded = true;
				executePluginMethod('load');
				for (i = 0; i < registeredOnLoadHandlers.length; i++) {
					registeredOnLoadHandlers[i]();
				}
			}
			return true;
		}

		/*
		 * Add onload or DOM ready handler
		 */
		function addReadyListener() {
			if (documentAlias.addEventListener) {
				addEventListener(documentAlias, 'DOMContentLoaded', function ready() {
					documentAlias.removeEventListener('DOMContentLoaded', ready, false);
					loadHandler();
				});
			} else if (documentAlias.attachEvent) {
				documentAlias.attachEvent('onreadystatechange', function ready() {
					if (documentAlias.readyState === 'complete') {
						documentAlias.detachEvent('onreadystatechange', ready);
						loadHandler();
					}
				});

				if (documentAlias.documentElement.doScroll && windowAlias === windowAlias.top) {
					(function ready() {
						if (!hasLoaded) {
							try {
								documentAlias.documentElement.doScroll('left');
							} catch (error) {
								setTimeout(ready, 0);
								return;
							}
							loadHandler();
						}
					}());
				}
			}

			// sniff for older WebKit versions
			if ((new RegExp('WebKit')).test(navigatorAlias.userAgent)) {
				var _timer = setInterval(function () {
					if (hasLoaded || /loaded|complete/.test(documentAlias.readyState)) {
						clearInterval(_timer);
						loadHandler();
					}
				}, 10);
			}

			// fallback
			addEventListener(windowAlias, 'load', loadHandler, false);
		}

		/*
		 * Get page referrer
		 */
		function getReferrer() {
			var referrer = '';

			try {
				referrer = windowAlias.top.document.referrer;
			} catch (e) {
				if (windowAlias.parent) {
					try {
						referrer = windowAlias.parent.document.referrer;
					} catch (e2) {
						referrer = '';
					}
				}
			}
			if (referrer === '') {
				referrer = documentAlias.referrer;
			}

			return referrer;
		}

		/*
		 * Extract hostname from URL
		 */
		function getHostName(url) {
			// scheme : // [username [: password] @] hostame [: port] [/ [path] [? query] [# fragment]]
			var e = new RegExp('^(?:(?:https?|ftp):)/*(?:[^@]+@)?([^:/#]+)'),
				matches = e.exec(url);

			return matches ? matches[1] : url;
		}

		/*
		 * Extract parameter from URL
		 */
		function getParameter(url, name) {
			// scheme : // [username [: password] @] hostame [: port] [/ [path] [? query] [# fragment]]
			var e = new RegExp('^(?:https?|ftp)(?::/*(?:[^?]+)[?])([^#]+)'),
				matches = e.exec(url),
				f = new RegExp('(?:^|&)' + name + '=([^&]*)'),
				result = matches ? f.exec(matches[1]) : 0;

			return result ? decodeWrapper(result[1]) : '';
		}

		/*
		 * Set cookie value
		 */
		function setCookie(cookieName, value, msToExpire, path, domain, secure) {
			var expiryDate;

			// relative time to expire in milliseconds
			if (msToExpire) {
				expiryDate = new Date();
				expiryDate.setTime(expiryDate.getTime() + msToExpire);
			}

			documentAlias.cookie = cookieName + '=' + encodeWrapper(value) +
				(msToExpire ? ';expires=' + expiryDate.toGMTString() : '') +
				';path=' + (path ? path : '/') +
				(domain ? ';domain=' + domain : '') +
				(secure ? ';secure' : '');
		}

		/*
		 * Get cookie value
		 */
		function getCookie(cookieName) {
			var cookiePattern = new RegExp('(^|;)[ ]*' + cookieName + '=([^;]*)'),

				cookieMatch = cookiePattern.exec(documentAlias.cookie);

			return cookieMatch ? decodeWrapper(cookieMatch[2]) : 0;
		}

		/*
		 * UTF-8 encoding
		 */
		function utf8_encode(argString) {
			return unescape(encodeWrapper(argString));
		}

		/************************************************************
		 * sha1
		 * - based on sha1 from http://phpjs.org/functions/sha1:512 (MIT / GPL v2)
		 ************************************************************/
		function sha1(str) {
			// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
			// + namespaced by: Michael White (http://getsprink.com)
			// +      input by: Brett Zamir (http://brett-zamir.me)
			// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			// +   jslinted by: Anthon Pang (http://piwik.org)

			var
				rotate_left = function (n, s) {
					return (n << s) | (n >>> (32 - s));
				},

				cvt_hex = function (val) {
					var str = '',
						i,
						v;

					for (i = 7; i >= 0; i--) {
						v = (val >>> (i * 4)) & 0x0f;
						str += v.toString(16);
					}
					return str;
				},

				blockstart,
				i,
				j,
				W = [],
				H0 = 0x67452301,
				H1 = 0xEFCDAB89,
				H2 = 0x98BADCFE,
				H3 = 0x10325476,
				H4 = 0xC3D2E1F0,
				A,
				B,
				C,
				D,
				E,
				temp,
				str_len,
				word_array = [];

			str = utf8_encode(str);
			str_len = str.length;

			for (i = 0; i < str_len - 3; i += 4) {
				j = str.charCodeAt(i) << 24 | str.charCodeAt(i + 1) << 16 |
					str.charCodeAt(i + 2) << 8 | str.charCodeAt(i + 3);
				word_array.push(j);
			}

			switch (str_len & 3) {
			case 0:
				i = 0x080000000;
				break;
			case 1:
				i = str.charCodeAt(str_len - 1) << 24 | 0x0800000;
				break;
			case 2:
				i = str.charCodeAt(str_len - 2) << 24 | str.charCodeAt(str_len - 1) << 16 | 0x08000;
				break;
			case 3:
				i = str.charCodeAt(str_len - 3) << 24 | str.charCodeAt(str_len - 2) << 16 | str.charCodeAt(str_len - 1) << 8 | 0x80;
				break;
			}

			word_array.push(i);

			while ((word_array.length & 15) !== 14) {
				word_array.push(0);
			}

			word_array.push(str_len >>> 29);
			word_array.push((str_len << 3) & 0x0ffffffff);

			for (blockstart = 0; blockstart < word_array.length; blockstart += 16) {
				for (i = 0; i < 16; i++) {
					W[i] = word_array[blockstart + i];
				}

				for (i = 16; i <= 79; i++) {
					W[i] = rotate_left(W[i - 3] ^ W[i - 8] ^ W[i - 14] ^ W[i - 16], 1);
				}

				A = H0;
				B = H1;
				C = H2;
				D = H3;
				E = H4;

				for (i = 0; i <= 19; i++) {
					temp = (rotate_left(A, 5) + ((B & C) | (~B & D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B, 30);
					B = A;
					A = temp;
				}

				for (i = 20; i <= 39; i++) {
					temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B, 30);
					B = A;
					A = temp;
				}

				for (i = 40; i <= 59; i++) {
					temp = (rotate_left(A, 5) + ((B & C) | (B & D) | (C & D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B, 30);
					B = A;
					A = temp;
				}

				for (i = 60; i <= 79; i++) {
					temp = (rotate_left(A, 5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B, 30);
					B = A;
					A = temp;
				}

				H0 = (H0 + A) & 0x0ffffffff;
				H1 = (H1 + B) & 0x0ffffffff;
				H2 = (H2 + C) & 0x0ffffffff;
				H3 = (H3 + D) & 0x0ffffffff;
				H4 = (H4 + E) & 0x0ffffffff;
			}

			temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);
			return temp.toLowerCase();
		}
		/************************************************************
		 * end sha1
		 ************************************************************/

		/*
		 * Fix-up URL when page rendered from search engine cache or translated page
		 */
		function urlFixup(hostName, href, referrer) {
			if (hostName === 'translate.googleusercontent.com') {		// Google
				if (referrer === '') {
					referrer = href;
				}
				href = getParameter(href, 'u');
				hostName = getHostName(href);
			} else if (hostName === 'cc.bingj.com' ||					// Bing
					hostName === 'webcache.googleusercontent.com' ||	// Google
					hostName.slice(0, 5) === '74.6.') {					// Yahoo (via Inktomi 74.6.0.0/16)
				href = documentAlias.links[0].href;
				hostName = getHostName(href);
			}
			return [hostName, href, referrer];
		}

		/*
		 * Fix-up domain
		 */
		function domainFixup(domain) {
			var dl = domain.length;

			// remove trailing '.'
			if (domain.charAt(--dl) === '.') {
				domain = domain.slice(0, dl);
			}
			// remove leading '*'
			if (domain.slice(0, 2) === '*.') {
				domain = domain.slice(1);
			}
			return domain;
		}

		/*
		 * Piwik Tracker class
		 *
		 * trackerUrl and trackerSiteId are optional arguments to the constructor
		 *
		 * See: Tracker.setTrackerUrl() and Tracker.setSiteId()
		 */
		function Tracker(trackerUrl, siteId) {

			/************************************************************
			 * Private members
			 ************************************************************/

			var
/*<DEBUG>*/
				/*
				 * registered test hooks
				 */
				registeredHooks = {},
/*</DEBUG>*/

				// Current URL and Referrer URL
				locationArray = urlFixup(documentAlias.domain, windowAlias.location.href, getReferrer()),
				domainAlias = domainFixup(locationArray[0]),
				locationHrefAlias = locationArray[1],
				configReferrerUrl = locationArray[2],

				// Request method (GET or POST)
				configRequestMethod = 'GET',

				// Tracker URL
				configTrackerUrl = trackerUrl || '',

				// Site ID
				configTrackerSiteId = siteId || '',

				// Document URL
				configCustomUrl,

				// Document title
				configTitle = documentAlias.title,

				// Extensions to be treated as download links
				configDownloadExtensions = '7z|aac|ar[cj]|as[fx]|avi|bin|csv|deb|dmg|doc|exe|flv|gif|gz|gzip|hqx|jar|jpe?g|js|mp(2|3|4|e?g)|mov(ie)?|ms[ip]|od[bfgpst]|og[gv]|pdf|phps|png|ppt|qtm?|ra[mr]?|rpm|sea|sit|tar|t?bz2?|tgz|torrent|txt|wav|wm[av]|wpd||xls|xml|z|zip',

				// Hosts or alias(es) to not treat as outlinks
				configHostsAlias = [domainAlias],

				// HTML anchor element classes to not track
				configIgnoreClasses = [],

				// HTML anchor element classes to treat as downloads
				configDownloadClasses = [],

				// HTML anchor element classes to treat at outlinks
				configLinkClasses = [],

				// Maximum delay to wait for web bug image to be fetched (in milliseconds)
				configTrackerPause = 500,

				// Minimum visit time after initial page view (in milliseconds)
				configMinimumVisitTime,

				// Recurring heart beat after initial ping (in milliseconds)
				configHeartBeatTimer,

				// Disallow hash tags in URL
				configDiscardHashTag,

				// Custom data
				configCustomData,

				// First-party cookie name prefix
				configCookieNamePrefix = '_pk_',

				// First-party cookie domain
				// User agent defaults to origin hostname
				configCookieDomain,

				// First-party cookie path
				// Default is user agent defined.
				configCookiePath,

				// Do Not Track
				configDoNotTrack,

				// Do we attribute the conversion to the first referrer or the most recent referrer?
				configConversionAttributionFirstReferrer,

				// Life of the visitor cookie (in milliseconds)
				configVisitorCookieTimeout = 63072000000, // 2 years

				// Life of the session cookie (in milliseconds)
				configSessionCookieTimeout = 1800000, // 30 minutes

				// Life of the referral cookie (in milliseconds)
				configReferralCookieTimeout = 15768000000, // 6 months

				// Custom Variables read from cookie
				customVariables = false,

				// Custom Variables names and values are each truncated before being sent in the request or recorded in the cookie
				customVariableMaximumLength = 100,

				// Browser features  via client-side data collection
				browserFeatures = {},

				// Guard against installing the link tracker more than once per Tracker instance
				linkTrackingInstalled = false,

				// Guard against installing the activity tracker more than once per Tracker instance
				activityTrackingInstalled = false,

				// Last activity timestamp
				lastActivityTime,

				// Internal state of the pseudo click handler
				lastButton,
				lastTarget,

				// Hash function
				hash = sha1,

				// Domain hash value
				domainHash;

			/*
			 * Purify URL.
			 */
			function purify(str) {
				var targetPattern;

				if (configDiscardHashTag) {
					targetPattern = new RegExp('#.*');
					return str.replace(targetPattern, '');
				}
				return str;
			}

			/*
			 * Is the host local?  (i.e., not an outlink)
			 */
			function isSiteHostName(hostName) {
				var i,
					alias,
					offset;

				for (i = 0; i < configHostsAlias.length; i++) {
					alias = domainFixup(configHostsAlias[i].toLowerCase());

					if (hostName === alias) {
						return true;
					}

					if (alias.slice(0, 1) === '.') {
						if (hostName === alias.slice(1)) {
							return true;
						}

						offset = hostName.length - alias.length;
						if ((offset > 0) && (hostName.slice(offset) === alias)) {
							return true;
						}
					}
				}
				return false;
			}

			/*
			 * Send image request to Piwik server using GET.
			 * The infamous web bug (or beacon) is a transparent, single pixel (1x1) image
			 */
			function getImage(request) {
				var image = new Image(1, 1);

				image.onLoad = function () { };
				image.src = configTrackerUrl + (configTrackerUrl.indexOf('?') < 0 ? '?' : '&') + request;
			}

			/*
			 * POST request to Piwik server using XMLHttpRequest.
			 */
			function sendXmlHttpRequest(request) {
				try {
					// we use the progid Microsoft.XMLHTTP because
					// IE5.5 included MSXML 2.5; the progid MSXML2.XMLHTTP
					// is pinned to MSXML2.XMLHTTP.3.0
					var xhr = windowAlias.XMLHttpRequest ? new windowAlias.XMLHttpRequest() :
						windowAlias.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') :
						null;

					xhr.open('POST', configTrackerUrl, true);
					xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
					// Safari: unsafe headers
//					xhr.setRequestHeader('Content-Length', request.length);
//					xhr.setRequestHeader('Connection', 'close');
					xhr.send(request);
				} catch (e) {
					// fallback
					getImage(request);
				}
			}

			/*
			 * Send request
			 */
			function sendRequest(request, delay) {
				var now = new Date();

				if (!configDoNotTrack) {
					if (configRequestMethod === 'POST') {
						sendXmlHttpRequest(request);
					} else {
						getImage(request);
					}

					expireDateTime = now.getTime() + delay;
				}
			}

			/*
			 * Get cookie name with prefix and domain hash
			 */
			function getCookieName(baseName) {
				return configCookieNamePrefix + baseName + '.' + configTrackerSiteId + '.' + domainHash;
			}

			/*
			 * Does browser have cookies enabled (for this site)?
			 */
			function hasCookies() {
				var testCookieName = getCookieName('testcookie');

				if (!isDefined(navigatorAlias.cookieEnabled)) {
					setCookie(testCookieName, '1');
					return getCookie(testCookieName) === '1' ? '1' : '0';
				}

				return navigatorAlias.cookieEnabled ? '1' : '0';
			}

			/*
			 * Update domain hash
			 */
			function updateDomainHash() {
				domainHash = hash((configCookieDomain || domainAlias) + (configCookiePath || '/')).slice(0, 4); // 4 hexits = 16 bits
			}

			/*
			 * Inits the custom variables object
			 */
			function getCustomVariablesFromCookie() {
				var cookieName = getCookieName('cvar'),
					cookie = getCookie(cookieName);

				if (cookie.length) {
					cookie = JSON2.parse(cookie);
					if (isObject(cookie)) {
						return cookie;
					}
				}
				return {};
			}

			/*
			 * Lazy loads the custom variables from the cookie, only once during this page view
			 */
			function loadCustomVariables() {
				if (customVariables === false) {
					customVariables = getCustomVariablesFromCookie();
				}
			}

			/*
			 * Process all "activity" events.
			 * For performance, this function must have low overhead.
			 */
			function activityHandler(evt) {
				var now = new Date();

				lastActivityTime = now.getTime();
			}

			/*
			 * Load visitor ID cookie
			 */
			function loadVisitorId() {
				var now = new Date(),
					nowTs = Math.round(now.getTime() / 1000),
					id = getCookie(getCookieName('id')),
					tmpContainer;

				if (id) {
					tmpContainer = id.split('.');

					// returning visitor
					tmpContainer.unshift('0');
				} else {
					tmpContainer = [
						// new visitor
						'1',

						// uuid - generate a pseudo-unique ID to fingerprint this user;
						// note: this isn't a RFC4122-compliant UUID
						hash(
							(navigatorAlias.userAgent || '') +
								(navigatorAlias.platform || '') +
								JSON2.stringify(browserFeatures) + nowTs
						).slice(0, 16), // 16 hexits = 64 bits

						// creation timestamp - seconds since Unix epoch
						nowTs,

						// visitCount - 0 = no previous visit
						0,

						// current visit timestamp
						nowTs,

						// last visit timestamp - blank = no previous visit
						''
					];
				}

				return tmpContainer;
			}

			/*
			 * Returns the URL to call piwik.php,
			 * with the standard parameters (plugins, resolution, url, referrer, etc.).
			 * Sends the pageview and browser settings with every request in case of race conditions.
			 */
			function getRequest(request, customData, pluginMethod) {
				var i,
					now = new Date(),
					nowTs = Math.round(now.getTime() / 1000),
					tmpPos,
					newVisitor,
					uuid,
					visitCount,
					createTs,
					currentVisitTs,
					lastVisitTs,
					referralTs,
					referralUrl,
					referralUrlMaxLength = 1024,
					currentReferrerHostName,
					originalReferrerHostName,
					customVariablesCopy = customVariables,
					idname = getCookieName('id'),
					sesname = getCookieName('ses'),
					refname = getCookieName('ref'),
					cvarname = getCookieName('cvar'),
					id = loadVisitorId(),
					ses = getCookie(sesname),
					ref = getCookie(refname),
					secure = documentAlias.location.protocol === 'https';

				if (configDoNotTrack) {
					setCookie(idname, '', -1, configCookiePath, configCookieDomain);
					setCookie(sesname, '', -1, configCookiePath, configCookieDomain);
					setCookie(cvarname, '', -1, configCookiePath, configCookieDomain);
					setCookie(refname, '', -1, configCookiePath, configCookieDomain);
					return '';
				}

				newVisitor = id[0];
				uuid = id[1];
				createTs = id[2];
				visitCount = id[3];
				currentVisitTs = id[4];
				lastVisitTs = id[5];

				if (ref) {
					tmpPos = ref.indexOf('.');
					referralTs = ref.slice(0, tmpPos);
					referralUrl = ref.slice(tmpPos + 1);
				} else {
					referralTs = 0;
					referralUrl = '';
				}

				if (!ses) {
					// new session (aka new visit)
					visitCount++;

					lastVisitTs = currentVisitTs;

					// Store the referrer URL and time in the cookie;
					// referral URL depends on the first or last referrer attribution
					currentReferrerHostName = getHostName(configReferrerUrl);
					originalReferrerHostName = ref ? getHostName(ref) : '';
					if (currentReferrerHostName.length && // there is a referrer
							!isSiteHostName(currentReferrerHostName) && // domain is not the current domain
							(!configConversionAttributionFirstReferrer || // attribute to last known referrer
							!originalReferrerHostName.length || // previously empty
							isSiteHostName(originalReferrerHostName))) { // previously set but in current domain
						// record this referral
						referralTs = nowTs;
						referralUrl = configReferrerUrl;

						// set the referral cookie
						setCookie(refname, referralTs + '.' + referralUrl.slice(0, referralUrlMaxLength), configReferralCookieTimeout, configCookiePath, configCookieDomain, secure);
					}
				}

				// build out the rest of the request
				request += '&idsite=' + configTrackerSiteId +
					'&rec=1' +
					'&rand=' + Math.random() +
					'&h=' + now.getHours() + '&m=' + now.getMinutes() + '&s=' + now.getSeconds() +
					'&url=' + encodeWrapper(purify(configCustomUrl || locationHrefAlias)) +
					'&urlref=' + encodeWrapper(purify(configReferrerUrl)) +
					'&_id=' + uuid + '&_idts=' + createTs + '&_idvc=' + visitCount + '&_idn=' + newVisitor +
					'&_ref=' + encodeWrapper(purify(referralUrl.slice(0, referralUrlMaxLength))) +
					'&_refts=' + referralTs +
					'&_viewts=' + lastVisitTs;

				// browser features
				for (i in browserFeatures) {
					request += '&' + i + '=' + browserFeatures[i];
				}

				// custom data
				if (customData) {
					request += '&data=' + encodeWrapper(JSON2.stringify(customData));
				} else if (configCustomData) {
					request += '&data=' + encodeWrapper(JSON2.stringify(configCustomData));
				}

				// Don't send custom variables if empty
				if (customVariables) {
					request += '&_cvar=' + encodeWrapper(JSON2.stringify(customVariables));

					// Don't save deleted custom variables in the cookie
					for (i in customVariablesCopy) {
						if (customVariables[i][0] === '' || customVariables[i][1] === '') {
							delete customVariables[i];
						}
					}

					setCookie(cvarname, JSON2.stringify(customVariables), configSessionCookieTimeout, configCookiePath, configCookieDomain, secure);
				}

				// update cookies
				setCookie(idname, uuid + '.' + createTs + '.' + visitCount + '.' + nowTs + '.' + lastVisitTs, configVisitorCookieTimeout, configCookiePath, configCookieDomain, secure);
				setCookie(sesname, '*', configSessionCookieTimeout, configCookiePath, configCookieDomain, secure);

				// tracker plugin hook
				request += executePluginMethod(pluginMethod);

				return request;
			}

			/*
			 * Log the page view / visit
			 */
			function logPageView(customTitle, customData) {
				var now = new Date(),
					request = getRequest('action_name=' + encodeWrapper(customTitle || configTitle), customData, 'log');

				sendRequest(request, configTrackerPause);

				// send ping
				if (configMinimumVisitTime && configHeartBeatTimer && !activityTrackingInstalled) {
					activityTrackingInstalled = true;

					// add event handlers; cross-browser compatibility here varies significantly
					// @see http://quirksmode.org/dom/events
					addEventListener(documentAlias, 'click', activityHandler);
					addEventListener(documentAlias, 'mouseup', activityHandler);
					addEventListener(documentAlias, 'mousedown', activityHandler);
					addEventListener(documentAlias, 'mousemove', activityHandler);
					addEventListener(documentAlias, 'mousewheel', activityHandler);
					addEventListener(windowAlias, 'DOMMouseScroll', activityHandler);
					addEventListener(windowAlias, 'scroll', activityHandler);
					addEventListener(documentAlias, 'keypress', activityHandler);
					addEventListener(documentAlias, 'keydown', activityHandler);
					addEventListener(documentAlias, 'keyup', activityHandler);
					addEventListener(windowAlias, 'resize', activityHandler);
					addEventListener(windowAlias, 'focus', activityHandler);
					addEventListener(windowAlias, 'blur', activityHandler);

					// periodic check for activity
					lastActivityTime = now.getTime();
					setTimeout(function heartBeat() {
						var now = new Date(),
							request;

						// there was activity during the heart beat period;
						// on average, this is going to overstate the visitLength by configHeartBeatTimer/2
						if ((lastActivityTime + configHeartBeatTimer) > now.getTime()) {
							// send ping if minimum visit time has elapsed
							if (configMinimumVisitTime < now.getTime()) {
								request = getRequest('ping=1', customData, 'ping');

								sendRequest(request, configTrackerPause);
							}

							// resume heart beat
							setTimeout(heartBeat, configHeartBeatTimer);
						}
						// else heart beat cancelled due to inactivity
					}, configHeartBeatTimer);
				}
			}

			/*
			 * Log the goal with the server
			 */
			function logGoal(idGoal, customRevenue, customData) {
				var request = getRequest('idgoal=' + idGoal, customData, 'goal');

				// custom revenue
				if (customRevenue) {
					request += '&revenue=' + customRevenue;
				}

				sendRequest(request, configTrackerPause);
			}

			/*
			 * Log the link or click  with the server
			 */
			function logLink(url, linkType, customData) {
				var request = getRequest(linkType + '=' + encodeWrapper(purify(url)), customData, 'link');

				sendRequest(request, configTrackerPause);
			}

			/*
			 * Construct regular expression of classes
			 */
			function getClassesRegExp(configClasses, defaultClass) {
				var i,
					classesRegExp = '(^| )(piwik[_-]' + defaultClass;

				if (configClasses) {
					for (i = 0; i < configClasses.length; i++) {
						classesRegExp += '|' + configClasses[i];
					}
				}
				classesRegExp += ')( |$)';

				return new RegExp(classesRegExp);
			}

			/*
			 * Link or Download?
			 */
			function getLinkType(className, href, isInLink) {
				// outlinks
				if (!isInLink) {
					return 'link';
				}

				// does class indicate whether it is an (explicit/forced) outlink or a download?
				var downloadPattern = getClassesRegExp(configDownloadClasses, 'download'),
					linkPattern = getClassesRegExp(configLinkClasses, 'link'),

					// does file extension indicate that it is a download?
					downloadExtensionsPattern = new RegExp('\\.(' + configDownloadExtensions + ')([?&#]|$)', 'i');

				// optimization of the if..elseif..else construct below
				return linkPattern.test(className) ? 'link' : (downloadPattern.test(className) || downloadExtensionsPattern.test(href) ? 'download' : 0);

/*
				var linkType;

				if (linkPattern.test(className)) {
					// class attribute contains 'piwik_link' (or user's override)
					linkType = 'link';
				} else if (downloadPattern.test(className)) {
					// class attribute contains 'piwik_download' (or user's override)
					linkType = 'download';
				} else if (downloadExtensionsPattern.test(sourceHref)) {
					// file extension matches a defined download extension
					linkType = 'download';
				} else {
					// otherwise none of the above
					linkType = 0;
				}

				return linkType;
 */
			}

			/*
			 * Process clicks
			 */
			function processClick(sourceElement) {
				var parentElement,
					tag,
					linkType;

				while (!!(parentElement = sourceElement.parentNode) &&
						((tag = sourceElement.tagName) !== 'A' && tag !== 'AREA')) {
					sourceElement = parentElement;
				}

				if (isDefined(sourceElement.href)) {
					// browsers, such as Safari, don't downcase hostname and href
					var originalSourceHostName = sourceElement.hostname || getHostName(sourceElement.href),
						sourceHostName = originalSourceHostName.toLowerCase(),
						sourceHref = sourceElement.href.replace(originalSourceHostName, sourceHostName),
						scriptProtocol = new RegExp('^(javascript|vbscript|jscript|mocha|livescript|ecmascript):', 'i');

					// ignore script pseudo-protocol links
					if (!scriptProtocol.test(sourceHref)) {
						// track outlinks and all downloads
						linkType = getLinkType(sourceElement.className, sourceHref, isSiteHostName(sourceHostName));
						if (linkType) {
							// This block commented out to preserve the user experience.
/*
							// WebKit/Chrome/Safari:
							// - "Failed to load resource" for onclick tracking requests where target opens in current window/tab
							if ((new RegExp('WebKit')).test(navigatorAlias.userAgent) && (new RegExp('^(_self|_top|_parent|_main|_media|_search|)$')).test(sourceElement.target) && linkType === 'link') {
								sourceElement.target = '_blank';
							}
 */
							logLink(sourceHref, linkType);
						}
					}
				}
			}

			/*
			 * Handle click event
			 */
			function clickHandler(evt) {
				var button,
					target;

				evt = evt || windowAlias.event;
				button = evt.which || evt.button;
				target = evt.target || evt.srcElement;

				// Using evt.type (added in IE4), we avoid defining separate handlers for mouseup and mousedown.
				if (evt.type === 'click') {
					if (target) {
						processClick(target);
					}
				} else if (evt.type === 'mousedown') {
					if ((button === 1 || button === 2) && target) {
						lastButton = button;
						lastTarget = target;
					} else {
						lastButton = lastTarget = null;
					}
				} else if (evt.type === 'mouseup') {
					if (button === lastButton && target === lastTarget) {
						processClick(target);
					}
					lastButton = lastTarget = null;
				}
			}

			/*
			 * Add click listener to a DOM element
			 */
			function addClickListener(element, enable) {
				if (enable) {
					// for simplicity and performance, we ignore drag events
					addEventListener(element, 'mouseup', clickHandler, false);
					addEventListener(element, 'mousedown', clickHandler, false);
				} else {
					addEventListener(element, 'click', clickHandler, false);
				}
			}

			/*
			 * Add click handlers to anchor and AREA elements, except those to be ignored
			 */
			function addClickListeners(enable) {
				if (!linkTrackingInstalled) {
					linkTrackingInstalled = true;

					// iterate through anchor elements with href and AREA elements

					var i,
						ignorePattern = getClassesRegExp(configIgnoreClasses, 'ignore'),
						linkElements = documentAlias.links;

					if (linkElements) {
						for (i = 0; i < linkElements.length; i++) {
							if (!ignorePattern.test(linkElements[i].className)) {
								addClickListener(linkElements[i], enable);
							}
						}
					}
				}
			}

			/*
			 * Browser features (plugins, resolution, cookies)
			 */
			function detectBrowserFeatures() {
				var i,
					mimeType,
					pluginMap = {
						// document types
						pdf: 'application/pdf',

						// media players
						qt: 'video/quicktime',
						realp: 'audio/x-pn-realaudio-plugin',
						wma: 'application/x-mplayer2',

						// interactive multimedia
						dir: 'application/x-director',
						fla: 'application/x-shockwave-flash',

						// RIA
						java: 'application/x-java-vm',
						gears: 'application/x-googlegears',
						ag: 'application/x-silverlight'
					};

				// general plugin detection
				if (navigatorAlias.mimeTypes && navigatorAlias.mimeTypes.length) {
					for (i in pluginMap) {
						mimeType = navigatorAlias.mimeTypes[pluginMap[i]];
						browserFeatures[i] = (mimeType && mimeType.enabledPlugin) ? '1' : '0';
					}
				}

				// Safari and Opera
				// IE6/IE7 navigator.javaEnabled can't be aliased, so test directly
				if (typeof navigator.javaEnabled !== 'unknown' &&
						isDefined(navigatorAlias.javaEnabled) &&
						navigatorAlias.javaEnabled()) {
					browserFeatures.java = '1';
				}

				// Firefox
				if (isFunction(windowAlias.GearsFactory)) {
					browserFeatures.gears = '1';
				}

				// other browser features
				browserFeatures.res = screenAlias.width + 'x' + screenAlias.height;
				browserFeatures.cookie = hasCookies();
			}

/*<DEBUG>*/
			/*
			 * Register a test hook.  Using eval() permits access to otherwise
			 * privileged membmers.
			 */
			function registerHook(hookName, userHook) {
				var hookObj = null;

				if (isString(hookName) && !isDefined(registeredHooks[hookName]) && userHook) {
					if (isObject(userHook)) {
						hookObj = userHook;
					} else if (isString(userHook)) {
						try {
							eval('hookObj =' + userHook);
						} catch (e) { }
					}

					registeredHooks[hookName] = hookObj;
				}
				return hookObj;
			}
/*</DEBUG>*/

			/************************************************************
			 * Constructor
			 ************************************************************/

			/*
			 * initialize tracker
			 */
			detectBrowserFeatures();
			updateDomainHash();

/*<DEBUG>*/
			/*
			 * initialize test plugin
			 */
			executePluginMethod('run', registerHook);
/*</DEBUG>*/

			/************************************************************
			 * Public data and methods
			 ************************************************************/

			return {
/*<DEBUG>*/
				/*
				 * Test hook accessors
				 */
				hook: registeredHooks,
				getHook: function (hookName) {
					return registeredHooks[hookName];
				},
/*</DEBUG>*/

				/**
				 * Get visitor ID (from first party cookie)
				 *
				 * @return string Visitor ID in hexits (or null, if not yet known)
				 */
				getVisitorId: function () {
					return (loadVisitorId())[1];
				},

				/**
				 * Specify the Piwik server URL
				 *
				 * @param string trackerUrl
				 */
				setTrackerUrl: function (trackerUrl) {
					configTrackerUrl = trackerUrl;
				},

				/**
				 * Specify the site ID
				 *
				 * @param int|string siteId
				 */
				setSiteId: function (siteId) {
					configTrackerSiteId = siteId;
				},

				/**
				 * Pass custom data to the server
				 *
				 * Examples:
				 *   tracker.setCustomData(object);
				 *   tracker.setCustomData(key, value);
				 *
				 * @param mixed key_or_obj
				 * @param mixed opt_value
				 */
				setCustomData: function (key_or_obj, opt_value) {
					if (isObject(key_or_obj)) {
						configCustomData = key_or_obj;
					} else {
						if (!configCustomData) {
							configCustomData = [];
						}
						configCustomData[key_or_obj] = opt_value;
					}
				},

				/**
				 * Get custom data
				 *
				 * @return mixed
				 */
				getCustomData: function () {
					return configCustomData;
				},

				/**
				 * Set custom variable to this visit
				 *
				 * @param int index
				 * @param string name
				 * @param string value
				 */
				setCustomVariable: function (index, name, value) {
					loadCustomVariables();
					if (index > 0 && index <= 5) {
						customVariables[index] = [name.slice(0, customVariableMaximumLength), value.slice(0, customVariableMaximumLength)];
					}
				},

				/**
				 * Get custom variable
				 *
				 * @param int index
				 */
				getCustomVariable: function (index) {
					var cvar;

					loadCustomVariables();
					cvar = customVariables[index];
					if (cvar && cvar[0] === '') {
						return;
					}
					return customVariables[index];
				},

				/**
				 * Delete custom variable
				 *
				 * @param int index
				 */
				deleteCustomVariable: function (index) {
					// Only delete if it was there already
					if (this.getCustomVariable(index)) {
						this.setCustomVariable(index, '', '');
					}
				},

				/**
				 * Set delay for link tracking (in milliseconds)
				 *
				 * @param int delay
				 */
				setLinkTrackingTimer: function (delay) {
					configTrackerPause = delay;
				},

				/**
				 * Set list of file extensions to be recognized as downloads
				 *
				 * @param string extensions
				 */
				setDownloadExtensions: function (extensions) {
					configDownloadExtensions = extensions;
				},

				/**
				 * Specify additional file extensions to be recognized as downloads
				 *
				 * @param string extensions
				 */
				addDownloadExtensions: function (extensions) {
					configDownloadExtensions += '|' + extensions;
				},

				/**
				 * Set array of domains to be treated as local
				 *
				 * @param string|array hostsAlias
				 */
				setDomains: function (hostsAlias) {
					configHostsAlias = isString(hostsAlias) ? [hostsAlias] : hostsAlias;
					configHostsAlias.push(domainAlias);
				},

				/**
				 * Set array of classes to be ignored if present in link
				 *
				 * @param string|array ignoreClasses
				 */
				setIgnoreClasses: function (ignoreClasses) {
					configIgnoreClasses = isString(ignoreClasses) ? [ignoreClasses] : ignoreClasses;
				},

				/**
				 * Set request method
				 *
				 * @param string method GET or POST; default is GET
				 */
				setRequestMethod: function (method) {
					configRequestMethod = method || 'GET';
				},

				/**
				 * Override referrer
				 *
				 * @param string url
				 */
				setReferrerUrl: function (url) {
					configReferrerUrl = url;
				},

				/**
				 * Override url
				 *
				 * @param string url
				 */
				setCustomUrl: function (url) {
					configCustomUrl = url;
				},

				/**
				 * Override document.title
				 *
				 * @param string title
				 */
				setDocumentTitle: function (title) {
					configTitle = title;
				},

				/**
				 * Set array of classes to be treated as downloads
				 *
				 * @param string|array downloadClasses
				 */
				setDownloadClasses: function (downloadClasses) {
					configDownloadClasses = isString(downloadClasses) ? [downloadClasses] : downloadClasses;
				},

				/**
				 * Set array of classes to be treated as outlinks
				 *
				 * @param string|array linkClasses
				 */
				setLinkClasses: function (linkClasses) {
					configLinkClasses = isString(linkClasses) ? [linkClasses] : linkClasses;
				},

				/**
				 * Strip hash tag (or anchor) from URL
				 *
				 * @param bool enableFilter
				 */
				discardHashTag: function (enableFilter) {
					configDiscardHashTag = enableFilter;
				},

				/**
				 * Set first-party cookie name prefix
				 *
				 * @param string cookieNamePrefix
				 */
				setCookieNamePrefix: function (cookieNamePrefix) {
					configCookieNamePrefix = cookieNamePrefix;
					// Re-init the Custom Variables cookie
					customVariables = getCustomVariablesFromCookie();
				},

				/**
				 * Set first-party cookie domain
				 *
				 * @param string domain
				 */
				setCookieDomain: function (domain) {
					configCookieDomain = domainFixup(domain);
					updateDomainHash();
				},

				/**
				 * Set first-party cookie path
				 *
				 * @param string domain
				 */
				setCookiePath: function (path) {
					configCookiePath = path;
					updateDomainHash();
				},

				/**
				 * Set visitor cookie timeout (in seconds)
				 *
				 * @param int timeout
				 */
				setVisitorCookieTimeout: function (timeout) {
					configVisitorCookieTimeout = timeout * 1000;
				},

				/**
				 * Set session cookie timeout (in seconds)
				 *
				 * @param int timeout
				 */
				setSessionCookieTimeout: function (timeout) {
					configSessionCookieTimeout = timeout * 1000;
				},

				/**
				 * Set referral cookie timeout (in seconds)
				 *
				 * @param int timeout
				 */
				setReferralCookieTimeout: function (timeout) {
					configReferralCookieTimeout = timeout * 1000;
				},

				/**
				 * Set conversion attribution to first referrer
				 *
				 * @param bool enable If true, use first referrer; if false, use the last referrer
				 */
				setConversionAttributionFirstReferrer: function (enable) {
					configConversionAttributionFirstReferrer = enable;
				},

				/**
				 * Handle do-not-track requests
				 *
				 * @param bool enable If true, don't track if user agent sends 'do-not-track' header
				 */
				setDoNotTrack: function (enable) {
					configDoNotTrack = enable && navigatorAlias.doNotTrack;
				},

				/**
				 * Add click listener to a specific link element.
				 * When clicked, Piwik will log the click automatically.
				 *
				 * @param DOMElement element
				 * @param bool enable If true, use pseudo click-handler (mousedown+mouseup)
				 */
				addListener: function (element, enable) {
					addClickListener(element, enable);
				},

				/**
				 * Install link tracker
				 *
				 * The default behaviour is to use actual click events.  However, some browsers
				 * (e.g., Firefox, Opera, and Konqueror) don't generate click events for the middle mouse button.
				 *
				 * To capture more "clicks", the pseudo click-handler uses mousedown + mouseup events.
				 * This is not industry standard and is vulnerable to false positives (e.g., drag events).
				 *
				 * @param bool enable If true, use pseudo click-handler (mousedown+mouseup)
				 */
				enableLinkTracking: function (enable) {
					if (hasLoaded) {
						// the load event has already fired, add the click listeners now
						addClickListeners(enable);
					} else {
						// defer until page has loaded
						registeredOnLoadHandlers.push(function () {
							addClickListeners(enable);
						});
					}
				},

				/**
				 * Set heartbeat (in seconds)
				 *
				 * @param int minimumVisitLength
				 * @param int heartBeatDelay
				 */
				setHeartBeatTimer: function (minimumVisitLength, heartBeatDelay) {
					var now = new Date();

					configMinimumVisitTime = now.getTime() + minimumVisitLength * 1000;
					configHeartBeatTimer = heartBeatDelay * 1000;
				},

				/**
				 * Frame buster
				 */
				killFrame: function () {
					if (windowAlias.location !== windowAlias.top.location) {
						windowAlias.top.location = windowAlias.location;
					}
				},

				/**
				 * Redirect if browsing offline (aka file: buster)
				 *
				 * @param string url Redirect to this URL
				 */
				redirectFile: function (url) {
					if (windowAlias.location.protocol === 'file:') {
						windowAlias.location = url;
					}
				},

				/**
				 * Trigger a goal
				 *
				 * @param int|string idGoal
				 * @param int|float customRevenue
				 * @param mixed customData
				 */
				trackGoal: function (idGoal, customRevenue, customData) {
					logGoal(idGoal, customRevenue, customData);
				},

				/**
				 * Manually log a click from your own code
				 *
				 * @param string sourceUrl
				 * @param string linkType
				 * @param mixed customData
				 */
				trackLink: function (sourceUrl, linkType, customData) {
					logLink(sourceUrl, linkType, customData);
				},

				/**
				 * Log visit to this page
				 *
				 * @param string customTitle
				 * @param mixed customData
				 */
				trackPageView: function (customTitle, customData) {
					logPageView(customTitle, customData);
				}
			};
		}

		/************************************************************
		 * Proxy object
		 * - this allows the caller to continue push()'ing to _paq
		 *   after the Tracker has been initialized and loaded
		 ************************************************************/

		function TrackerProxy() {
			return {
				push: apply
			};
		}

		/************************************************************
		 * Constructor
		 ************************************************************/

		// initialize the Piwik singleton
		addEventListener(windowAlias, 'beforeunload', beforeUnloadHandler, false);
		addReadyListener();

		asyncTracker = new Tracker();

		for (i = 0; i < _paq.length; i++) {
			apply(_paq[i]);
		}

		// replace initialization array with proxy object
		_paq = new TrackerProxy();

		/************************************************************
		 * Public data and methods
		 ************************************************************/

		return {
			/**
			 * Add plugin
			 *
			 * @param string pluginName
			 * @param Object pluginObj
			 */
			addPlugin: function (pluginName, pluginObj) {
				plugins[pluginName] = pluginObj;
			},

			/**
			 * Get Tracker (factory method)
			 *
			 * @param string piwikUrl
			 * @param int|string siteId
			 * @return Tracker
			 */
			getTracker: function (piwikUrl, siteId) {
				return new Tracker(piwikUrl, siteId);
			},

			/**
			 * Get internal asynchronous tracker object
			 *
			 * @return Tracker
			 */
			getAsyncTracker: function () {
				return asyncTracker;
			}
		};
	}()),

	/************************************************************
	 * Deprecated functionality below
	 * - for legacy piwik.js compatibility
	 ************************************************************/

	/*
	 * Piwik globals
	 *
	 *   var piwik_install_tracker, piwik_tracker_pause, piwik_download_extensions, piwik_hosts_alias, piwik_ignore_classes;
	 */

	piwik_track,

	/**
	 * Track page visit
	 *
	 * @param string documentTitle
	 * @param int|string siteId
	 * @param string piwikUrl
	 * @param mixed customData
	 */
	piwik_log = function (documentTitle, siteId, piwikUrl, customData) {
		"use strict";

		function getOption(optionName) {
			try {
				return eval('piwik_' + optionName);
			} catch (e) { }

			return; /* undefined */
		}

		// instantiate the tracker
		var option,
			piwikTracker = Piwik.getTracker(piwikUrl, siteId);

		// initializer tracker
		piwikTracker.setDocumentTitle(documentTitle);
		piwikTracker.setCustomData(customData);

		// handle Piwik globals
		if (!!(option = getOption('tracker_pause'))) {
			piwikTracker.setLinkTrackingTimer(option);
		}
		if (!!(option = getOption('download_extensions'))) {
			piwikTracker.setDownloadExtensions(option);
		}
		if (!!(option = getOption('hosts_alias'))) {
			piwikTracker.setDomains(option);
		}
		if (!!(option = getOption('ignore_classes'))) {
			piwikTracker.setIgnoreClasses(option);
		}

		// track this page view
		piwikTracker.trackPageView();

		// default is to install the link tracker
		if ((getOption('install_tracker'))) {

			/**
			 * Track click manually (function is defined below)
			 *
			 * @param string sourceUrl
			 * @param int|string siteId
			 * @param string piwikUrl
			 * @param string linkType
			 */
			piwik_track = function (sourceUrl, siteId, piwikUrl, linkType) {
				piwikTracker.setSiteId(siteId);
				piwikTracker.setTrackerUrl(piwikUrl);
				piwikTracker.trackLink(sourceUrl, linkType);
			};

			// set-up link tracking
			piwikTracker.enableLinkTracking();
		}
	};
