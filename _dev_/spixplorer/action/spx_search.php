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

     The Original Code is fun_search.php, released on 2003-03-31.

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
	File-Search Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_spx_search()
{
	include_spip('inc/spx_init');
	search_items($GLOBALS['spx']["dir"]);
}

//------------------------------------------------------------------------------
function find_item($dir,$pat,&$list,$recur) {	// find items
	$handle=@opendir(get_abs_dir($dir));
	if($handle===false) return;		// unable to open dir
	
	while(($new_item=readdir($handle))!==false) {
		if(!@file_exists(get_abs_item($dir, $new_item))) continue;
		if(!get_show_item($dir, $new_item)) continue;
		
		// match?
		if(@eregi($pat,$new_item)) $list[]=array($dir,$new_item);
		
		// search sub-directories
		if(get_is_dir($dir, $new_item) && $recur) {
			find_item(get_rel_item($dir,$new_item),$pat,$list,$recur);
		}
	}
	
	closedir($handle);
}
//------------------------------------------------------------------------------
function make_list($dir,$item,$subdir) {	// make list of found items
	// convert shell-wildcards to PCRE Regex Syntax
	$pat="^".str_replace("?",".",str_replace("*",".*",str_replace(".","\.",$item)))."$";
	
	// search
	find_item($dir,$pat,$list,$subdir);
	if(is_array($list)) sort($list);
	return $list;
}
//------------------------------------------------------------------------------
function print_table($list) {			// print table of found items
	if(!is_array($list)) return;
	
	$cnt = count($list);
	for($i=0;$i<$cnt;++$i) {
		$dir = $list[$i][0];	$item = $list[$i][1];
		$s_dir=$dir;	if(strlen($s_dir)>65) $s_dir=substr($s_dir,0,62)."...";
		$s_item=$item;	if(strlen($s_item)>45) $s_item=substr($s_item,0,42)."...";
		$link = "";	$target = "";
		
		if(get_is_dir($dir,$item)) {
			$img = "dir.gif";
			$link = make_link("list",get_rel_item($dir, $item),NULL);
		} else {
			$img = get_mime_type($dir, $item, "img");
			//if(get_is_editable($dir,$item) || get_is_image($dir,$item)) {
				$link = $GLOBALS['spx']["home_url"]."/".get_rel_item($dir, $item);
				$target = "_blank";
			//}
		}
		
		echo "<TR><TD>" . "<IMG border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/" . $img . "\" ALT=\"\">&nbsp;";
		/*if($link!="")*/ echo"<A HREF=\"".$link."\" TARGET=\"".$target."\">";
		//else echo "<A>";
		echo $s_item."</A></TD><TD><A HREF=\"" . make_link("list",$dir,NULL)."\"> /";
		echo $s_dir."</A></TD></TR>\n";
	}
}
//------------------------------------------------------------------------------
function search_items($dir) {			// search for item
	set_time_limit(0);
	if($searchitem = _request('searchitem')) {
		$searchitem = stripslashes($searchitem);
		$subdir = (_request("subdir")=="y");
		$list = make_list($dir,$searchitem,$subdir);
	} else {
		$searchitem=NULL;
		$subdir=true;
	}
	
	$msg=_T('spixplorer:actsearchresults');
	if($searchitem!=NULL) $msg.=": (/" . get_rel_item($dir, $searchitem).")";
	show_header($msg);
	
	// Search Box
	echo "<BR><TABLE><FORM name=\"searchform\" action=\"".make_link("search",$dir,NULL);
	echo "\" method=\"post\">\n<TR><TD><INPUT name=\"searchitem\" type=\"text\" size=\"25\" value=\"";
	echo $searchitem."\"><INPUT type=\"submit\" value=\""._T('spixplorer:btnsearch');
	echo "\">&nbsp;<input type=\"button\" value=\""._T('spixplorer:btnclose');
	echo "\" onClick=\"javascript:location='".make_link("list",$dir,NULL);
	echo "';\"></TD></TR><TR><TD><INPUT type=\"checkbox\" name=\"subdir\" value=\"y\"";
	echo ($subdir?" checked>":">")._T('spixplorer:miscsubdirs')."</TD></TR></FORM></TABLE>\n";
	
	// Results
	if($searchitem!=NULL) {
		echo "<TABLE width=\"95%\"><TR><TD colspan=\"2\"><hr /></TD></TR>\n";
		if(count($list)>0) {
			// Table Header
			echo "<TR>\n<TD WIDTH=\"42%\" class=\"header\"><B>"._T('spixplorer:nameheader');
			echo "</B></TD>\n<TD WIDTH=\"58%\" class=\"header\"><B>"._T('spixplorer:pathheader');
			echo "</B></TD></TR>\n<TR><TD colspan=\"2\"><hr /></TD></TR>\n";
	
			// make & print table of found items
			print_table($list);

			echo "<TR><TD colspan=\"2\"><hr /></TD></TR>\n<TR><TD class=\"header\">".count($list)." ";
			echo _T('spixplorer:miscitems').".</TD><TD class=\"header\"></TD></TR>\n";
		} else {
			echo "<TR><TD>"._T('spixplorer:miscnoresult')."</TD></TR>";
		}
		echo "<TR><TD colspan=\"2\"><hr /></TD></TR></TABLE>\n";
	}
?><script language="JavaScript1.2" type="text/javascript">
<!--
	if(document.searchform) document.searchform.searchitem.focus();
// -->
</script><?php
}
//------------------------------------------------------------------------------
?>
