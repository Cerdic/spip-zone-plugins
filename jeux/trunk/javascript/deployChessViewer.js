/*
 * Copyright (c) 2008 Nikolai Pilafov.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   - Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 * deployChessViewer.js
 *
 * It provides functions for dynamic creation of a chess viewer instance
 * for displaying chess data contained on the Web.
 * 
 * The "live" copy of this file may be found at 
 * http://chesstuff.googlecode.com/svn/deployChessViewer.js
 * You are encouraged to link directly to the live copy of the file.
 *
 * @version @(#)deployChessViewer.js	1.6		03/16/2009
 * @version @(#)deployChessViewer.js	1.7		18/04/2020	ligne 106
 */

/**
 * Outputs an applet tag with the specified attributes and parameters, where both attributes and
 * parameters are associative arrays. Each key/value pair in attributes becomes an attribute of
 * the applet tag itself, while key/value pairs in parameters become <PARAM> tags.
 *
 * e.g.	makeChessApplet(url, {PuzzleMode: "on", DarkSquares: "AA3399"});
 *		makeChessApplet(url, null, { border: 4 });
 */

if ( typeof oCV_Publish == "undefined" ) {
  var oCV_Publish = new Object();

	oCV_Publish.PGN_Data = new Array();
	oCV_Publish.PGN_Index = 0;
}

var CHESS_VIEWER_SERVER = "http://chesstuff.110mb.com/",
	JAVA_MIME_TYPE = "application/x-java-applet";

oCV_Publish.createObjParam = function ( el, pName, pValue ) {
	if ( this.useOldWebKit ) {
		// Older webkit engines ignore the PARAM elements inside the OBJECT element, so
		// we have to fall back to the proprietary EMBED element
		el.setAttribute ( pName, pValue );
		return;
	}

	// Well-behaving browsers
	var p = document.createElement ( "PARAM" );
	p.setAttribute ( "name", pName );	
	p.setAttribute ( "value", pValue );
	el.appendChild ( p );
}

/**
 * Charset (Java Platform SE 6)
 * http://java.sun.com/javase/6/docs/api/java/nio/charset/Charset.html
 *
 * Special Edition Using Java 1.1
 * http://docs.rinet.ru/UJ11/ch16.htm
 */

/**
 * Cross-browser dynamic Applet element creation
 */

function createAppletElement ( oElem, pageUrl, paramObj, attribObj ) {
  var idStr, styleStr, sEncode, sTemp, o;

	styleStr = ( oElem.style.cssText.length > 0 ) ? oElem.style.cssText : "";
	if ( attribObj == 'undefined' || attribObj == null )
		attribObj = new Object();
	attribObj.id = oElem.getAttribute ( 'id' );
	attribObj.width = "631";
	attribObj.height = "560";
	attribObj.standby = "Loading Chess Viewer Deluxe...";
	idStr = oElem.getAttribute ( 'seqno' );
	idStr = ( idStr != null && idStr.length > 0 ) ? ( "&id=" + idStr ) : "";

	if ( paramObj == 'undefined' || paramObj == null )
		paramObj = new Object();
	paramObj.archive = 'Viewer-Deluxe.jar';
	paramObj.code = 'ChessBoard.class';
	paramObj.codebase_lookup = 'false';
	if ( pageUrl != null ) {
		paramObj.codebase = CHESS_VIEWER_SERVER + 'olympics/bin';
		paramObj.PgnGameFile = CHESS_VIEWER_SERVER + "cvd/pgn-proxy.php?url=" + encodeURI ( pageUrl ) + "&tag=s" + idStr;
	} else {
		paramObj.codebase = _JEUX_CODEBASE; //'http://chesstuff.googlecode.com/svn/bin/';
		paramObj.PgnGameFile = "javascript: oCV_Publish.getPgnData ( " + ( oCV_Publish.PGN_Index - 1 ) + " )";
	}
	paramObj.ImagesFolder = 'images';
//	paramObj.MayScript = 'on';
	paramObj.MayScript = 'true';								// Safari on Mac would only work this way
	if ( typeof paramObj.Encoding == 'undefined' ) {
		if ( typeof document.charset != 'undefined' )
			sEncode = document.charset;							// IE
		else
		if ( typeof document.characterSet != 'undefined' )
			sEncode = document.characterSet;					// Gecko

		sEncode = sEncode.toUpperCase();
		if ( sEncode == "UTF-8" )
			paramObj.Encoding = sEncode;
		else
			sEncode = "";
	} else
		sEncode = "";

	// Proprietary feature detection (conditional compiling) is used to detect IE's features
	/*@cc_on

		if ( !oCV_Publish.isJavaPluginSupported() ) {
			// Since nothing else is available we might as well try MS-Java
			sTemp = "08B0E5C0-4FCB-11CF-AAA5-00401C608501";				// ClassID of MS-Java
			if ( sEncode != "" )
				paramObj.Encoding = "UTF8";
		} else
			sTemp = "8AD9C840-044E-11D1-B3E9-00805F499D93";				// ClassID of Java Plug-in

		// IE, the object element and W3C DOM methods do not combine: fall back to outerHTML
		sTemp = '<OBJECT classid="clsID:' + sTemp + '" ' +
			'codetype="application/java-vm" align="baseline" VIEWASTEXT';
		if ( styleStr.length > 0 )
			sTemp += ' style="' + styleStr + '"';
		for ( var attribute in attribObj )
			sTemp += ( ' ' + attribute + '="' + attribObj[attribute] + '"' );
		sTemp += '>';

		for ( var parameter in paramObj )
			sTemp += '<PARAM name="' + parameter + '" value="' + paramObj[parameter] + '">';

		oElem.outerHTML = sTemp + "</OBJECT>";
		return;
	@*/

	if ( typeof oCV_Publish.useOldWebKit != "boolean" ) {
	  var u = navigator.userAgent.toLowerCase();

		// User agent string detection is only used when no alternative is possible
		if ( /webkit/.test ( u ) )
			// Compare the webkit version to a known good one
			oCV_Publish.useOldWebKit = ( parseFloat ( u.replace ( /^.*webkit\/(\d+(\.\d+)?).*$/, "$1" ) ) < 312 );
		else
			oCV_Publish.useOldWebKit = false;
  	}

	o = document.createElement ( oCV_Publish.useOldWebKit ?
		// Older webkit engines ignore the PARAM elements inside the OBJECT element, so
		// we have to fall back to the proprietary EMBED element
		"EMBED" :
		// Well-behaving browsers
		"OBJECT" );

	o.setAttribute ( "type", JAVA_MIME_TYPE );
	o.style.cssText = styleStr;
	for ( var attribute in attribObj )
		o.setAttribute ( attribute, attribObj[attribute] );

	for ( var parameter in paramObj )
		oCV_Publish.createObjParam ( o, parameter, paramObj[parameter] );

/*
	oTxt = document.createTextNode ( "Created using " + o.tagName + " element." );
	oElem.parentNode.appendChild ( oTxt );
	oElem.parentNode.style.border = "3px solid #7C5DD5";
*/
	oElem.parentNode.replaceChild ( o, oElem );
}

oCV_Publish.isJavaPluginSupported = function () {
	if ( typeof this.usePlugin != "boolean" )
		try {
			this.usePlugin = ( new ActiveXObject ( "JavaPlugin" ) != null );
		} catch (exception) {
			this.usePlugin = false;
		}

	return this.usePlugin;
}

oCV_Publish.getPgnData = function ( sParm ) {
	return this.PGN_Data[parseInt(sParm)];
}

function createAppletTag ( pageUrl, idStr, paramObj, attribObj ) {
  var sTemp;

	sTemp = "<APPLET codebase=\"" + CHESS_VIEWER_SERVER + "olympics/bin\" archive=\"Viewer-Deluxe.jar\" " +
		"code=\"ChessBoard.class\" align=\"baseline\" width=\"631\" height=\"560\"";
	for ( var attribute in attribObj )
		sTemp += ( ' ' + attribute + '="' + attribObj[attribute] + '"' );
	sTemp += '>\n';

	sTemp += "<PARAM name=PgnGameFile value=\"" + CHESS_VIEWER_SERVER + "cvd/pgn-proxy.php?url=" +
		pageUrl + "&tag=s&id=" + idStr + "\" />\n";
	sTemp += "<PARAM name=ImagesFolder value=\"images\" />\n";
	sTemp += "<PARAM name=MayScript value=\"on\" />\n";

	if ( paramObj != null ) {
	  var codebaseParam = false;

		for ( var parameter in paramObj ) {
			if ( parameter == 'codebase_lookup' )
				codebaseParam = true;
			sTemp += '<PARAM name="' + parameter + '" value="' + paramObj[parameter] + '">\n';
		}
		if ( !codebaseParam )
			sTemp += '<PARAM name="codebase_lookup" value="false">\n';
	}

	sTemp += "</APPLET>\n";
	return sTemp;
}

function makeChessApplet ( urlParm, parameters, attributes, useWrite ) {
  var oScript, sTemp, sId;

	// we must be called from inside of the very latest SCRIPT element
	sTemp = document.getElementsByTagName ( 'SCRIPT' );
	oScript = sTemp[sTemp.length - 1];
	if ( oScript.innerHTML.length > 0 ) {
	  var strUrl;

		//	if the first parameter is a string we enclose it in quotes
		strUrl = ( urlParm != null ) ? '"' + urlParm + '"' : 'null';

		sTemp = oScript.innerHTML.match ( /makeChessApplet\s*\(\s*(\S+?)[\s,\)]/ );
		sId = oScript.getAttribute ( 'id' );
		if ( sTemp == null || sTemp[1] != strUrl || sId == null || sId != 'oChessViewer' ) {
			oScript = null;
		}
	} else
		oScript = null;

	if ( oScript == null ) {
		alert ( "Unable to properly identify the corresponding SCRIPT element !" );
		return;
	}

	if ( urlParm == null ) {
	  var sText = oScript.innerHTML;

		sTemp = sText.indexOf ( "[CDATA[" );
		if ( sTemp == -1 )
			sTemp = sText.indexOf ( "/*" ) + 2;
		else
			sTemp += 7;
		sText = sText.substr ( sTemp );
		sText = sText.substr ( 0, sText.lastIndexOf ( "*/" ) );

		sText = sText.replace ( /\<br \/\>/ig, "\n" );					// caused by the blogspot Post Editor
		oCV_Publish.PGN_Data[oCV_Publish.PGN_Index++] = sText;
	}

	if ( useWrite == 'applet' ) {
		sTemp = createAppletTag ( urlParm, sId, parameters, attributes );
		document.write ( sTemp );
/*
		sTemp = sTemp.replace ( /</g, "&lt;" );
		sTemp = sTemp.replace ( />/g, ">" );
		document.write ( '<pre>' + sTemp + '</pre>' );
*/
	} else
		createAppletElement ( oScript, urlParm, parameters, attributes );
//		createManyElements ( oScript, urlParm, parameters, attributes );
};

var oMyTemp = new Object();

function createManyElements ( oElem, pageUrl, paramObj, attribObj ) {
	oMyTemp.nCount = 0;
	oMyTemp.id = oElem.getAttribute ( 'id' );
	oMyTemp.pageUrl = pageUrl;
	oMyTemp.parameters = paramObj;
	oMyTemp.attributes = attribObj;
	window.setTimeout ( createNextElement, 100 );
}

function createNextElement () {
  var oScript = document.getElementById ( oMyTemp.id );

	if ( oMyTemp.nCount % 20 == 5 )
		alert ( oMyTemp.nCount );
	createAppletElement ( oScript, oMyTemp.pageUrl, oMyTemp.parameters, oMyTemp.attributes );
	oMyTemp.nCount++;
	if ( oMyTemp.nCount < 60 )
		window.setTimeout ( createNextElement, 1000 );
	else
		alert ( "Done !" );
}
