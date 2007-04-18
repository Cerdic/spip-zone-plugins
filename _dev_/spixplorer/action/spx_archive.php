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

     The Original Code is fun_archive.php, released on 2003-03-31.

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
	Zip, Tar & Gzip Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
if($GLOBALS['spx']["zip"]) include_spip("inc/spx_lib_zip");
//if($GLOBALS['spx']["tar"]) include_spip("inc/spx_lib_tar");
//if($GLOBALS['spx']["tgz"]) include_spip("inc/spx_lib_tgz");
//------------------------------------------------------------------------------
function zip_items($dir,$name) {
	$cnt=count($GLOBALS['spx']['__POST']["selitems"]);
	$abs_dir=get_abs_dir($dir);
	
	$zipfile=new ZipFile();
	for($i=0;$i<$cnt;++$i) {
		$selitem=stripslashes($GLOBALS['spx']['__POST']["selitems"][$i]);
		if(!$zipfile->add($abs_dir,$selitem)) {
			show_error($selitem.": Failed adding item.");
		}
	}
	if(!$zipfile->save(get_abs_item($dir,$name))) {
		show_error($name.": Failed saving zipfile.");
	}
	
	header("Location: ".make_link("list",$dir,NULL));
}
//------------------------------------------------------------------------------
function tar_items($dir,$name) {
	// ...
}
//------------------------------------------------------------------------------
function tgz_items($dir,$name) {
	// ...
}
//------------------------------------------------------------------------------
function archive_items($dir) {
	if(($GLOBALS['spx']["permissions"]&01)!=01) show_error(_T('spixplorer:"accessfunc"'));
	if(!$GLOBALS['spx']["zip"] && !$GLOBALS['spx']["tar"] && !$GLOBALS['spx']["tgz"]) show_error(_T('spixplorer:"miscnofunc"'));
	
	if(isset($GLOBALS['spx']['__POST']["name"])) {
		$name=basename(stripslashes($GLOBALS['spx']['__POST']["name"]));
		if($name=="") show_error(_T('spixplorer:"miscnoname"'));
		switch($GLOBALS['spx']['__POST']["type"]) {
			case "zip":	zip_items($dir,$name);	break;
			case "tar":	tar_items($dir,$name);	break;
			default:		tgz_items($dir,$name);
		}
		header("Location: ".make_link("list",$dir,NULL));
	}
	
	show_header(_T('spixplorer:"actarchive"'));
	echo "<BR><FORM name=\"archform\" method=\"post\" action=\"".make_link("arch",$dir,NULL)."\">\n";
	
	$cnt=count($GLOBALS['spx']['__POST']["selitems"]);
	for($i=0;$i<$cnt;++$i) {
		echo "<INPUT type=\"hidden\" name=\"selitems[]\" value=\"".stripslashes($GLOBALS['spx']['__POST']["selitems"][$i])."\">\n";
	}
	
	echo "<TABLE width=\"300\"><TR><TD>"._T('spixplorer:"nameheader"').":</TD><TD align=\"right\">";
	echo "<INPUT type=\"text\" name=\"name\" size=\"25\"></TD></TR>\n";
	echo "<TR><TD>"._T('spixplorer:"typeheader"').":</TD><TD align=\"right\"><SELECT name=\"type\">\n";
	if($GLOBALS['spx']["zip"]) echo "<OPTION value=\"zip\">Zip</OPTION>\n";
	if($GLOBALS['spx']["tar"]) echo "<OPTION value=\"tar\">Tar</OPTION>\n";
	if($GLOBALS['spx']["tgz"]) echo "<OPTION value=\"tgz\">TGz</OPTION>\n";
	echo "</SELECT></TD></TR>";
	echo "<TR><TD></TD><TD align=\"right\"><INPUT type=\"submit\" value=\""._T('spixplorer:"btncreate"')."\">\n";
	echo "<input type=\"button\" value=\""._T('spixplorer:"btncancel"');
	echo "\" onClick=\"javascript:location='".make_link("list",$dir,NULL)."';\">\n</TD></TR></FORM></TABLE><BR>\n";
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.archform) document.archform.name.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
?>
