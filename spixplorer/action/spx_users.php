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

     The Original Code is fun_users.php, released on 2003-03-31.

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
	Administrative Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function load_users() {
	include_spip("config/spx_.htusers");
}
//------------------------------------------------------------------------------
function save_users() {
	$cnt=count($GLOBALS['spx']["users"]);
	if($cnt>0) sort($GLOBALS['spx']["users"]);
	
	// Make PHP-File
	$content='<?php $GLOBALS['spx']["users"]=array(';
	for($i=0;$i<$cnt;++$i) {
		// if($GLOBALS['spx']["users"][6]&4==4) $GLOBALS['spx']["users"][6]=7;	// If admin, all permissions
		$content.="\r\n\tarray(\"".$GLOBALS['spx']["users"][$i][0].'","'.
			$GLOBALS['spx']["users"][$i][1].'","'.$GLOBALS['spx']["users"][$i][2].'","'.$GLOBALS['spx']["users"][$i][3].'",'.
			$GLOBALS['spx']["users"][$i][4].',"'.$GLOBALS['spx']["users"][$i][5].'",'.$GLOBALS['spx']["users"][$i][6].','.
			$GLOBALS['spx']["users"][$i][7].'),';
	}
	$content.="\r\n); ?>";
	
	// Write to File
	$fp = @fopen(_DIR_PLUGIN_SPIXPLORER . "config/spx_htusers.php", "w");
	if($fp===false) return false;	// Error
	fputs($fp,$content);
	fclose($fp);
	
	return true;
}
//------------------------------------------------------------------------------
function &find_user($user,$pass) {
	$cnt=count($GLOBALS['spx']["users"]);
	for($i=0;$i<$cnt;++$i) {
		if($user==$GLOBALS['spx']["users"][$i][0]) {
			if($pass==NULL || ($pass==$GLOBALS['spx']["users"][$i][1] &&
				$GLOBALS['spx']["users"][$i][7]))
			{
				return $GLOBALS['spx']["users"][$i];
			}
		}
	}
	
	return NULL;
}
//------------------------------------------------------------------------------
function activate_user($user,$pass) {
	$data=find_user($user,$pass);
	if($data==NULL) return false;
	
	// Set Login
	$GLOBALS['spx']['__SESSION']["s_user"]	= $data[0];
	$GLOBALS['spx']['__SESSION']["s_pass"]	= $data[1];
	$GLOBALS['spx']["home_dir"]	= $data[2];
	$GLOBALS['spx']["home_url"]	= $data[3];
	$GLOBALS['spx']["show_hidden"]	= $data[4];
	$GLOBALS['spx']["no_access"]	= $data[5];
	$GLOBALS['spx']["permissions"]	= $data[6];
	
	return true;
}
//------------------------------------------------------------------------------
function update_user($user,$new_data) {
	$data=&find_user($user,NULL);
	if($data==NULL) return false;
	
	$data=$new_data;
	return save_users();
}
//------------------------------------------------------------------------------
function add_user($data) {
	if(find_user($data[0],NULL)) return false;
	
	$GLOBALS['spx']["users"][]=$data;
	return save_users();
}
//------------------------------------------------------------------------------
function remove_user($user) {
	$data=&find_user($user,NULL);
	if($data==NULL) return false;
	
	// Remove
	$data=NULL;
	
	// Copy Valid Users
	$cnt=count($GLOBALS['spx']["users"]);
	for($i=0;$i<$cnt;++$i) {
		if($GLOBALS['spx']["users"][$i]!=NULL) $save_users[]=$GLOBALS['spx']["users"][$i];
	}
	$GLOBALS['spx']["users"]=$save_users;
	return save_users();
}
//------------------------------------------------------------------------------
/*
function num_users($active=true) {
	$cnt=count($GLOBALS['spx']["users"]);
	if(!$active) return $cnt;
	
	for($i=0, $j=0;$i<$cnt;++$i) {
		if($GLOBALS['spx']["users"][$i][7]) ++$j;
	}
	return $j;
}
*/
//------------------------------------------------------------------------------
?>
