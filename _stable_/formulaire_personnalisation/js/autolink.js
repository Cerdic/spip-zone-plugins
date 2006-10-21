/*
Copyright (c) 2006 Dan Webb

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
documentation files (the "Software"), to deal in the Software without restriction, including without limitation 
the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial 
portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED 
TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF 
CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS 
IN THE SOFTWARE.
*/
DomBuilder = {
  IE_TRANSLATIONS : {
    'class' : 'className',
    'for' : 'htmlFor'
  },
  ieAttrSet : function(a, i, el) {
    var trans;
    if (trans = this.IE_TRANSLATIONS[i]) el[trans] = a[i];
    else if (i == 'style') el.style.cssText = a[i];
    else if (i.match(/^on/)) el[i] = new Function(a[i]);
    else el.setAttribute(i, a[i]);
  },
	apply : function(o) { 
	  o = o || {};
		var els = ("p|div|span|strong|em|img|table|tr|td|th|thead|tbody|tfoot|pre|code|" + 
					   "h1|h2|h3|h4|h5|h6|ul|ol|li|form|input|textarea|legend|fieldset|" + 
					   "select|option|blockquote|cite|br|hr|dd|dl|dt|address|a|button|abbr|acronym|" +
					   "script|link|style|bdo|ins|del|object|param|col|colgroup|optgroup|caption|" + 
					   "label|dfn|kbd|samp|var").split("|");
    var el, i=0;
		while (el = els[i++]) o[el.toUpperCase()] = DomBuilder.tagFunc(el);
		return o;
	},
	tagFunc : function(tag) {
	  return function() {
	    var a = arguments, at, ch; a.slice = [].slice; if (a.length>0) { 
	    if (a[0].nodeName || typeof a[0] == "string") ch = a; 
	    else { at = a[0]; ch = a.slice(1); } }
	    return DomBuilder.elem(tag, at, ch);
	  }
  },
	elem : function(e, a, c) {
		a = a || {}; c = c || [];
		var isIE = navigator.userAgent.match(/MSIE/)
		var el = document.createElement((isIE && a.name)?"<" + e + " name=" + a.name + ">":e);
		for (var i in a) {
		  if (typeof a[i] != 'function') {
		    if (isIE) this.ieAttrSet(a, i, el);
		    else el.setAttribute(i, a[i]);
		  }
	  }
		for (var i=0; i<c.length; i++) {
			if (typeof c[i] == 'string') c[i] = document.createTextNode(c[i]);
			el.appendChild(c[i]);
		} 
		return el;
	}
}

/*
	class manipulation function written by Harmen Christope
*/

function trim(s) {return s.replace(/(^\s+)|(\s+$)/g,"");}

function hasClassName(oNode,className) {
	return (oNode.nodeType==1)?((" "+oNode.className+" ").indexOf(" "+className+" ")!=-1):false;
}

function addClassName(oNode,className) {
	if ((oNode.nodeType==1) && !hasClassName(oNode,className))
		oNode.className = trim(oNode.className+" "+className);
}

function deleteClassName(oNode,className) {
	if (oNode.nodeType==1)
    oNode.className = trim((" "+oNode.className+" ").replace(" "+className+" "," "));
}

/*
	read cookie function
*/

function getCookieVal(offset)
{
var endstr=document.cookie.indexOf (";", offset);
if (endstr==-1) endstr=document.cookie.length;
return unescape(document.cookie.substring(offset, endstr));
}
function LireCookie(nom)
{
var arg=nom+"=";
var alen=arg.length;
var clen=document.cookie.length;
var i=0;
while (i<clen)
{
var j=i+alen;
if (document.cookie.substring(i, j)==arg) return getCookieVal(j);
i=document.cookie.indexOf(" ",i)+1;
if (i==0) break;

}
return null;
}

/*	
	autolink functions - Aurelien Levy - http://www.fairytells.net - licence LGPL 
*/

function gotolink(){
	monform = document.getElementById("personnalisation");
	if(monform=!'undefined'){
   		for (var j=0;j<monform.elements["duree"].length;j++) {
       		if (monform.elements["duree"][j].checked) {
           		duree = monform.elements["duree"][j].value;
           		break;
       		}
   		}
   	}else{
   		duree=LireCookie("spip_personnalisation_duree");
   	}
 	urlencours=this.href;
	retardateur = setTimeout("redirecturlencours(urlencours);",duree);
}

function stopgotolink(){
 	clearTimeout(retardateur);
}


function redirecturlencours(urlencours){
   window.location=urlencours;
}

function gosubmit(){
	monform = document.getElementById("personnalisation");
	if(monform=!'undefined'){
   		for (var j=0;j<monform.elements["duree"].length;j++) {
       		if (monform.elements["duree"][j].checked) {
           		duree = monform.elements["duree"][j].value;
           		break;
       		}
   		}
   	}else{
   		duree=LireCookie("spip_personnalisation_duree");
   	}
 	formencours=this.form;
	retardateur2 = setTimeout("submitform(formencours);",duree);
}

function stopsubmit(){
 	clearTimeout(retardateur2);
}


function submitform(formencours){
   formencours.submit();
}

function goradio(){
	monform = document.getElementById("personnalisation");
	if(monform=!'undefined'){
   		for (var j=0;j<monform.elements["duree"].length;j++) {
       		if (monform.elements["duree"][j].checked) {
           		duree = monform.elements["duree"][j].value;
           		break;
       		}
   		}
   	}else{
   		duree=LireCookie("spip_personnalisation_duree");
   	}
    if ( document.all ) monid = this.getAttribute('htmlFor');
    else monid = this.getAttribute('for');
    champencours = document.getElementById(monid);
	retardateur3 = setTimeout("radiobox(champencours);",duree);
}

function stopradio(){
 	clearTimeout(retardateur3);
}


function radiobox(champencours){
   champencours.checked = true;
}


//Page Scroller (aka custom scrollbar)- By Dynamic Drive
//For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
//This credit MUST stay intact for use

var Hoffset=90 //Enter buttons' offset from right edge of window (adjust depending on images width)
var Voffset=100 //Enter buttons' offset from bottom edge of window (adjust depending on images height)
var thespeed=3 //Enter scroll speed in integer (Advised: 1-3)

var ieNOTopera=document.all&&navigator.userAgent.indexOf("Opera")==-1
var myspeed=0

var ieHoffset_extra=document.all? 15 : 0
function iecompattest(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function positionit(){
var dsocleft=document.all? iecompattest().scrollLeft : pageXOffset
var dsoctop=document.all? iecompattest().scrollTop : pageYOffset
var window_width=ieNOTopera? iecompattest().clientWidth+ieHoffset_extra : window.innerWidth+ieHoffset_extra
var window_height=ieNOTopera? iecompattest().clientHeight : window.innerHeight

if (document.all||document.getElementById){
cross_obj.style.left=parseInt(dsocleft)+parseInt(window_width)-Hoffset+"px"
cross_obj.style.top=dsoctop+parseInt(window_height)-Voffset+"px"
}
else if (document.layers){
cross_obj.left=dsocleft+window_width-Hoffset
cross_obj.top=dsoctop+window_height-Voffset
}
}

function scrollwindow(){
window.scrollBy(0,myspeed)
}

function initializeIT(){
positionit()
if (myspeed!=0){
scrollwindow()
}
}

// Dean Edwards/Matthias Miller/John Resig

function init() {
	// quit if this function has already been called
	if (arguments.callee.done) return;

	// flag this function so we don't do the same thing twice
	arguments.callee.done = true;

	// kill the timer
	if (_timer) clearInterval(_timer);

	// do stuff
    DomBuilder.apply(window);
	var ctrlZone = DIV({'id': 'staticbuttons'}, 
					A({'href': 'javascript','onmouseover': 'myspeed=-thespeed', 'onmouseout': 'myspeed=0', 'class': 'scroll'},
                    IMG({'src': '' + imgPath + 'arrows_up.gif'})), 
					BR(), 
					A({'href': 'javascript','onmouseover': 'myspeed=thespeed', 'onmouseout': 'myspeed=0', 'class': 'scroll'},
                    IMG({'src': '' + imgPath + 'arrows_dn.gif'}))
			        );


    document.body.appendChild(ctrlZone);
    
    
    var links=document.getElementsByTagName("a");
	for (i=0;i<links.length;i++){
	if(!hasClassName(links[i],"scroll")){
	links[i].onmouseover = gotolink;
	links[i].onmouseout = stopgotolink;
	}
	}
    
    var inputs=document.getElementsByTagName("input");
	for (j=0;j<inputs.length;j++){
	if(inputs[j].getAttribute('type')=='submit'){
	inputs[j].onmouseover = gosubmit;
	inputs[j].onmouseout = stopsubmit;
	}
	}
    
    var labels=document.getElementsByTagName("label");
	for (k=0;k<labels.length;k++){
	if ( labels[k].getAttribute("htmlFor") || labels[k].getAttribute("for") ) {
        if ( document.all ) monid = labels[k].getAttribute('htmlFor');
        else monid = labels[k].getAttribute('for');
        monchamp = document.getElementById(monid);
        if(monchamp.getAttribute('type')=='radio'){
        labels[k].onmouseover = goradio;
	    labels[k].onmouseout = stopradio;
        }
	}
	}
    
	cross_obj=document.all? document.all.staticbuttons : document.getElementById? document.getElementById("staticbuttons") : document.staticbuttons
	if (document.all||document.getElementById||document.layers)
	montimer2=setInterval("initializeIT()",20);

};

/* for Mozilla/Opera9 */
if (document.addEventListener) {
	document.addEventListener("DOMContentLoaded", init, false);
}

/* for Internet Explorer */
/*@cc_on @*/
/*@if (@_win32)
	document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
	var script = document.getElementById("__ie_onload");
	script.onreadystatechange = function() {
		if (this.readyState == "complete") {
			init(); // call the onload handler
		}
	};
/*@end @*/

/* for Safari */
if (/WebKit/i.test(navigator.userAgent)) { // sniff
	var _timer = setInterval(function() {
		if (/loaded|complete/.test(document.readyState)) {
			init(); // call the onload handler
		}
	}, 10);
}

/* for other browsers */
window.onload = init;