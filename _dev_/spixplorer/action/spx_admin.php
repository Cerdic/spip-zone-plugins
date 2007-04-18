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

     The Original Code is fun_admin.php, released on 2003-03-31.

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
function admin($admin, $dir) {			// Change Password & Manage Users Form
	show_header(_T('spixplorer:"actadmin"'));
	
	// Javascript functions:
	include_spip("inc/spx_js_admin");
	
	// Change Password
	echo "<BR><HR width=\"95%\"><TABLE width=\"350\"><TR><TD colspan=\"2\" class=\"header\"><B>";
	echo _T('spixplorer:"actchpwd"').":</B></TD></TR>\n";
	echo "<FORM name=\"chpwd\" action=\"".make_link("admin",$dir,NULL)."\" method=\"post\">\n";
	echo "<INPUT type=\"hidden\" name=\"action2\" value=\"chpwd\">\n";
	echo "<TR><TD>"._T('spixplorer:"miscoldpass"').": </TD><TD align=\"right\">";
	echo "<INPUT type=\"password\" name=\"oldpwd\" size=\"25\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscnewpass"').": </TD><TD align=\"right\">";
	echo "<INPUT type=\"password\" name=\"newpwd1\" size=\"25\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscconfnewpass"').": </TD><TD align=\"right\">";
	echo "<INPUT type=\"password\" name=\"newpwd2\" size=\"25\"></TD></TR>\n";
	echo "<TR><TD colspan=\"2\" align=\"right\"><INPUT type=\"submit\" value=\""._T('spixplorer:"btnchange"');
	echo "\" onClick=\"return check_pwd();\">\n</TD></TR></FORM></TABLE>\n";
	
	// Edit / Add / Remove User
	if($admin) {
		echo "<HR width=\"95%\"><TABLE width=\"350\"><TR><TD colspan=\"6\" class=\"header\" nowrap>";
		echo "<B>"._T('spixplorer:"actusers"').":</B></TD></TR>\n";
		echo "<TR><TD colspan=\"5\">"._T('spixplorer:"miscuseritems"')."</TD></TR>\n";
		echo "<FORM name=\"userform\" action=\"".make_link("admin",$dir,NULL)."\" method=\"post\">\n";
		echo "<INPUT type=\"hidden\" name=\"action2\" value=\"edituser\">\n";
		$cnt=count($GLOBALS['spx']["users"]);
		for($i=0;$i<$cnt;++$i) {
			// Username & Home dir:
			$user=$GLOBALS['spx']["users"][$i][0];	if(strlen($user)>15) $user=substr($user,0,12)."...";
			$home=$GLOBALS['spx']["users"][$i][2];	if(strlen($home)>30) $home=substr($home,0,27)."...";
			
			echo "<TR><TD width=\"1%\"><INPUT TYPE=\"radio\" name=\"user\" value=\"";
			echo $GLOBALS['spx']["users"][$i][0]."\"".(($i==0)?" checked":"")."></TD>\n";
			echo "<TD width=\"30%\">".$user."</TD><TD width=\"60%\">".$home."</TD>\n";
			echo "<TD width=\"3%\">".($GLOBALS['spx']["users"][$i][4]?_T('spixplorer:"miscyesno"')[2]:
				_T('spixplorer:"miscyesno"')[3])."</TD>\n";
			echo "<TD width=\"3%\">".$GLOBALS['spx']["users"][$i][6]."</TD>\n";
			echo "<TD width=\"3%\">".($GLOBALS['spx']["users"][$i][7]?_T('spixplorer:"miscyesno"')[2]:
				_T('spixplorer:"miscyesno"')[3])."</TD></TR>\n";
		}
		echo "<TR><TD colspan=\"6\" align=\"right\">";
		echo "<input type=\"button\" value=\""._T('spixplorer:"btnadd"');
		echo "\" onClick=\"javascript:location='".make_link("admin",$dir,NULL)."&action2=adduser';\">\n";
		echo "<input type=\"button\" value=\""._T('spixplorer:"btnedit"');
		echo "\" onClick=\"javascript:Edit();\">\n";
		echo "<input type=\"button\" value=\""._T('spixplorer:"btnremove"');
		echo "\" onClick=\"javascript:Delete();\">\n</TD></TR></FORM></TABLE>\n";
	}
	
	echo "<HR width=\"95%\"><input type=\"button\" value=\""._T('spixplorer:"btnclose"');
	echo "\" onClick=\"javascript:location='".make_link("list",$dir,NULL)."';\"><BR><BR>\n";
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.chpwd) document.chpwd.oldpwd.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
function changepwd($dir) {			// Change Password
	$pwd=md5(stripslashes($GLOBALS['spx']['__POST']["oldpwd"]));
	if($GLOBALS['spx']['__POST']["newpwd1"]!=$GLOBALS['spx']['__POST']["newpwd2"]) show_error(_T('spixplorer:"miscnopassmatch"'));
	
	$data=find_user($GLOBALS['spx']['__SESSION']["s_user"],$pwd);
	if($data==NULL) show_error(_T('spixplorer:"miscnouserpass"'));
	
	$data[1]=md5(stripslashes($GLOBALS['spx']['__POST']["newpwd1"]));
	if(!update_user($data[0],$data)) show_error($data[0].": "._T('spixplorer:"chpass"'));
	activate_user($data[0],NULL);
	
	header("location: ".make_link("list",$dir,NULL));
}
//------------------------------------------------------------------------------
function adduser($dir) {			// Add User
	if(_request("confirm")=="true") {
		$user=stripslashes($GLOBALS['spx']['__POST']["user"]);
		if($user=="" || $GLOBALS['spx']['__POST']["home_dir"]=="") {
			show_error(_T('spixplorer:"miscfieldmissed"'));
		}
		if($GLOBALS['spx']['__POST']["pass1"]!=$GLOBALS['spx']['__POST']["pass2"]) show_error(_T('spixplorer:"miscnopassmatch"'));
		$data=find_user($user,NULL);
		if($data!=NULL) show_error($user.": "._T('spixplorer:"miscuserexist"'));
		
		$data=array($user,md5(stripslashes($GLOBALS['spx']['__POST']["pass1"])),
			stripslashes($GLOBALS['spx']['__POST']["home_dir"]),stripslashes($GLOBALS['spx']['__POST']["home_url"]),
			$GLOBALS['spx']['__POST']["show_hidden"],stripslashes($GLOBALS['spx']['__POST']["no_access"]),
			$GLOBALS['spx']['__POST']["permissions"],$GLOBALS['spx']['__POST']["active"]);
			
		if(!add_user($data)) show_error($user.": "._T('spixplorer:"adduser"'));
		header("location: ".make_link("admin",$dir,NULL));
		return;
	}
	
	show_header(_T('spixplorer:"actadmin"').": "._T('spixplorer:"miscadduser"'));
	
	// Javascript functions:
	include_spip("inc/spx_js_admin2");
	
	echo "<FORM name=\"adduser\" action=\"".make_link("admin",$dir,NULL)."&action2=adduser\" method=\"post\">\n";
	echo "<INPUT type=\"hidden\" name=\"confirm\" value=\"true\"><BR><TABLE width=\"450\">\n";
	echo "<TR><TD>"._T('spixplorer:"miscusername"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"user\" size=\"30\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscpassword"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"password\" name=\"pass1\" size=\"30\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscconfpass"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"password\" name=\"pass2\" size=\"30\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"mischomedir"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"home_dir\" size=\"30\" value=\"";
		echo $GLOBALS['spx']["home_dir"]."\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"mischomeurl"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"home_url\" size=\"30\" value=\"";
		echo $GLOBALS['spx']["home_url"]."\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscshowhidden"').":</TD>";
		echo "<TD align=\"right\"><SELECT name=\"show_hidden\">\n";
		echo "<OPTION value=\"0\">"._T('spixplorer:"miscyesno"')[1]."</OPTION>";
		echo "<OPTION value=\"1\">"._T('spixplorer:"miscyesno"')[0]."</OPTION>\n";
		echo "</SELECT></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"mischidepattern"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"no_access\" size=\"30\" value=\"^\\.ht\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscperms"').":</TD><TD align=\"right\"><SELECT name=\"permissions\">\n";
		$permvalues = array(0,1,2,3,7);
		for($i=0;$i<count(_T('spixplorer:"miscpermnames"'));++$i) {
			echo "<OPTION value=\"".$permvalues[$i]."\">";
			echo _T('spixplorer:"miscpermnames"')[$i]."</OPTION>\n";
		}
		echo "</SELECT></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscactive"').":</TD>";
		echo "<TD align=\"right\"><SELECT name=\"active\">\n";
		echo "<OPTION value=\"1\">"._T('spixplorer:"miscyesno"')[0]."</OPTION>";
		echo "<OPTION value=\"0\">"._T('spixplorer:"miscyesno"')[1]."</OPTION>\n";
		echo "</SELECT></TD></TR>\n";
	echo "<TR><TD colspan=\"2\" align=\"right\"><input type=\"submit\" value=\""._T('spixplorer:"btnadd"');
		echo "\" onClick=\"return check_pwd();\">\n<input type=\"button\" value=\"";
		echo _T('spixplorer:"btncancel"')."\" onClick=\"javascript:location='";
		echo make_link("admin",$dir,NULL)."';\"></TD></TR></FORM></TABLE><BR>\n";
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.adduser) document.adduser.user.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
function edituser($dir) {			// Edit User
	$user=stripslashes($GLOBALS['spx']['__POST']["user"]);
	$data=find_user($user,NULL);
	if($data==NULL) show_error($user.": "._T('spixplorer:"miscnofinduser"'));
	if($self=($user==$GLOBALS['spx']['__SESSION']["s_user"])) $dir="";
	
	if(_request("confirm")=="true") {
		$nuser=stripslashes($GLOBALS['spx']['__POST']["nuser"]);
		if($nuser=="" || $GLOBALS['spx']['__POST']["home_dir"]=="") {
			show_error(_T('spixplorer:"miscfieldmissed"'));
		}
		if(_request("chpass")=="true")
		{
			if($GLOBALS['spx']['__POST']["pass1"]!=$GLOBALS['spx']['__POST']["pass2"]) show_error(_T('spixplorer:"miscnopassmatch"'));
			$pass=md5(stripslashes($GLOBALS['spx']['__POST']["pass1"]));
		} else $pass=$data[1];
		
		if($self) $GLOBALS['spx']['__POST']["active"]=1;
		
		$data=array($nuser,$pass,stripslashes($GLOBALS['spx']['__POST']["home_dir"]),
			stripslashes($GLOBALS['spx']['__POST']["home_url"]),$GLOBALS['spx']['__POST']["show_hidden"],
			stripslashes($GLOBALS['spx']['__POST']["no_access"]),$GLOBALS['spx']['__POST']["permissions"],$GLOBALS['spx']['__POST']["active"]);
			
		if(!update_user($user,$data)) show_error($user.": "._T('spixplorer:"saveuser"'));
		if($self) activate_user($nuser,NULL);
		
		header("location: ".make_link("admin",$dir,NULL));
		return;
	}
	
	show_header(_T('spixplorer:"actadmin"').": ".sprintf(_T('spixplorer:"miscedituser"'),$data[0]));
	
	// Javascript functions:
	include_spip("inc/spx_js_admin3");
	
	echo "<FORM name=\"edituser\" action=\"".make_link("admin",$dir,NULL)."&action2=edituser\" method=\"post\">\n";
	echo "<INPUT type=\"hidden\" name=\"confirm\" value=\"true\"><INPUT type=\"hidden\" name=\"user\" value=\"".$data[0]."\">\n";
	echo "<BR><TABLE width=\"450\">\n";
	echo "<TR><TD>"._T('spixplorer:"miscusername"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type\"text\" name=\"nuser\" size=\"30\" value=\"";
		echo $data[0]."\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscconfpass"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"password\" name=\"pass1\" size=\"30\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscconfnewpass"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"password\" name=\"pass2\" size=\"30\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscchpass"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"checkbox\" name=\"chpass\" value=\"true\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"mischomedir"').":</TD>\n";	
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"home_dir\" size=\"30\" value=\"";
		echo $data[2]."\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"mischomeurl"').":</TD>\n";	
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"home_url\" size=\"30\" value=\"";
		echo $data[3]."\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscshowhidden"').":</TD>";
		echo "<TD align=\"right\"><SELECT name=\"show_hidden\">\n";
		echo "<OPTION value=\"0\">"._T('spixplorer:"miscyesno"')[1]."</OPTION>";
		echo "<OPTION value=\"1\"".($data[4]?" selected ":"").">";
		echo _T('spixplorer:"miscyesno"')[0]."</OPTION>\n";
		echo "</SELECT></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"mischidepattern"').":</TD>\n";
		echo "<TD align=\"right\"><INPUT type=\"text\" name=\"no_access\" size=\"30\" value=\"";
		echo $data[5]."\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscperms"').":</TD><TD align=\"right\"><SELECT name=\"permissions\">\n";
		$permvalues = array(0,1,2,3,7);
		for($i=0;$i<count(_T('spixplorer:"miscpermnames"'));++$i) {
			echo "<OPTION value=\"".$permvalues[$i]."\"".($permvalues[$i]==$data[6]?" selected ":"").">";
			echo _T('spixplorer:"miscpermnames"')[$i]."</OPTION>\n";
		}
		echo "</SELECT></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"miscactive"').":</TD>";
		echo "<TD align=\"right\"><SELECT name=\"active\"".($self?" DISABLED ":"").">\n";
		echo "<OPTION value=\"1\">"._T('spixplorer:"miscyesno"')[0]."</OPTION>";
		echo "<OPTION value=\"0\"".($data[7]?"":" selected ").">";
		echo _T('spixplorer:"miscyesno"')[1]."</OPTION>\n";
		echo "</SELECT></TD></TR>\n";
	echo "<TR><TD colspan=\"2\" align=\"right\"><input type=\"submit\" value=\""._T('spixplorer:"btnsave"');
		echo "\" onClick=\"return check_pwd();\">\n<input type=\"button\" value=\"";
		echo _T('spixplorer:"btncancel"')."\" onClick=\"javascript:location='";
		echo make_link("admin",$dir,NULL)."';\"></TD></TR></FORM></TABLE><BR>\n";
}
//------------------------------------------------------------------------------
function removeuser($dir) {			// Remove User
	$user=stripslashes($GLOBALS['spx']['__POST']["user"]);
	if($user==$GLOBALS['spx']['__SESSION']["s_user"]) show_error(_T('spixplorer:"miscselfremove"'));
	if(!remove_user($user)) show_error($user.": "._T('spixplorer:"deluser"'));
	
	header("location: ".make_link("admin",$dir,NULL));
}
//------------------------------------------------------------------------------
function show_admin($dir) {			// Execute Admin Action
	$pwd=(($GLOBALS['spx']["permissions"]&2)==2);
	$admin=(($GLOBALS['spx']["permissions"]&4)==4);
	
	if(!$GLOBALS['spx']["require_login"]) show_error(_T('spixplorer:"miscnofunc"'));
	if(!$pwd && !$admin) show_error(_T('spixplorer:"accessfunc"'));
	
	if(isset($GLOBALS['spx']['__GET']["action2"])) $action2 = $GLOBALS['spx']['__GET']["action2"];
	elseif() $action2 = _request("action2");
	else $action2="";
	
	switch($action2) {
	case "chpwd":
		changepwd($dir);
	break;
	case "adduser":
		if(!$admin) show_error(_T('spixplorer:"accessfunc"'));
		adduser($dir);
	break;
	case "edituser":
		if(!$admin) show_error(_T('spixplorer:"accessfunc"'));
		edituser($dir);
	break;
	case "rmuser":
		if(!$admin) show_error(_T('spixplorer:"accessfunc"'));
		removeuser($dir);
	break;
	default:
		admin($admin,$dir);
	}
}
//------------------------------------------------------------------------------
?>
