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

     The Original Code is fun_del.php, released on 2003-03-31.

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
	File-Delete Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function del_items($dir) {		// delete files/dirs
	if(($GLOBALS['spx']["permissions"]&01)!=01) show_error(_T('spixplorer:"accessfunc"'));
	
	$cnt=count($GLOBALS['spx']['__POST']["selitems"]);
	$err=false;
	
	// delete files & check for errors
	for($i=0;$i<$cnt;++$i) {
		$items[$i] = stripslashes($GLOBALS['spx']['__POST']["selitems"][$i]);
		$abs = get_abs_item($dir,$items[$i]);
	
		if(!@file_exists(get_abs_item($dir, $items[$i]))) {
			$error[$i]=_T('spixplorer:"itemexist"');
			$err=true;	continue;
		}
		if(!get_show_item($dir, $items[$i])) {
			$error[$i]=_T('spixplorer:"accessitem"');
			$err=true;	continue;
		}
		
		// Delete
		$ok=remove(get_abs_item($dir,$items[$i]));
		
		if($ok===false) {
			$error[$i]=_T('spixplorer:"delitem"');
			$err=true;	continue;
		}
		
		$error[$i]=NULL;
	}
	
	if($err) {			// there were errors
		$err_msg="";
		for($i=0;$i<$cnt;++$i) {
			if($error[$i]==NULL) continue;
			
			$err_msg .= $items[$i]." : ".$error[$i]."<BR>\n";
		}
		show_error($err_msg);
	}
	
	header("Location: ".make_link("list",$dir,NULL));
}
//------------------------------------------------------------------------------
?>
