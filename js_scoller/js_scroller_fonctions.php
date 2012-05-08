<?php
/**
 * @name 		Fonctions
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @copyright 	CreaDesign 2009 {@link http://creadesignweb.free.fr/}
 * @license		(c) 2009 GNU GPL v3 {@link http://opensource.org/licenses/gpl-license.php GNU Public License}
 * @version 	1.0 (10/2009)
 * @package		Javascript_Scroller
 */

function js_scroller_get_js($width,$height,$dir,$speed){
	$_dir = ($dir=='rtl') ? 'right' : 'left';
	$_dir_px = ($dir=='rtl') ? 'pixelRight' : 'pixelLeft';
	$code = <<<EOT

/***********************************
*   http://javascripts.vbarsan.com/
*   This notice may not be removed 
***********************************/
var wwidth=$width;
var wheight=$height;
var wbcolor="#fff";
var sspeed=$speed;
var restart=sspeed;
var rspeed=sspeed;
//-- end Parameters-->
var wwholemessage='';
//-- end message-->
var sizeupw=0;var operbr=navigator.userAgent.toLowerCase().indexOf('opera');if(operbr==-1&&navigator.product&&navigator.product=="Gecko"){var agt = navigator.userAgent.toLowerCase();var rvStart = agt.indexOf('rv:');var rvEnd = agt.indexOf(')', rvStart);var check15 = agt.substring(rvStart+3, rvEnd);if(parseFloat(check15)>=1.8) operbr=0;}if (navigator.appVersion.indexOf("Mac")!=-1)operbr=0;
function goup(){if(sspeed!=rspeed*8){sspeed=sspeed*2;restart=sspeed;}}
function godown(){if(sspeed>rspeed){sspeed=sspeed/2;restart=sspeed;}}
function startw(str){if(str.length == 0 || !str) return; wwholemessage = str;if(document.getElementById)ns6marqueew(document.getElementById('wslider'));else if(document.all) iemarqueew(wslider);else if(document.layers)ns4marqueew(document.wslider1.document.wslider2);}
function iemarqueew(whichdiv){iedivw=eval(whichdiv);iedivw.style.$_dir_px=wwidth+"px";iedivw.innerHTML='<nobr>'+wwholemessage+'</nobr>';sizeupw=iedivw.offsetWidth;ieslidew();}
function ieslidew(){if(iedivw.style.$_dir_px>=sizeupw*(-1)){iedivw.style.$_dir_px-=sspeed+"px";setTimeout("ieslidew()",100);}else{iedivw.style.$_dir_px=wwidth+"px";ieslidew();}}
function ns4marqueew(whichlayer){ns4layerw=eval(whichlayer);ns4layerw.$_dir=wwidth;ns4layerw.document.write('<nobr>'+wwholemessage+'</nobr>');ns4layerw.document.close();sizeupw=ns4layerw.document.width;ns4slidew();}
function ns4slidew(){if(ns4layerw.$_dir>=sizeupw*(-1)){ns4layerw.$_dir-=sspeed;setTimeout("ns4slidew()",100);}else{ns4layerw.$_dir=wwidth;ns4slidew();}}
function ns6marqueew(whichdiv){ns6divw=eval(whichdiv);ns6divw.style.$_dir=wwidth+"px";ns6divw.innerHTML='<nobr>'+wwholemessage+'</nobr>';sizeupw=ns6divw.offsetWidth;if(operbr!=-1){document.getElementById('operaslider').innerHTML='<nobr>'+wwholemessage+'</nobr>';sizeupw=document.getElementById('operaslider').offsetWidth;}ns6slidew();}
function ns6slidew(){if(parseInt(ns6divw.style.$_dir)>=sizeupw*(-1)){ns6divw.style.$_dir=parseInt(ns6divw.style.$_dir)-sspeed+"px";setTimeout("ns6slidew()",100);}else{ns6divw.style.$_dir=wwidth+"px";ns6slidew();}}
//-- end Algorithm -->
if(document.getElementById || document.all){document.write("<span style=\'width:"+wwidth+"px;\'><div id=\"js_scroller\" style=\'width:"+wwidth+"px;height:"+wheight+"px;clip:rect(0 "+wwidth+"px "+wheight+"px 0);\' onmouseover=\'sspeed=0;\' onmouseout=\'sspeed=restart\'>");
if(operbr!=-1)document.write("<div id=\'operaslider\' style=\'position:absolute;visibility:hidden;\'><\/div>");
document.write("<div id=\'wslider\' style=\'height:"+wheight+"px;\'><\/div><\/div><\/span>")}
EOT;
	return $code;
}
?>