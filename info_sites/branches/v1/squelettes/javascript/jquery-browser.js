/*
 * ******************************************************************************
 *  jquery.mb.components
 *  file: jquery.mb.browser.js
 *
 *  Copyright (c) 2001-2014. Matteo Bicocchi (Pupunzi);
 *  Open lab srl, Firenze - Italy
 *  email: matteo@open-lab.com
 *  site:   http://pupunzi.com
 *  blog:   http://pupunzi.open-lab.com
 *  http://open-lab.com
 *
 *  Licences: MIT, GPL
 *  http://www.opensource.org/licenses/mit-license.php
 *  http://www.gnu.org/licenses/gpl.html
 *
 *  last modified: 27/01/14 19.58
 *  *****************************************************************************
 */

/*******************************************************************************
 *
 * jquery.mb.browser
 * Author: pupunzi
 * Creation date: 16/01/13
 *
 ******************************************************************************/
/*Browser detection patch*/

if(!jQuery.browser){

    jQuery.browser = {};
    jQuery.browser.mozilla = false;
    jQuery.browser.webkit = false;
    jQuery.browser.opera = false;
    jQuery.browser.safari = false;
    jQuery.browser.chrome = false;
    jQuery.browser.msie = false;
    jQuery.browser.android = false;
    jQuery.browser.blackberry = false;
    jQuery.browser.ios = false;
    jQuery.browser.operaMobile = false;
    jQuery.browser.windowsMobile = false;
    jQuery.browser.mobile = false;

    var nAgt = navigator.userAgent;
    jQuery.browser.ua = nAgt;

    jQuery.browser.name  = navigator.appName;
    jQuery.browser.fullVersion  = ''+parseFloat(navigator.appVersion);
    jQuery.browser.majorVersion = parseInt(navigator.appVersion,10);
    var nameOffset,verOffset,ix;

// In Opera, the true version is after "Opera" or after "Version"
    if ((verOffset=nAgt.indexOf("Opera"))!=-1) {
        jQuery.browser.opera = true;
        jQuery.browser.name = "Opera";
        jQuery.browser.fullVersion = nAgt.substring(verOffset+6);
        if ((verOffset=nAgt.indexOf("Version"))!=-1)
            jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
    }

// In MSIE < 11, the true version is after "MSIE" in userAgent
    else if ( (verOffset=nAgt.indexOf("MSIE"))!=-1) {
        jQuery.browser.msie = true;
        jQuery.browser.name = "Microsoft Internet Explorer";
        jQuery.browser.fullVersion = nAgt.substring(verOffset+5);
    }

// In TRIDENT (IE11) => 11, the true version is after "rv:" in userAgent
    else if (nAgt.indexOf("Trident")!=-1 ) {
        jQuery.browser.msie = true;
        jQuery.browser.name = "Microsoft Internet Explorer";
        var start = nAgt.indexOf("rv:")+3;
        var end = start+4;
        jQuery.browser.fullVersion = nAgt.substring(start,end);
    }

// In Chrome, the true version is after "Chrome"
    else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) {
        jQuery.browser.webkit = true;
        jQuery.browser.chrome = true;
        jQuery.browser.name = "Chrome";
        jQuery.browser.fullVersion = nAgt.substring(verOffset+7);
    }
// In Safari, the true version is after "Safari" or after "Version"
    else if ((verOffset=nAgt.indexOf("Safari"))!=-1) {
        jQuery.browser.webkit = true;
        jQuery.browser.safari = true;
        jQuery.browser.name = "Safari";
        jQuery.browser.fullVersion = nAgt.substring(verOffset+7);
        if ((verOffset=nAgt.indexOf("Version"))!=-1)
            jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
    }
// In Safari, the true version is after "Safari" or after "Version"
    else if ((verOffset=nAgt.indexOf("AppleWebkit"))!=-1) {
        jQuery.browser.webkit = true;
        jQuery.browser.name = "Safari";
        jQuery.browser.fullVersion = nAgt.substring(verOffset+7);
        if ((verOffset=nAgt.indexOf("Version"))!=-1)
            jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
    }
// In Firefox, the true version is after "Firefox"
    else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) {
        jQuery.browser.mozilla = true;
        jQuery.browser.name = "Firefox";
        jQuery.browser.fullVersion = nAgt.substring(verOffset+8);
    }
// In most other browsers, "name/version" is at the end of userAgent
    else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) < (verOffset=nAgt.lastIndexOf('/')) ){
        jQuery.browser.name = nAgt.substring(nameOffset,verOffset);
        jQuery.browser.fullVersion = nAgt.substring(verOffset+1);
        if (jQuery.browser.name.toLowerCase()==jQuery.browser.name.toUpperCase()) {
            jQuery.browser.name = navigator.appName;
        }
    }

    /*Check all mobile environments*/
    jQuery.browser.android = (/Android/i).test(nAgt);
    jQuery.browser.blackberry = (/BlackBerry/i).test(nAgt);
    jQuery.browser.ios = (/iPhone|iPad|iPod/i).test(nAgt);
    jQuery.browser.operaMobile = (/Opera Mini/i).test(nAgt);
    jQuery.browser.windowsMobile = (/IEMobile/i).test(nAgt);
    jQuery.browser.mobile = jQuery.browser.android || jQuery.browser.blackberry || jQuery.browser.ios || jQuery.browser.windowsMobile || jQuery.browser.operaMobile;


// trim the fullVersion string at semicolon/space if present
    if ((ix=jQuery.browser.fullVersion.indexOf(";"))!=-1)
        jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,ix);
    if ((ix=jQuery.browser.fullVersion.indexOf(" "))!=-1)
        jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,ix);

    jQuery.browser.majorVersion = parseInt(''+jQuery.browser.fullVersion,10);
    if (isNaN(jQuery.browser.majorVersion)) {
        jQuery.browser.fullVersion  = ''+parseFloat(navigator.appVersion);
        jQuery.browser.majorVersion = parseInt(navigator.appVersion,10);
    }
    jQuery.browser.version = jQuery.browser.majorVersion;
}

// var txt = '' + 'navigator.appName = ' + navigator.appName + '<br>' + 'navigator.userAgent = ' + navigator.userAgent + '<br><br><br>' + 'jQuery.browser.name  = ' + jQuery.browser.name + '<br>' + 'jQuery.browser.fullVersion  = ' + jQuery.browser.fullVersion + '<br>' + 'jQuery.browser.version = ' + jQuery.browser.version + '<br>' + 'jQuery.browser.majorVersion = ' + jQuery.browser.majorVersion + '<br><br><br>' + 'jQuery.browser.msie = ' + jQuery.browser.msie + '<br>' + 'jQuery.browser.mozilla = ' + jQuery.browser.mozilla + '<br>' + 'jQuery.browser.opera = ' + jQuery.browser.opera + '<br>' + 'jQuery.browser.chrome = ' + jQuery.browser.chrome + '<br>'+ 'jQuery.browser.webkit = ' + jQuery.browser.webkit + '<br>' + '<br>' + 'jQuery.browser.android = ' + jQuery.browser.android + '<br>' + 'jQuery.browser.blackberry = ' + jQuery.browser.blackberry + '<br>' + 'jQuery.browser.ios = ' + jQuery.browser.ios + '<br>' +  'jQuery.browser.operaMobile = ' + jQuery.browser.operaMobile + '<br>' + 'jQuery.browser.windowsMobile = ' + jQuery.browser.windowsMobile + '<br>' + 'jQuery.browser.mobile = ' + jQuery.browser.mobile;

// $("#result").html(txt);