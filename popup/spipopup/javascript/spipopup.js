/**
 * Copyright (c) 2008 Pierre Cassat (piero DOT wbmstr AT gmail DOT com || http://trac.ateliers-pierrot.fr/)
 * Licensed under GPL (http://www.opensource.org/licenses/gpl-license.php) license.
 */

/*
// Version Couteau Suisse : maintenant inclus en options js
var popup_settings = {
	default_popup_name:		'popup',
	default_popup_width:	'620',
	default_popup_height:	'640'
};
*/

/**
 * <b>Verification of an URL</b>
 * Returns TRUE if url is a valid url (ftp, http or https)
 * @param string url The url you want to verify
 */
function _is_url(url){
	if (!url) return;
	var good_url = /^(f|ht)tp(s)?:\/\/((\d+\.\d+\.\d+\.\d+)|(([\w-]+\.)+([a-z,A-Z][\w-]*)))(:[1-9][0-9]*)?(\/([\w-.\/:%+@&=]+[\w- .\/?:%+@&=]*)?)?(#(.*))?$/i;
	if(!good_url.test(url)) return false;
	else return true;
}

/**
 * Simulate a clic link href
 */
function _goto(url, opener, close){
	if(!url || !_is_url(url)) return;
	if (opener) {
		window.opener.location.href = url;
		window.opener.focus();
		if(close) window.close();
	}
	else window.location.href = url;
}

/**
 * Join all args of an array in a string
 */
function _join(array, sep_arg, sep_item) {
	var string = '';
	var s_arg = (!sep_arg || sep_arg == '') ? '=' : sep_arg;
	var s_item = (!sep_item || sep_item == '') ? ';' : sep_item;
	if(typeof(array) != 'object') return;
	else {
		for(var item in array) {
			var value = array[item];
 			if(typeof(value) == 'object') {
				string += item+s_arg+'(';
				string += _join(value);
				string += ')'+s_item;
			} else {
				string += item+s_arg+value+s_item;
			}
		}
	} 
	return string;
}

/**
 * <b>Window Sizes</b>
 * Returns infos about current window loaded in an array() :
 * -> width : window's width
 * -> height : window's height
 * -> scrol_x : window's scroll X position
 * -> scrol_y : window's scroll Y position
 * -> top :window's top position
 * -> left : window's left position
 */
function _window_size() {
	var sizes = {

		width: (window.innerWidth != null) ? 
			window.innerWidth : (document.documentElement && document.documentElement.clientWidth) ?
				document.documentElement.clientWidth : (document.body != null) ? 
					document.body.clientWidth : 0,

		height: (window.innerHeight != null) ? 
			window.innerHeight : (document.documentElement && document.documentElement.clientHeight) ?  
				document.documentElement.clientHeight : (document.body != null) ? 
					document.body.clientHeight : 0,

		left: (window.screenX != null) ? 
			window.screenX : (window.top.screenLeft != null) ? 
				window.top.screenLeft : 0,

		top: (window.screenY != null) ? 
			window.screenY : (window.top.screenTop != null) ? 
				window.top.screenTop : 0,

		right: (this.left != "0") ? this.left+this.width : "0",

		bottom: (this.top != "0") ? this.top+this.height : "0",

		scrol_x: (typeof(window.pageXOffset) != 'undefined') ?
			window.pageXOffset : (document.documentElement && document.documentElement.scrollTop) ?
				document.documentElement.scrollTop : (document.body != null && document.body.scrollTop) ?
					document.body.scrollTop : 0,

		scrol_y: (typeof(window.pageYOffset) != 'undefined') ?
			window.pageYOffset : (document.documentElement && document.documentElement.scrollLeft) ?
				document.documentElement.scrollLeft : (document.body != null && document.body.scrollLeft) ?
					document.body.scrollLeft : 0

	};
	return sizes;
}

/**
 * <b>Popup Set</b>
 * Function to open a popup window.
 *
 * Args : (all optionals except url)
 * - url : page to open un popup
 * - w : popup width | default is 380px
 * - h : popup height | default is 230px
 * - focus : bool | default is TRUE
 * - options : popup window options, default : resizable=yes, toolbar=no, scrollbars=yes
 * - name : popup name | default is described ahead
 */
function _popup_set(url, w, h, focus, options, name) {
	// defaults or args
	var width = (!w || w=='') ? popup_settings.default_popup_width : w;
	var height = (!h || h=='') ? popup_settings.default_popup_height : h;
	var name_f = (!name) ? popup_settings.default_popup_name : name;

	// options
	var opt_set = {
		'directories': 0, 
		'menubar': 0, 
		'status': 0, 
		'location': 1, 
		'scrollbars': 1, 
		'resizable': 1, 
		'fullscreen': 0, 
		'width': width, 
		'height': height,
		'left': (screen.width - width)/2,
		'top': (screen.height - height)/2
	};
	var opt_f = _join(explode_options(options), '', ',');

	// function to analyze options to pass
	function explode_options(options) {
		if(!options) return opt_set;
		var opt_send = opt_set;
		var reg_first = new RegExp("[ ,]+", "g");
		var reg_second = new RegExp("[ =]+", "g");
		var opt_list = options.split(reg_first);
		for (var i=0; i<opt_list.length; i++) {
			var opt_tag = opt_list[i].split(reg_second);
			opt_send[opt_tag[0]] = opt_tag[1];
		}
		return opt_send;
	}
	// create the new window
	var new_f = window.open(url, name_f, opt_f);
	if(!focus || focus !== false) new_f.focus();
}
