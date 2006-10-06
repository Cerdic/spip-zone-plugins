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
	getElementsByTagNames() function - The getElementsByTagNames script 
	takes a list of tag names and returns an array that contains all elements 
	with these tag names in the order they appear in the source code.
	written by PPK  //  http://www.quirksmode.org/
*/

function getElementsByTagNames(list,obj) {
	if (!obj) var obj = document;
	var tagNames = list.split(',');
	var resultArray = new Array();
	for (var i=0;i<tagNames.length;i++) {
		var tags = obj.getElementsByTagName(tagNames[i]);
		for (var j=0;j<tags.length;j++) {
			resultArray.push(tags[j]);
		}
	}
	var testNode = resultArray[0];
	if (!testNode) return [];
	if (testNode.sourceIndex) {
		resultArray.sort(function (a,b) {
				return a.sourceIndex - b.sourceIndex;
		});
	}
	else if (testNode.compareDocumentPosition) {
		resultArray.sort(function (a,b) {
				return 3 - (a.compareDocumentPosition(b) & 6);
		});
	}
	return resultArray;
}

/*
	getElementsByClassName() function - The getElementsByClassName script 
	returns an array that contains all elements with a class names.
	written by Stuart Colville  //  http://muffinresearch.co.uk/
*/

function getElementsByClassName(strClass, strTag, objContElm) {
  strTag = strTag || "*";
  objContElm = objContElm || document;
  var objColl = (strTag == '*' && document.all && !window.opera) ? document.all : objContElm.getElementsByTagName(strTag);
  var arr = new Array();
  var delim = strClass.indexOf('|') != -1  ? '|' : ' ';
  var arrClass = strClass.split(delim);
  for (var i = 0, j = objColl.length; i < j; i++) {
    var arrObjClass = objColl[i].className.split(' ');
    if (delim == ' ' && arrClass.length > arrObjClass.length) continue;
    var c = 0;
    comparisonLoop:
    for (var k = 0, l = arrObjClass.length; k < l; k++) {
      for (var m = 0, n = arrClass.length; m < n; m++) {
        if (arrClass[m] == arrObjClass[k]) c++;
        if (( delim == '|' && c == 1) || (delim == ' ' && c == arrClass.length)) {
          arr.push(objColl[i]);
          break comparisonLoop;
        }
      }
    }
  }
  return arr;
}

// To cover IE 5.0's lack of the push method
Array.prototype.push = function(value) {
  this[this.length] = value;
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
	autofocus function - Aurelien Levy - http://www.fairytells.net - licence LGPL
*/


var focusencours=-1;

function autofocus(nblinks) {
	if(focusencours>nblinks-2){
		focusencours=-1;
	}
	focusencours++;
	for(i=0;i<nblinks;i++){
		if(focusencours==i){
			document.links[meslinks[i]].focus();
         	addClassName(document.links[meslinks[i]],"encours");
         	
    	} else{
    		deleteClassName(document.links[meslinks[i]],"encours");
    	}
   }
}

function stopautofocus(){
	clearInterval(montimer);
	mesafocus = getElementsByClassName('encours','a');
	for(j=0;j<mesafocus.length;j++){
		deleteClassName(mesafocus[j],"encours");
   	}
	ex = document.getElementById('stopper');
	domEl('','','',ex.getElementsByTagName('a'),1);
	domEl('a','Reprendre le défilement',[['href','javascript:'],['onclick','loadautofocus();return false;']],ex,0);
	domEl('br','','',ex,0);
	domEl('a','Monter',[['href','javascript:'],['onclick','monter(100);return false;']],ex,0);
	domEl('br','','',ex,0);
	domEl('a','Descendre',[['href','javascript:'],['onclick','descendre(100);return false;']],ex,0);
	ex.firstChild.focus();
	totallinks=getElementsByTagNames('a');
	meslinks = new Array();
	for(i=0;i<totallinks.length;i++){
		if (totallinks[i].parentNode.id=='stopper'){
			meslinks.push(i);
		}	
   	}
   	duree=LireCookie("duree");
	nblinks=meslinks.length;
	montimer=setInterval("autofocus("+nblinks+")", duree);
}


function loadautofocus(){
	clearInterval(montimer);
	ex = document.getElementById('stopper');
	domEl('','','',ex.getElementsByTagName('a'),1);
	domEl('','','',ex.getElementsByTagName('br'),1);
	domEl('a','Arrêter le défilement',[['href','javascript:'],['onclick','stopautofocus();return false;']],ex,0);	
	totallinks=getElementsByTagNames('a');
	meslinks = new Array();
	for(i=0;i<totallinks.length;i++){
		if (!/#[0-9a-zA-Z]*/.test(totallinks[i].href)){
			meslinks.push(i);
		}	
   	}
   	duree=LireCookie("spip_personnalisation_duree");
	nblinks=meslinks.length;
	focusencours=-1;
	montimer=setInterval("autofocus("+nblinks+")", duree);
}

function monter(valeur){
	self.scrollBy(0,-valeur);
}

function descendre(valeur){
	self.scrollBy(0,valeur);
}

//Page Scroller (aka custom scrollbar)- By Dynamic Drive
//For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
//This credit MUST stay intact for use

var Hoffset=250 //Enter buttons' offset from right edge of window (adjust depending on images width)
var Voffset=70 //Enter buttons' offset from bottom edge of window (adjust depending on images height)
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
	domEl('div',[domEl('a','Arrêter le défilement',[['href','javascript:'],['onclick','stopautofocus();return false;']])],[['id','stopper'],['style','position:absolute;']],document.body,0);
	totallinks=getElementsByTagNames('a');
	meslinks = new Array();
	for(i=0;i<totallinks.length;i++){
		if (!/#[0-9a-zA-Z]*/.test(totallinks[i].href)){
			meslinks.push(i);
		}	
   	}
   	duree=LireCookie("duree");
	nblinks=meslinks.length;
	montimer=setInterval("autofocus("+nblinks+")", duree);
	cross_obj=document.all? document.all.stopper : document.getElementById? document.getElementById("stopper") : document.stopper
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