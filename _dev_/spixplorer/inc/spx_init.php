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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
$test = determine_upload();

// pour l'instant reserve aux admins toutes rubriques
if ($GLOBALS['connect_statut'] != '0minirezo' || !$GLOBALS['connect_toutes_rubriques']) {
        include_spip('inc/headers');
        include_spip('inc/minipres');
        http_status('403');
        echo // spx_debut_html() .
        	$GLOBALS['connect_statut'] . $GLOBALS['connect_toutes_rubriques'] .'*-*-*-*' . _T('ecrire:avis_non_acces_page');
//        	. spx_fin_html();
        exit;
}

//------------------------------------------------------------------------------
// Vars
if(isset($_SERVER)) {
	$GLOBALS['spx']['__FILES']	=&$_FILES;
} elseif(isset($HTTP_SERVER_VARS)) {
	$GLOBALS['spx']['__FILES']	=&$HTTP_POST_FILES;
} else {
	die("<B>ERROR: Your PHP version is too old</B><BR>".
	"You need at least PHP 4.0.0 to run QuiXplorer; preferably PHP 4.3.1 or higher.");
}

//------------------------------------------------------------------------------
// Get Action
$spx_action = $GLOBALS['spx']["action"] = spx_request('action', 'spx_list');
if ($spx_action != 'spx_list') {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
}
/*
if($GLOBALS['spx']["action"]=="post" && isset($GLOBALS['spx']['__POST']["do_action"])) {
	$GLOBALS['spx']["action"]=$GLOBALS['spx']['__POST']["do_action"];
}
*/

// Default Dir
$GLOBALS['spx']["dir"] = spx_request('dir', '');
/* bugge de toute facon , == !!!!
if($GLOBALS['spx']["dir"]==".") $GLOBALS['spx']["dir"]=="";
*/

// Get Item
$GLOBALS['spx']["item"] = spx_request('item', '');

// Get Sort
$GLOBALS['spx']["order"] = spx_request('order', 'name');

// Get Sortorder (yes==up)
$GLOBALS['spx']["srt"] = spx_request('srt', 'yes');

//------------------------------------------------------------------------------
// Necessary files
ob_start(); // prevent unwanted output
include_spip("config/spx_conf");
$GLOBALS['spx']["language"] = $GLOBALS['spip_lang'];
$GLOBALS['spx']["lang"] = $GLOBALS['spip_lang'];
include_spip("spx_lang/" . $GLOBALS['spx']["language"] . "_mimes");
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

function spx_request($var, $def = null)
{
	($ret = stripslashes(_request($var))) || ($ret = $def);
	return $ret;
}
?>
