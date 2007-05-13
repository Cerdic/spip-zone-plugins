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
	list_dir($GLOBALS['spx']['dir']);
	exit;
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
		
// est-il nécessaire de mourrir si on a un telle erreur ? On pourrait mettre le ligne en rouge ...
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
		} elseif($stat['edit'] || get_is_image($dir, $item)) {
//toggg			$link = $GLOBALS['spx']["home_url"]."/".$stat['rel'];
			$link = make_link("show", $dir, $item);
		} else {
			$link = "";
		}
		
		echo '
<tr class="rowdata"><td><input type="checkbox" name="selitems[]" value="' .
htmlspecialchars($item) . '" class="selitem" /></td>
' .
	// Icon + Link
'<td nowrap>' .
		/*if($link!="") */ '<a href="' . $link . '" target="' . $target . '">' .
		//else echo "<A>";
'<img border="0" width="16" height="16" ' .
/*toggg		echo "align=\"ABSMIDDLE\" */ 'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/' .
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
				'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_edit.gif" alt="' .
				_T('spixplorer:editlink') . '" title="' .
				_T('spixplorer:editlink') . '"></a></td>
'
			:
				'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_edit_.gif" alt="' .
				_T('spixplorer:editlink') . '" title="' .
				_T('spixplorer:editlink') . '"></td>
'
			)
		:
			'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
			'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_.gif" alt=""></td>
'
		) .
		// DOWNLOAD
		($stat['file'] ?
			($allow ?
				'<td><a href="' . make_link("down",$dir,$item) . '">' .
				'<img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_download.gif" alt="' . 
				_T('spixplorer:downlink') .
				'" title="' . _T('spixplorer:downlink') . '"></a></td>
'
			:
				'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
				'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_download_.gif" alt="' . 
				_T('spixplorer:downlink') .
				'" title="' . _T('spixplorer:downlink') . '"></td>
'
			)
		:
			'<td><img border="0" width="16" height="16" ' . //toggg align=\"ABSMIDDLE\" ";
			'src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_.gif" alt=""></td>
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
	if($dir_up == '.') {
		$dir_up = '';
	}
	
//	if(!get_show_item($dir_up,basename($dir))) show_error($dir." : "._T('spixplorer:accessdir'));
	
	// make file & dir tables, & get total filesize & number of items
	make_tables($dir, $dir_list, $file_list, $tot_file_size, $num_items);
	
	$s_dir=$dir;		if(strlen($s_dir)>50) $s_dir="...".substr($s_dir,-47);
	show_header('', true);
	
	// Sorting of items
	$_img = 
'&nbsp;<img width="10" height="10" border="0" align="ABSMIDDLE" src="' . _DIR_PLUGIN_SPIXPLORER . '_img/';
	if ($GLOBALS['spx']['srt'] == 'yes') {
		$_srt = 'no';	$_img .= '_arrowup.gif" alt="^">';
	} else {
		$_srt = 'yes';	$_img .= '_arrowdown.gif" alt="v">';
	}
	
	// Toolbar
	echo '<div class="toolbar" width="95%">
' .
	// SEARCH
'<span>' . btn_link('search', $dir) . '</span>
	
<span>::</span>' .
	
	($allow ?
		// COPY
'<span>' . btn_link('copy', $dir, null, 'javascript:Copy();') . '</span>
' .
		// DELETE
'<span>' . btn_link('del', $dir, null, 'javascript:Delete();') . '</span>
' .
		// MOVE
'<span>' . btn_link('move', $dir, null, 'javascript:Move();') . '</span>
' .
		// UPLOAD
		(get_cfg_var("file_uploads") ?
'<span>' . btn_link('up', $dir) . '</span>
'
		:
'<span class="inactif">' . btn_link('up', $dir) . '</span>
'
		) .
		// ARCHIVE
'<span>' . btn_link('archive', $dir, null, 'javascript:Archive();') . '</span>
'
	:
		// COPY
'<span><img border="0" width="16" height="16" align="ABSMIDDLE"
src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_copy_.gif" alt="' . _T('spixplorer:copylink') .
'" title="' . _T('spixplorer:copylink') . '"></span>
' .
		// MOVE
'<span><img border="0" width="16" height="16" align="ABSMIDDLE"
src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_move_.gif" alt="' . _T('spixplorer:movelink') .
'" title="' . _T('spixplorer:movelink') . '"></span>
' .
		// DELETE
'<span><img border="0" width="16" height="16" align="ABSMIDDLE"
src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_delete_.gif" alt="' . _T('spixplorer:dellink') .
'" title="' . _T('spixplorer:dellink') . '"></span>
' .
		// UPLOAD
'<span><img border="0" width="16" height="16" align="ABSMIDDLE"
src="' . _DIR_PLUGIN_SPIXPLORER . '_img/_upload_.gif" alt="' . _T('spixplorer:uplink') .
'" title="' . _T('spixplorer:uplink') . '"></span>
'
	);
	
	// ADMIN & LOGOUT (************** Non utilise pour l'instant ************)
	if($GLOBALS['spx']["require_login"]) {
		echo "<span>::</span>";
		// ADMIN
		if($admin) {
			echo "<span><a href=\"".make_link("admin",$dir,NULL)."\">";
			echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
			echo "src=\"plugins/spixplorer/_img/_admin.gif\" alt=\""._T('spixplorer:adminlink')."\" title=\"";
			echo _T('spixplorer:adminlink')."\"></a></span>\n";
		}
		// LOGOUT
		echo "<span><a href=\"".make_link("logout",NULL,NULL)."\">";
		echo "<img border=\"0\" width=\"16\" height=\"16\" align=\"ABSMIDDLE\" ";
		echo "src=\"plugins/spixplorer/_img/_logout.gif\" alt=\""._T('spixplorer:logoutlink')."\" title=\"";
		echo _T('spixplorer:logoutlink')."\"></a></span>\n";
	}
//	echo "</span>\n";
	
	// Create File / Dir
	if($allow) {
		list($arg, $hash) = make_hash('mkitem', $dir);
		echo
'<FORM action="spip.php" method="post" name="creaform"><span align="right">
<input type="hidden" name="action" value="spx_mkitem" />
<input type="hidden" name="arg" value="' . $arg . '" />
<input type="hidden" name="hash" value="' . $hash . '" />
<input type="hidden" name="dir" value="' . $dir . '" />
<SELECT name="mktype">
<option value="file">' . $GLOBALS['spx']['mimes']['file'] . '</option>
<option value="dir">' . $GLOBALS['spx']['mimes']['dir'] . '</option>
<option value="archive">' . _T('spixplorer:comprlink') . '</option>
</SELECT>
<input name="mkname" type="text" size="15" />
<input type="submit" value="' . _T('spixplorer:btncreate') . '" />
</span></FORM>
';
	}
	
	echo '</div>
';
	
	// End Toolbar
	
	
	// Begin Table + Form for checkboxes
	echo '
		<FORM name="selform" method="POST" action="spip.php" id="selform">
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
	echo
'<table width="95%"><tr><td colspan="8"><hr /></td></tr><tr><td width="2%" class="header">
<input type="checkbox" name="toggleAllC" id="toggle_all"></td>
<td class="header"><b><a href="' . make_link("list", $dir, NULL, "name",
 $GLOBALS['spx']["order"] == "name" ? $_srt : "yes") . '">' . _T('spixplorer:nameheader') .
($GLOBALS['spx']["order"] == "name" ? $_img : '') . '</a></b></td>
<td width="10%" class="header"><b><a href="' . make_link("list", $dir, NULL, "size",
 $GLOBALS['spx']["order"] == "size" ? $_srt : "yes") . '">' . _T('spixplorer:sizeheader') .
($GLOBALS['spx']["order"] == "size" ? $_img : '') . '</a></b></td>
<td width="16%" class="header"><b><a href="' . make_link("list", $dir, NULL, "type",
 $GLOBALS['spx']["order"] == "type" ? $_srt : "yes") . '">' . _T('spixplorer:typeheader') .
($GLOBALS['spx']["order"] == "type" ? $_img : '') . '</a></b></td>
<td width="14%" class="header"><b><a href="' . make_link("list", $dir, NULL, "mod",
 $GLOBALS['spx']["order"] == "mod" ? $_srt : "yes") . '">' . _T('spixplorer:modifheader') .
($GLOBALS['spx']["order"] == "mod" ? $_img : '') . '</a></b></td>
<td width="8%" class="header"><b>' . _T('spixplorer:permheader') . '</b></td>
<td width="10%" class="header"><b>' . _T('spixplorer:owner_group') . '</b></td>
<td width="6%" class="header"><b>' . _T('spixplorer:actionheader') . '</b></td></tr>
<tr><td colspan="8"><hr /></td></tr>
';
		
	// make & print Table using lists
	print_table($dir, make_list($dir_list, $file_list), $allow);

	// print number of items & total filesize
	echo '<tr><td colspan="8"><hr /></td></tr><tr>
<td class="header"></td>
<td class="header">' . $num_items . ' ' . _T('spixplorer:miscitems') . ' (' .
_T('spixplorer:miscfree') . ': ' .
	(function_exists("disk_free_space") ?
		parse_file_size(disk_free_space(get_abs_dir($dir))) :
	(function_exists("diskfreespace") ?
		parse_file_size(diskfreespace(get_abs_dir($dir))) : '?')) . ')</td>
<td class="header">' . parse_file_size($tot_file_size) . '</td>';
	for ($i=0; $i<4; ++$i) {
		echo '<td class="header"></td>';
	}
	echo '</tr>
<tr><td colspan="8"><hr /></td></tr></table></FORM>
';

	show_footer();
}
//------------------------------------------------------------------------------
function btn_link($action, $dir, $item = null, $href = '') {
	return '
<td><a href="' . ($href ? $href : make_link($action, $dir, $item)) . '">
<img border="0" width="32" height="32"
src="' . _DIR_PLUGIN_SPIXPLORER . '_img/' . $action . '.png"
alt="' . ($alt = _T('spixplorer:' . $action . 'link')) . '" title="' . $alt . '"></a></td>';
}
?>
