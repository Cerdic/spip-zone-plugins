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

     The Original Code is fun_up.php, released on 2003-03-31.

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
	File-Upload Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function upload_items($dir) {		// upload file
	if(($GLOBALS['spx']["permissions"]&01)!=01) show_error(_T('spixplorer:accessfunc'));
	
	// Execute
	if(_request("confirm")=="true") {	
		$cnt=count($GLOBALS['spx']['__FILES']['userfile']['name']);
		$err=false;
		$err_avaliable=isset($GLOBALS['spx']['__FILES']['userfile']['error']);
	
		// upload files & check for errors
		for($i=0;$i<$cnt;$i++) {
			$errors[$i]=NULL;
			$tmp = $GLOBALS['spx']['__FILES']['userfile']['tmp_name'][$i];
			$items[$i] = stripslashes($GLOBALS['spx']['__FILES']['userfile']['name'][$i]);
			if($err_avaliable) $up_err = $GLOBALS['spx']['__FILES']['userfile']['error'][$i];
			else $up_err=(file_exists($tmp)?0:4);
			$abs = get_abs_item($dir,$items[$i]);
		
			if($items[$i]=="" || $up_err==4) continue;
			if($up_err==1 || $up_err==2) {
				$errors[$i]=_T('spixplorer:miscfilesize');
				$err=true;	continue;
			}
			if($up_err==3) {
				$errors[$i]=_T('spixplorer:miscfilepart');
				$err=true;	continue;
			}
			if(!@is_uploaded_file($tmp)) {
				$errors[$i]=_T('spixplorer:uploadfile');
				$err=true;	continue;
			}
			if(@file_exists($abs)) {
				$errors[$i]=_T('spixplorer:itemdoesexist');
				$err=true;	continue;
			}
			
			// Upload
			if(function_exists("move_uploaded_file")) {
				$ok = @move_uploaded_file($tmp, $abs);
			} else {
				$ok = @copy($tmp, $abs);
				@unlink($tmp);	// try to delete...
			}
			
			if($ok===false) {
				$errors[$i]=_T('spixplorer:uploadfile');
				$err=true;	continue;
			}
		}
		
		if($err) {			// there were errors
			$err_msg="";
			for($i=0;$i<$cnt;$i++) {
				if($errors[$i]==NULL) continue;
				$err_msg .= $items[$i]." : ".$errors[$i]."<BR>\n";
			}
			show_error($err_msg);
		}
		
		header("Location: ".make_link("list",$dir,NULL));
		return;
	}
	
	show_header(_T('spixplorer:actupload'));
	
	// List
	echo "<BR><FORM enctype=\"multipart/form-data\" action=\"".make_link("upload",$dir,NULL);
	echo "\" method=\"post\">\n<INPUT type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"";
	echo get_max_file_size()."\"><INPUT type=\"hidden\" name=\"confirm\" value=\"true\"><TABLE>\n";
	for($i=0;$i<10;$i++) {
		echo "<TR><TD nowrap align=\"center\">";
		echo "<INPUT name=\"userfile[]\" type=\"file\" size=\"40\"></TD></TR>\n";
	}
	echo "</TABLE>\n<BR><TABLE><TR><TD><INPUT type=\"submit\" value=\""._T('spixplorer:btnupload');
	echo "\"></TD>\n<TD><input type=\"button\" value=\""._T('spixplorer:btncancel');
	echo "\" onClick=\"javascript:location='".make_link("list",$dir,NULL)."';\">\n</TD></TR></FORM></TABLE><BR>\n";
	
	return;
}
//------------------------------------------------------------------------------
?>
