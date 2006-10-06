/*
	domEl() function - painless DOM manipulation
	written by Pawel Knapik  //  pawel.saikko.com
*/

var domEl = function(e,c,a,p,x) {
if(e||c) {
	c=(typeof c=='string'||(typeof c=='object'&&!c.length))?[c]:c;	
	e=(!e&&c.length==1)?document.createTextNode(c[0]):e;	
	var n = (typeof e=='string')?document.createElement(e) : !(e&&e===c[0])?e.cloneNode(false):e.cloneNode(true);	
	if(e.nodeType!=3) {
		c[0]===e?c[0]='':'';
		for(var i=0,j=c.length;i<j;i++) typeof c[i]=='string'?n.appendChild(document.createTextNode(c[i])):n.appendChild(c[i].cloneNode(true));
		if(a) {for(var i=(a.length-1);i>=0;i--) a[i][0]=='class'?n.className=a[i][1]:n.setAttribute(a[i][0],a[i][1]);}
	}
}
	if(!p)return n;
	p=(typeof p=='object'&&!p.length)?[p]:p;
	for(var i=(p.length-1);i>=0;i--) {
		if(x){while(p[i].firstChild)p[i].removeChild(p[i].firstChild);
			if(!e&&!c&&p[i].parentNode)p[i].parentNode.removeChild(p[i]);}
		if(n) p[i].appendChild(n.cloneNode(true));
	}	
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
	autolink functions - Aurelien Levy - http://www.fairytells.net - licence LGPL 
*/

function gotolink(){
	var duree=LireCookie("spip_personnalisation_duree");
 	urlencours=this.href;
	retardateur = setTimeout("redirecturlencours(urlencours);",duree);
}

function stopgotolink(){
 	clearTimeout(retardateur);
}


function redirecturlencours(urlencours){
   window.location=urlencours;
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
	domEl('div',[domEl('a',[domEl('img','',[['src','arrows_up.gif']])],[['href','javascript:'],['onmouseover','myspeed=-thespeed'],['onmouseout','myspeed=0'],['class','scroll']]),domEl('br','',''),domEl('a',[domEl('img','',[['src','arrows_dn.gif']])],[['href','javascript:'],['onmouseover','myspeed=thespeed'],['onmouseout','myspeed=0'],['class','scroll']])],[['id','staticbuttons'],['style','position:absolute;']],document.body,0);
	var links=document.getElementsByTagName("a");
	for (i=0;i<links.length;i++){
	if(!hasClassName(links[i],"scroll")){
	links[i].onmouseover = gotolink;
	links[i].onmouseout = stopgotolink;
	}
	}
	cross_obj=document.all? document.all.stopper : document.getElementById? document.getElementById("staticbuttons") : document.stopper
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