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

     The Original Code is index.php, released on 2003-04-02.

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
umask(002); // Added to make created files/dirs group writable
//------------------------------------------------------------------------------
include_spip("inc/spx_init");	// Init
//------------------------------------------------------------------------------
switch($GLOBALS['spx']["action"]) {		// Execute action
//------------------------------------------------------------------------------
// EDIT FILE
case "edit":
	include_spip("action/spx_edit");
	edit_file($GLOBALS['spx']["dir"], $GLOBALS['spx']["item"]);
break;
//------------------------------------------------------------------------------
// DELETE FILE(S)/DIR(S)
case "delete":
	include_spip("action/spx_del");
	del_items($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// COPY/MOVE FILE(S)/DIR(S)
case "copy":	case "move":
	include_spip("action/spx_copy_move");
	copy_move_items($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// DOWNLOAD FILE
case "download":
	ob_start(); // prevent unwanted output
	include_spip("action/spx_down");
	ob_end_clean(); // get rid of cached unwanted output
	download_item($GLOBALS['spx']["dir"], $GLOBALS['spx']["item"]);
	ob_start(false); // prevent unwanted output
	exit;
break;
//------------------------------------------------------------------------------
// UPLOAD FILE(S)
case "upload":
	include_spip("action/spx_up");
	upload_items($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// CREATE DIR/FILE
case "mkitem":
	include_spip("action/spx_mkitem");
	make_item($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// CHMOD FILE/DIR
case "chmod":
	include_spip("action/spx_chmod");
	chmod_item($GLOBALS['spx']["dir"], $GLOBALS['spx']["item"]);
break;
//------------------------------------------------------------------------------
// SEARCH FOR FILE(S)/DIR(S)
case "search":
	include_spip("action/spx_search");
	search_items($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// CREATE ARCHIVE
case "arch":
	include_spip("action/spx_archive");
	archive_items($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// USER-ADMINISTRATION
case "admin":
	include_spip("action/spx_admin");
	show_admin($GLOBALS['spx']["dir"]);
break;
//------------------------------------------------------------------------------
// DEFAULT: LIST FILES & DIRS
case "list":
default:
	include_spip("action/spx_list");
	list_dir($GLOBALS['spx']["dir"]);
//------------------------------------------------------------------------------
}				// end switch-statement
//------------------------------------------------------------------------------
show_footer();
//------------------------------------------------------------------------------
?>
