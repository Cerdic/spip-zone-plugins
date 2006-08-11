/*
 * interface utility functions - http://www.eyecon.ro/interface/
 *
 * Copyright (c) 2006 Stefan Petre
 * Licensed under the MIT License:
 *   http://www.opensource.org/licenses/mit-license.php
 */

jQuery.getPos = function (e)
{
	var l = 0;
	var t  = 0;
	var w = jQuery.intval(jQuery.css(e,'width'));
	var h = jQuery.intval(jQuery.css(e,'height'));
	var wb = e.offsetWidth;
	var hb = e.offsetHeight;
	while (e.offsetParent){
		l += e.offsetLeft + (e.currentStyle?jQuery.intval(e.currentStyle.borderLeftWidth):0);
		t += e.offsetTop  + (e.currentStyle?jQuery.intval(e.currentStyle.borderTopWidth):0);
		e = e.offsetParent;
	}
	l += e.offsetLeft + (e.currentStyle?jQuery.intval(e.currentStyle.borderLeftWidth):0);
	t  += e.offsetTop  + (e.currentStyle?jQuery.intval(e.currentStyle.borderTopWidth):0);
	return {x:l, y:t, w:w, h:h, wb:wb, hb:hb};
};
jQuery.getClient = function(e)
{
	if (e) {
		w = e.clientWidth;
		h = e.clientHeight;
	} else {
		w = (window.innerWidth) ? window.innerWidth : (document.documentElement && document.documentElement.clientWidth) ? document.documentElement.clientWidth : document.body.offsetWidth;
		h = (window.innerHeight) ? window.innerHeight : (document.documentElement && document.documentElement.clientHeight) ? document.documentElement.clientHeight : document.body.offsetHeight;
	}
	return {w:w,h:h};
};
jQuery.getScroll = function (e) 
{
	if (e) {
		t = e.scrollTop;
		l = e.scrollLeft;
		w = e.scrollWidth;
		h = e.scrollHeight;
	} else  {
		if (document.documentElement && document.documentElement.scrollTop) {
			t = document.documentElement.scrollTop;
			l = document.documentElement.scrollLeft;
			w = document.documentElement.scrollWidth;
			h = document.documentElement.scrollHeight;
		} else if (document.body) {
			t = document.body.scrollTop;
			l = document.body.scrollLeft;
			w = document.body.scrollWidth;
			h = document.body.scrollHeight;
		}
	}
	return { t: t, l: l, w: w, h: h };
};
jQuery.getMargins = function(e)
{
	t = jQuery.css(e,"marginTop") || 0;
	r = jQuery.css(e,"marginRight") || 0;
	b = jQuery.css(e,"marginBottom") || 0;
	l = jQuery.css(e,"marginLeft") || 0;
	return {
		t: t,
		r: r,
		b: b,
		l: l
	};
};
jQuery.intval = function (v)
{
	v = parseInt(v);
	return isNaN(v) ? 0 : v;
};
jQuery.getPadding = function(e)
{
	t = jQuery.css(e,"paddingTop") || 0;
	r = jQuery.css(e,"paddingRight") || 0;
	b = jQuery.css(e,"paddingBottom") || 0;
	l = jQuery.css(e,"paddingLeft") || 0;
	return {
		t: t,
		r: r,
		b: b,
		l: l
	};
};
jQuery.getBorder = function(e)
{
	t = jQuery.css(e,"borderTopWidth") || 0;
	r = jQuery.css(e,"borderRightWidth") || 0;
	b = jQuery.css(e,"borderBottomWidth") || 0;
	l = jQuery.css(e,"borderLeftWidth") || 0;
	return {
		t: t,
		r: r,
		b: b,
		l: l
	};
};
