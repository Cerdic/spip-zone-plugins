/*
 * fadingrollover for SPIP
 *
 * Copyright (c) 2006 Renato Formato (renatoformato@virgilio.it)
 * Licensed under the GPL License:
 *   http://www.gnu.org/licenses/gpl.html
 *
 */
//CSS style selector to match the fading elements
var FADINGROLLOVER_SEL = 'li';
var FADINGROLLOVER_COLOR = '#FF0000';

var init_f = function() {
	$(FADINGROLLOVER_SEL,this).fadingRollover(FADINGROLLOVER_COLOR);
}
if (typeof onAjaxLoad == "function") onAjaxLoad(init_f);
$(document).ready(init_f);

//fadingRollover effect
jQuery.fn.fadingRollover = function(colorTo,s) {
	return this.each(function(){
		var currentColor = getStyle(this,'backgroundColor');
		var rolloverEl = this;
		$(this).hover(function(){fadingRolloverIn(rolloverEl,colorTo,s)},function(){fadingRolloverOut(rolloverEl,currentColor,s)});
	})
}

function fadingRolloverIn(el,colorTo,s) {
	if(el.rolloutEffect) {el.rolloutEffect.clear();el.rolloutEffect=null;}
	var o = jQuery.speed(s,function(){el.rollinEffect=null;});
	el.rollinEffect = new jQuery.fx.fadingToColor(el,colorTo,o);
	
}

function fadingRolloverOut(el,colorTo,s) {	
	if(el.rollinEffect) {el.rollinEffect.clear();el.rollinEffect=null;}
	var o = jQuery.speed(s,function(){el.rolloutEffect=null;});
	el.rolloutEffect = new jQuery.fx.fadingToColor(el,colorTo,o);
}

//fadeToColor effect
jQuery.fn.fadingToColor = function(colorTo,s) {
	var o = jQuery.speed(s);
	return this.each(function() {
		new jQuery.fx.fadingToColor(this,colorTo,o)
		}
	);
}

jQuery.fx.fadingToColor = function(el,colorTo,o) {
	var z = this;
	z.ce = CSSToRGB(colorTo);
	z.cs = CSSToRGB(getStyle(el,'backgroundColor'));
	z.o = o;
	z.clear = function(){clearInterval(timer);timer=null;};
	var tstart =(new Date).getTime();
	z.step = function(){
		var t = (new Date).getTime();
		var p = (t - tstart) / z.o.duration;
		if (t >= z.o.duration+tstart) {
			z.clear();
			el.style.backgroundColor = RGBToCSS(z.ce);
			z.o.complete();
		} else {
			var pos = ((-Math.cos(p*Math.PI)/2) + 0.5);
			var rgb = [parseInt(pos*(z.ce[0]-z.cs[0])+z.cs[0]),
								 parseInt(pos*(z.ce[1]-z.cs[1])+z.cs[1]),
								 parseInt(pos*(z.ce[2]-z.cs[2])+z.cs[2])];
			el.style.backgroundColor = RGBToCSS(rgb);
		}
	};
	var timer=setInterval(function(){z.step();},13);
}

function getStyle(e,s) {
	var v = e.style[s];
	if(!v) {
		if (document.defaultView && document.defaultView.getComputedStyle) {
	  	s = s.replace(/([A-Z])/g,function(str){return '-'+str.toLowerCase()});
			v = document.defaultView.getComputedStyle(e, null).getPropertyValue(s);
	  } else if (e.currentStyle) {
			v = e.currentStyle[s];
	  }
  }
  if(v=='transparent' && e.parentNode)  v = getStyle(e.parentNode,s);
  return v;
}

function CSSToRGB(c) {
	var rgb = [];
	var m = c.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/); 
	if(m) {
		rgb = [parseInt(m[1]),parseInt(m[2]),parseInt(m[3])];
	} else	if(c.substring(1).length==3) {
		var chars = [c.charAt(1)+c.charAt(1),c.charAt(2)+c.charAt(2),c.charAt(3)+c.charAt(3)]
		rgb = [parseInt(chars[0],16),parseInt(chars[1],16),parseInt(chars[2],16)];
	} else {
		rgb = [parseInt(c.substring(1,3),16),parseInt(c.substring(3,5),16),parseInt(c.substring(5,7),16)];
	}
	return rgb;
}

function RGBToCSS(rgb) {return 'rgb('+rgb.join(',')+')';}
