<?php
/*------------------------------------------------------------------------------
     The contents of this file are subject to the Mozilla Public License
     Version 1.1 (the "License"); you may not use this file except in
     compliance with the License. You may obtain a copy of the License at
     http://www.mozilla.org/MPL/

     Software distributed under the License is distributed on an "AS IS"
     basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
     License for the specific language governing rights and limitations
     under the License.

     The Original Code is init.php, released on 2003-03-31.

     The Initial Developer of the Original Code is The QuiX project.

     Alternatively, the contents of this file may be used under the terms
     of the GNU General Public License Version 2 or later (the "GPL"), in
     which case the provisions of the GPL are applicable instead of
     those above. If you wish to allow use of your version of this file only
     under the terms of the GPL and not to allow others to use
     your version of this file under the MPL, indicate your decision by
     deleting  the provisions above and replace  them with the notice and
     other provisions required by the GPL.  If you do not delete
     the provisions above, a recipient may use your version of this file
     under either the MPL or the GPL."
------------------------------------------------------------------------------*/
/*------------------------------------------------------------------------------
Author: The QuiX project
	quix@free.fr
	http://www.quix.tk
	http://quixplorer.sourceforge.net

Comment:
	QuiXplorer Version 2.3
	Main File
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
// Vars
if(isset($_SERVER)) {
	$GLOBALS['spx']['__GET']	=&$_GET;
	$GLOBALS['spx']['__POST']	=&$_POST;
	$GLOBALS['spx']['__SERVER']	=&$_SERVER;
	$GLOBALS['spx']['__FILES']	=&$_FILES;
} elseif(isset($HTTP_SERVER_VARS)) {
	$GLOBALS['spx']['__GET']	=&$HTTP_GET_VARS;
	$GLOBALS['spx']['__POST']	=&$HTTP_POST_VARS;
	$GLOBALS['spx']['__SERVER']	=&$HTTP_SERVER_VARS;
	$GLOBALS['spx']['__FILES']	=&$HTTP_POST_FILES;
} else {
	die("<B>ERROR: Your PHP version is too old</B><BR>".
	"You need at least PHP 4.0.0 to run QuiXplorer; preferably PHP 4.3.1 or higher.");
}
//------------------------------------------------------------------------------
// Get Action
if(isset($GLOBALS['spx']['__GET']["action"])) $GLOBALS['spx']["action"]=$GLOBALS['spx']['__GET']["action"];
else $GLOBALS['spx']["action"]="list";
if($GLOBALS['spx']["action"]=="post" && isset($GLOBALS['spx']['__POST']["do_action"])) {
	$GLOBALS['spx']["action"]=$GLOBALS['spx']['__POST']["do_action"];
}
if($GLOBALS['spx']["action"]=="") $GLOBALS['spx']["action"]="list";
$GLOBALS['spx']["action"]=stripslashes($GLOBALS['spx']["action"]);
// Default Dir
if(isset($GLOBALS['spx']['__GET']["dir"])) $GLOBALS['spx']["dir"]=stripslashes($GLOBALS['spx']['__GET']["dir"]);
else $GLOBALS['spx']["dir"]="";
if($GLOBALS['spx']["dir"]==".") $GLOBALS['spx']["dir"]=="";
// Get Item
if(isset($GLOBALS['spx']['__GET']["item"])) $GLOBALS['spx']["item"]=stripslashes($GLOBALS['spx']['__GET']["item"]);
else $GLOBALS['spx']["item"]="";
// Get Sort
if(isset($GLOBALS['spx']['__GET']["order"])) $GLOBALS['spx']["order"]=stripslashes($GLOBALS['spx']['__GET']["order"]);
else $GLOBALS['spx']["order"]="name";
if($GLOBALS['spx']["order"]=="") $GLOBALS['spx']["order"]=="name";
// Get Sortorder (yes==up)
if(isset($GLOBALS['spx']['__GET']["srt"])) $GLOBALS['spx']["srt"]=stripslashes($GLOBALS['spx']['__GET']["srt"]);
else $GLOBALS['spx']["srt"]="yes";
if($GLOBALS['spx']["srt"]=="") $GLOBALS['spx']["srt"]=="yes";
// Get Language
if(isset($GLOBALS['spx']['__GET']["lang"])) $GLOBALS['spx']["lang"]=$GLOBALS['spx']['__GET']["lang"];
elseif() $GLOBALS['spx']["lang"]=_request("lang");
//------------------------------------------------------------------------------
// Necessary files
ob_start(); // prevent unwanted output
include_spip("config/spx_conf");
if(isset($GLOBALS['spx']["lang"])) $GLOBALS['spx']["language"]=$GLOBALS['spx']["lang"];
include_spip("spx_lang/".$GLOBALS['spx']["language"]."");
include_spip("spx_lang/".$GLOBALS['spx']["language"]."_mimes");
include_spip("config/spx_mimes");
include_spip("inc/spx_extra");
include_spip("inc/spx_header");
include_spip("inc/spx_footer");
include_spip("inc/spx_error");
ob_end_clean(); // get rid of cached unwanted output
//------------------------------------------------------------------------------
if($GLOBALS['spx']["require_login"]) {	// LOGIN
	ob_start(); // prevent unwanted output
	include_spip("inc/spx_login");
	ob_end_clean(); // get rid of cached unwanted output
	if($GLOBALS['spx']["action"]=="logout") {
		logout();
	} else {
		login();
	}
}
//------------------------------------------------------------------------------
$abs_dir=get_abs_dir($GLOBALS['spx']["dir"]);
if(!@file_exists($GLOBALS['spx']["home_dir"])) {
	if($GLOBALS['spx']["require_login"]) {
		$extra="<A HREF=\"".make_link("logout",NULL,NULL)."\">".
			_T('spixplorer:btnlogout')."</A>";
	} else $extra=NULL;
	show_error(_T('spixplorer:home'),$extra);
}
if(!down_home($abs_dir)) show_error($GLOBALS['spx']["dir"]." : "._T('spixplorer:abovehome'));
if(!is_dir($abs_dir)) show_error($GLOBALS['spx']["dir"]." : "._T('spixplorer:direxist'));
//------------------------------------------------------------------------------
?>
