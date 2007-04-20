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

     The Original Code is fun_list.php, released on 2003-03-31.

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
	Directory-Listing Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_spx_list()
{
	include_spip('inc/spx_init');
	list_dir($GLOBALS['spx']["dir"]);
}

//------------------------------------------------------------------------------
// HELPER FUNCTIONS (USED BY MAIN FUNCTION 'list_dir', SEE BOTTOM)
function make_list($_list1, $_list2) {		// make list of files
	$list = array();

	if($GLOBALS['spx']["srt"]=="yes") {
		$list1 = $_list1;
		$list2 = $_list2;
	} else {
		$list1 = $_list2;
		$list2 = $_list1;
	}
	
	if(is_array($list1)) {
		while (list($key, $val) = each($list1)) {
			$list[$key] = $val;
		}
	}
	
	if(is_array($list2)) {
		while (list($key, $val) = each($list2)) {
			$list[$key] = $val;
		}
	}
	
	return $list;
}
//------------------------------------------------------------------------------
function make_tables($dir, &$dir_list, &$file_list, &$tot_file_size, &$num_items)
{						// make table of files in dir
	// make tables & place results in reference-variables passed to function
	// also 'return' total filesize & total number of items
	
	$tot_file_size = $num_items = 0;
	
	// Open directory
	$handle = @opendir(get_abs_dir($dir));
	if($handle===false) show_error($dir.": "._T('spixplorer:opendir'));
	
	// Read directory
	while(($new_item = readdir($handle))!==false) {
		$abs_new_item = get_abs_item($dir, $new_item);
		
		if(!@file_exists($abs_new_item)) show_error($dir.": "._T('spixplorer:readdir'));
		if(!get_show_item($dir, $new_item)) continue;
		
		$new_file_size = filesize($abs_new_item);
		$tot_file_size += $new_file_size;
		$num_items++;
		
		if(get_is_dir($dir, $new_item)) {
			if($GLOBALS['spx']["order"]=="mod") {
				$dir_list[$new_item] =
					@filemtime($abs_new_item);
			} else {	// order == "size", "type" or "name"
				$dir_list[$new_item] = $new_item;
			}
		} else {
			if($GLOBALS['spx']["order"]=="size") {
				$file_list[$new_item] = $new_file_size;
			} elseif($GLOBALS['spx']["order"]=="mod") {
				$file_list[$new_item] =
					@filemtime($abs_new_item);
			} elseif($GLOBALS['spx']["order"]=="type") {
				$file_list[$new_item] =
					get_mime_type($dir, $new_item, "type");
			} else {	// order == "name"
				$file_list[$new_item] = $new_item;
			}
		}
	}
	closedir($handle);
	
	
	// sort
	if(is_array($dir_list)) {
		if($GLOBALS['spx']["order"]=="mod") {
			if($GLOBALS['spx']["srt"]=="yes") arsort($dir_list);
			else asort($dir_list);
		} else {	// order == "size", "type" or "name"
			if($GLOBALS['spx']["srt"]=="yes") ksort($dir_list);
			else krsort($dir_list);
		}
	}
	
	// sort
	if(is_array($file_list)) {
		if($GLOBALS['spx']["order"]=="mod") {
			if($GLOBALS['spx']["srt"]=="yes") arsort($file_list);
			else asort($file_list);
		} elseif($GLOBALS['spx']["order"]=="size" || $GLOBALS['spx']["order"]=="type") {
			if($GLOBALS['spx']["srt"]=="yes") asort($file_list);
			else arsort($file_list);
		} else {	// order == "name"
			if($GLOBALS['spx']["srt"]=="yes") ksort($file_list);
			else krsort($file_list);
		}
	}
}
//------------------------------------------------------------------------------
function print_table($dir, $list, $allow) {	// print table of files
	if(!is_array($list)) return;
	
	while(list($item,) = each($list)){
		// link to dir / file
		$stat = spx_stat($dir, $item);
		$target="";
		//$extra="";
		//if(is_link($stat['abs'])) $extra=" -> ".@readlink($stat['abs']);
		if ($stat['is_dir']) {
			$link = make_link("list", $stat['rel'], NULL);
		} else { //if($stat['edit'] || get_is_image($dir,$item)) {
//toggg			$link = $GLOBALS['spx']["home_url"]."/".$stat['rel'];
//toggg			$target = "_blank";
		} //else $link = "";
		
		echo '
<tr class="rowdata"><td><input type="checkbox" name="selitems[]" value="' .
htmlspecialchars($item) . '" onclick="javascript:Toggle(this);"></td>
' .
	// Icon + Link
'<td nowrap>' .
		/*if($link!="") */ '<a href="' . $link . '" target="' . $target . '">' .
		//else echo "<A>";
'<img border="0" width="16" height="16" ' .
/*toggg		echo "align=\"ABSMIDDLE\" */ 'src="plugins/spixplorer/_img/' .
			$stat['mime_img'] . '" alt="">&nbsp;' .
htmlspecialchars(strlen($item) > 50 ? substr($item, 0, 47) . '...' : $item) .
'</a></td>
' .	// ...$extra...
	// Size
'<td>' . parse_file_size($stat['size']) . '</td>
' .
	// Type
'<td>' . $stat['mime_type'] . '</td>
' .
	// Modified
'<td>' . parse_file_date($stat['mtime']) . '</td>
' .
	// Permissions
'<td>' .
		($allow ?
			'<a href="' . make_link("chmod", $dir, $item) . '" title="' .
			_T('spixplorer:permlink') . '">'
		: '') .
		parse_file_type($dir,$item) . parse_file_perms($stat['perms']) .
		($allow ? '</a>' : '') .
'</td>
' .
	// Owner/Group
'<td>' . $stat['owner'] . '/' . $stat['group'] . '</td>
' .
	// Actions
'<td>
<table>
' .
		// EDIT
		($stat['edit'] ?
			($allow ?
				'<td><a href="' . make_link("edit",$dir,$item) . '">' .
				'<img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="plugins/spixplorer/_img/_edit.gif" alt="' .
				_T('spixplorer:editlink') . '" title="' .
				_T('spixplorer:editlink') . '"></a></td>
'
			:
				'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="plugins/spixplorer/_img/_edit_.gif" alt="' .
				_T('spixplorer:editlink') . '" title="' .
				_T('spixplorer:editlink') . '"></td>
'
			)
		:
			'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
			'src="plugins/spixplorer/_img/_.gif" alt=""></td>
'
		) .
		// DOWNLOAD
		($stat['file'] ?
			($allow ?
				'<td><a href="' . make_link("down",$dir,$item) . '">' .
				'<img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="plugins/spixplorer/_img/_download.gif" alt="' . 
				_T('spixplorer:downlink') .
				'" title="' . _T('spixplorer:downlink') . '"></a></td>
'
			:
				'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="plugins/spixplorer/_img/_download_.gif" alt="' . 
				_T('spixplorer:downlink') .
				'" title="' . _T('spixplorer:downlink') . '"></td>
'
			)
		:
			'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
			'src="plugins/spixplorer/_img/_.gif" alt=""></td>
'
		) .
		'</table>
</td></tr>
';
	}
}
//------------------------------------------------------------------------------
// MAIN FUNCTION
function list_dir($dir) {			// list directory contents
	$allow=($GLOBALS['spx']["permissions"]&01)==01;
	$admin=((($GLOBALS['spx']["permissions"]&04)==04) || (($GLOBALS['spx']["permissions"]&02)==02));
	
	$dir_up = dirname($dir);
//	if($dir_up==".") $dir_up = "";
	
//	if(!get_show_item($dir_up,basename($dir))) show_error($dir." : "._T('spixplorer:accessdir'));
	
	// make file & dir tables, & get total filesize & number of items
	make_tables($dir, $dir_list, $file_list, $tot_file_size, $num_items);
	
	$s_dir=$dir;		if(strlen($s_dir)>50) $s_dir="...".substr($s_dir,-47);
	show_header(_T('spixplorer:actdir').": /".get_rel_item("",$s_dir), true);
	
	// Sorting of items
	$_img = 
'&nbsp;<img width="10" height="10" border="0" align="ABSMIDDLE" src="plugins/spixplorer/_img/';
	if($GLOBALS['spx']["srt"]=="yes") {
		$_srt = "no";	$_img .= "_arrowup.gif\" alt=\"^\">";
	} else {
		$_srt = "yes";	$_img .= "_arrowdown.gif\" alt=\"v\">";
	}
	
	// Toolbar
	echo "<br><table width=\"95%\"><tr><td><table><tr>\n";
	
	// PARENT DIR
	echo "<td><a href=\"".make_link("list",$dir_up,NULL)."\">";
	echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_up.gif\" ";
	echo "alt=\""._T('spixplorer:uplink')."\" title=\""._T('spixplorer:uplink')."\"></a></td>\n";
	// HOME DIR
	echo "<td><a href=\"".make_link("list",NULL,NULL)."\">";
	echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_home.gif\" ";
	echo "alt=\""._T('spixplorer:homelink')."\" title=\""._T('spixplorer:homelink')."\"></a></td>\n";
	// RELOAD
	echo "<td><a href=\"javascript:location.reload();\"><img border=\"0\" width=\"16\" height=\"16\" ";
	echo "align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_refresh.gif\" alt=\""._T('spixplorer:reloadlink');
	echo "\" title=\""._T('spixplorer:reloadlink')."\"></a></td>\n";
	// SEARCH
	echo "<td><a href=\"".make_link("search",$dir,NULL)."\">";
	echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_search.gif\" ";
	echo "alt=\""._T('spixplorer:searchlink')."\" title=\""._T('spixplorer:searchlink');
	echo "\"></a></td>\n";
	
	echo "<td>::</td>";
	
	if($allow) {
		// COPY
		echo "<td><a href=\"javascript:Copy();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_copy.gif\" alt=\""._T('spixplorer:copylink');
		echo "\" title=\""._T('spixplorer:copylink')."\"></a></td>\n";
		// MOVE
		echo "<td><a href=\"javascript:Move();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_move.gif\" alt=\""._T('spixplorer:movelink');
		echo "\" title=\""._T('spixplorer:movelink')."\"></a></td>\n";
		// DELETE
		echo "<td><a href=\"javascript:Delete();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_delete.gif\" alt=\""._T('spixplorer:dellink');
		echo "\" title=\""._T('spixplorer:dellink')."\"></a></td>\n";
		// UPLOAD
		if(get_cfg_var("file_uploads")) {
			echo "<td><a href=\"".make_link("up",$dir,NULL)."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
			echo "src=\"plugins/spixplorer/_img/_upload.gif\" alt=\""._T('spixplorer:uploadlink');
			echo "\" title=\""._T('spixplorer:uploadlink')."\"></a></td>\n";
		} else {
			echo "<td><img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
			echo "src=\"plugins/spixplorer/_img/_upload_.gif\" alt=\""._T('spixplorer:uploadlink');
			echo "\" title=\""._T('spixplorer:uploadlink')."\"></td>\n";
		}
		// ARCHIVE
		echo "<td><a href=\"javascript:Archive();\"><img border=\"0\" width=\"16\" height=\"16\" ";
		echo "align=\"ABSMIDDLE\" src=\"plugins/spixplorer/_img/_archive.gif\" alt=\""._T('spixplorer:comprlink');
		echo "\" title=\""._T('spixplorer:comprlink')."\"></a></td>\n";
	} else {
		// COPY
		echo "<td><img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
		echo "src=\"plugins/spixplorer/_img/_copy_.gif\" alt=\""._T('spixplorer:copylink')."\" title=\"";
		echo _T('spixplorer:copylink')."\"></td>\n";
		// MOVE
		echo "<td><img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
		echo "src=\"plugins/spixplorer/_img/_move_.gif\" alt=\""._T('spixplorer:movelink')."\" title=\"";
		echo _T('spixplorer:movelink')."\"></td>\n";
		// DELETE
		echo "<td><img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
		echo "src=\"plugins/spixplorer/_img/_delete_.gif\" alt=\""._T('spixplorer:dellink')."\" title=\"";
		echo _T('spixplorer:dellink')."\"></td>\n";
		// UPLOAD
		echo "<td><img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
		echo "src=\"plugins/spixplorer/_img/_upload_.gif\" alt=\""._T('spixplorer:uplink');
		echo "\" title=\""._T('spixplorer:uplink')."\"></td>\n";
	}
	
	// ADMIN & LOGOUT
	if($GLOBALS['spx']["require_login"]) {
		echo "<td>::</td>";
		// ADMIN
		if($admin) {
			echo "<td><a href=\"".make_link("admin",$dir,NULL)."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
			echo "src=\"plugins/spixplorer/_img/_admin.gif\" alt=\""._T('spixplorer:adminlink')."\" title=\"";
			echo _T('spixplorer:adminlink')."\"></a></td>\n";
		}
		// LOGOUT
		echo "<td><a href=\"".make_link("logout",NULL,NULL)."\">";
		echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
		echo "src=\"plugins/spixplorer/_img/_logout.gif\" alt=\""._T('spixplorer:logoutlink')."\" title=\"";
		echo _T('spixplorer:logoutlink')."\"></a></td>\n";
	}
	echo "</tr></table></td>\n";
	
	// Create File / Dir
	if($allow) {
		echo "<td align=\"right\"><table><FORM action=\"".make_link("mkitem",$dir,NULL)."\" method=\"post\" name=\"creaform\">\n<tr><td>";
		echo "<SELECT name=\"mktype\"><option value=\"file\">".$GLOBALS['spx']["mimes"]["file"]."</option>";
		echo "<option value=\"dir\">".$GLOBALS['spx']["mimes"]["dir"]."</option></SELECT>\n";
		echo "<input name=\"mkname\" type=\"text\" size=\"15\">";
		echo "<input type=\"submit\" value=\""._T('spixplorer:btncreate');
		echo "\"></td></tr></FORM></table></td>\n";
	}
	
	echo "</tr></table>\n";
	
	// End Toolbar
	
	
	// Begin Table + Form for checkboxes
	echo '
		<table width="95%"><FORM name="selform" method="POST" action="spip.php">
		<input type="hidden" name="action" id="action">
		<input type="hidden" name="do_action">
		<input type="hidden" name="namearch">
		<input type="hidden" name="arg">
		<input type="hidden" name="hash">
		<input type="hidden" name="first" value="y">
		<input type="hidden" name="dir" value="' . htmlentities($dir) .'">
		<input type="hidden" name="order" value="' .
			($GLOBALS['spx']["srt"] == 'no' ? '-' : '') . $GLOBALS['spx']["order"] .'">
		';
	foreach (array('del', 'archive', 'copy_move') as $act) {
		list($arg, $hash) = make_hash($act, $dir);
		echo '
			<input type="hidden" name="arg_' . $act . '" value="' . $arg . '">
			<input type="hidden" name="hash_' . $act . '" value="' . $hash . '">
			';
	}
	
	// Table Header
	echo "<tr><td colspan=\"8\"><HR></td></tr><tr><td width=\"2%\" class=\"header\">\n";
	echo "<input type=\"checkbox\" name=\"toggleAllC\" onclick=\"javascript:ToggleAll(this);\"></td>\n";
	echo "<td width=\"34%\" class=\"header\"><b>\n";
	if($GLOBALS['spx']["order"]=="name") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"name",$new_srt)."\">"._T('spixplorer:nameheader');
	if($GLOBALS['spx']["order"]=="name") echo $_img;
	echo "</a></b></td>\n<td width=\"10%\" class=\"header\"><b>";
	if($GLOBALS['spx']["order"]=="size") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"size",$new_srt)."\">"._T('spixplorer:sizeheader');
	if($GLOBALS['spx']["order"]=="size") echo $_img;
	echo "</a></b></td>\n<td width=\"16%\" class=\"header\"><b>";
	if($GLOBALS['spx']["order"]=="type") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"type",$new_srt)."\">"._T('spixplorer:typeheader');
	if($GLOBALS['spx']["order"]=="type") echo $_img;
	echo "</a></b></td>\n<td width=\"14%\" class=\"header\"><b>";
	if($GLOBALS['spx']["order"]=="mod") $new_srt = $_srt;	else $new_srt = "yes";
	echo "<a href=\"".make_link("list",$dir,NULL,"mod",$new_srt)."\">"._T('spixplorer:modifheader');
	if($GLOBALS['spx']["order"]=="mod") echo $_img;
	echo "</a></b></td><td width=\"8%\" class=\"header\"><b>"._T('spixplorer:permheader')."</b>\n";
	echo "</td><td width=\"10%\" class=\"header\"><b>"._T('spixplorer:owner_group')."</b></td>\n";
	echo "</td><td width=\"6%\" class=\"header\"><b>"._T('spixplorer:actionheader')."</b></td></tr>\n";
	echo "<tr><td colspan=\"8\"><HR></td></tr>\n";
		
	// make & print Table using lists
	print_table($dir, make_list($dir_list, $file_list), $allow);

	// print number of items & total filesize
	echo "<tr><td colspan=\"8\"><HR></td></tr><tr>\n<td class=\"header\"></td>";
	echo "<td class=\"header\">".$num_items." "._T('spixplorer:miscitems')." (";
	if(function_exists("disk_free_space")) {
		$free=parse_file_size(disk_free_space(get_abs_dir($dir)));
	} elseif(function_exists("diskfreespace")) {
		$free=parse_file_size(diskfreespace(get_abs_dir($dir)));
	} else $free="?";
	// echo "Total: ".parse_file_size(disk_total_space(get_abs_dir($dir))).", ";
	echo _T('spixplorer:miscfree').": ".$free.")</td>\n";
	echo "<td class=\"header\">".parse_file_size($tot_file_size)."</td>\n";
	for($i=0;$i<4;++$i) echo"<td class=\"header\"></td>";
	echo "</tr>\n<tr><td colspan=\"8\"><HR></td></tr></FORM></table>\n";
	
?><script language="JavaScript1.2" type="text/javascript">
<!--
	// Uncheck all items (to avoid problems with new items)
	var ml = document.selform;
	var len = ml.elements.length;
	for(var i=0; i<len; ++i) {
		var e = ml.elements[i];
		if(e.name == "selitems[]" && e.checked == true) {
			e.checked=false;
		}
	}
// -->
</script><?php
}
//------------------------------------------------------------------------------
?>
