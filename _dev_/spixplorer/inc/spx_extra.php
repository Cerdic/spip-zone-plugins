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

     The Original Code is fun_extra.php, released on 2003-03-31.

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
	(Extra) Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/
function spx_stat($dir, $item)
{
	$fichier = get_abs_item($dir, $item);
	($ret = stat($fichier)) || ($ret = array());
	$ret['abs'] = $fichier;
	$ret['rel'] = get_rel_item($dir, $item);
	$ret['is_dir'] = is_dir($fichier);
	$ret['mime_type'] = get_mime_type($dir, $item, 'type');
	$ret['mime_img'] = get_mime_type($dir, $item, 'img');
	$ret['perms'] = get_file_perms($dir,$item);
	$ret['owner'] = ($posix = posix_getpwuid($ret['uid'])) ?
		$posix['name'] : $ret['uid'];
	$ret['group'] = ($posix = posix_getgrgid($ret['gid'])) ?
		$posix['name'] : $ret['gid'];
	$ret['edit'] = get_is_editable($dir, $item);
	$ret['file'] = get_is_file($dir, $item);
	return $ret;
}

//------------------------------------------------------------------------------
// THESE ARE NUMEROUS HELPER FUNCTIONS FOR THE OTHER INCLUDE FILES
//------------------------------------------------------------------------------
function make_hash($_action, $_dir, $_item=NULL)
{
	include_spip('inc/securiser_action');
    $arg = $_dir . '-' . $_item;
	$hash = calculer_action_auteur('spx_' . $_action . '-' . $arg);
	return array($arg, $hash);
}

function make_link($_action,$_dir,$_item=NULL,$_order=NULL,$_srt=NULL,$_lang=NULL) {
						// make link to next page
	if (!$_action) {
		$_action = "list";
	}
	
	$_order || ($_order = $GLOBALS['spx']["order"]);
	$_srt || ($_srt = $GLOBALS['spx']["srt"]);
//	if($_lang==NULL) $_lang=(isset($GLOBALS['spx']["lang"])?$GLOBALS['spx']["lang"]:NULL);
	
	$link = $_SERVER['PHP_SELF'] . '?action=spx_' . $_action;
	if ($_action != 'list' && $_action != 'show') {
	    list($arg, $hash) = make_hash($_action, $_dir, $_item);
		$link .= '&arg=' . $arg . '&hash=' .  $hash;
	}

	if ($_dir) $link.="&dir=".urlencode($_dir);
	if ($_item) $link.="&item=".urlencode($_item);

	if (!$_order && $_srt) {
		$_order = 'name';
	}
	if ($_order) {
		$link .= '&order=' . ($_srt != 'yes' ? '-' : '') . $_order;
	}

//	if($_lang!=NULL) $link.="&lang=".$_lang;
	
	return $link;
}
//------------------------------------------------------------------------------
function get_abs_dir($dir) {			// get absolute path
	$abs_dir=$GLOBALS['spx']["home_dir"];
	if($dir!="") $abs_dir.="/".$dir;
	return $abs_dir;
}
//------------------------------------------------------------------------------
function get_abs_item($dir, $item) {		// get absolute file+path
	return get_abs_dir($dir)."/".$item;
}
//------------------------------------------------------------------------------
function get_rel_item($dir,$item) {		// get file relative from home
	if($dir!="") return $dir."/".$item;
	else return $item;
}
//------------------------------------------------------------------------------
function get_is_file($dir, $item) {		// can this file be edited?
	return @is_file(get_abs_item($dir,$item));
}
//------------------------------------------------------------------------------
function get_is_dir($dir, $item) {		// is this a directory?
	return @is_dir(get_abs_item($dir,$item));
}
//------------------------------------------------------------------------------
function parse_file_type($dir,$item) {		// parsed file type (d / l / -)
	$abs_item = get_abs_item($dir, $item);
	if(@is_dir($abs_item)) return "d";
	if(@is_link($abs_item)) return "l";
	return "-";
}
//------------------------------------------------------------------------------
function get_file_perms($dir,$item) {		// file permissions
	return @decoct(@fileperms(get_abs_item($dir,$item)) & 0777);
}
//------------------------------------------------------------------------------
function parse_file_perms($mode) {		// parsed file permisions
	if(strlen($mode)<3) return "---------";
	$parsed_mode="";
	for($i=0;$i<3;$i++) {
		// read
		if(($mode{$i} & 04)) $parsed_mode .= "r";
		else $parsed_mode .= "-";
		// write
		if(($mode{$i} & 02)) $parsed_mode .= "w";
		else $parsed_mode .= "-";
		// execute
		if(($mode{$i} & 01)) $parsed_mode .= "x";
		else $parsed_mode .= "-";
	}
	return $parsed_mode;
}
//------------------------------------------------------------------------------
function get_file_size($dir, $item) {		// file size
	return @filesize(get_abs_item($dir, $item));
}
//------------------------------------------------------------------------------
function parse_file_size($size) {		// parsed file size
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . " GB";
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . " MB";
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . " KB";
	} else $size = $size . " Bytes";
	if($size==0) $size="-";

	return $size;
}
//------------------------------------------------------------------------------
function get_file_date($dir, $item) {		// file date
	return @filemtime(get_abs_item($dir, $item));
}
//------------------------------------------------------------------------------
function parse_file_date($date) {		// parsed file date
	return @date(_T('spixplorer:date_fmt'),$date);
}
//------------------------------------------------------------------------------
function get_is_image($dir, $item) {		// is this file an image?
	if(!get_is_file($dir, $item)) return false;
	return @eregi($GLOBALS['spx']["images_ext"], $item);
}
//-----------------------------------------------------------------------------
function get_is_editable($dir, $item) {		// is this file editable?
	if(!get_is_file($dir, $item)) return false;
	foreach($GLOBALS['spx']["editable_ext"] as $pat) if(@eregi($pat,$item)) return true;
	return false;
}
//-----------------------------------------------------------------------------
function get_mime_type($dir, $item, $query) {	// get file's mimetype
	if(get_is_dir($dir, $item)) {			// directory
		$mime_type	= $GLOBALS['spx']["super_mimes"]["dir"][0];
		$image		= $GLOBALS['spx']["super_mimes"]["dir"][1];
		
		if($query=="img") return $image;
		else return $mime_type;
	}
				// mime_type
	foreach($GLOBALS['spx']["used_mime_types"] as $mime) {
		list($desc,$img,$ext)	= $mime;
		if(@eregi($ext,$item)) {
			$mime_type	= $desc;
			$image		= $img;
			if($query=="img") return $image;
			else return $mime_type;
		}
	}
	
	if((function_exists("is_executable") &&
		@is_executable(get_abs_item($dir,$item))) ||
		@eregi($GLOBALS['spx']["super_mimes"]["exe"][2],$item))		
	{						// executable
		$mime_type	= $GLOBALS['spx']["super_mimes"]["exe"][0];
		$image		= $GLOBALS['spx']["super_mimes"]["exe"][1];
	} else {					// unknown file
		$mime_type	= $GLOBALS['spx']["super_mimes"]["file"][0];
		$image		= $GLOBALS['spx']["super_mimes"]["file"][1];
	}

	if($query=="img") return $image;
	else return $mime_type;
}
//------------------------------------------------------------------------------
function get_show_item($dir, $item) {		// show this file?
	if($item == "." || $item == ".." ||
		(substr($item,0,1)=="." && $GLOBALS['spx']["show_hidden"]==false)) return false;
		
	if($GLOBALS['spx']["no_access"]!="" && @eregi($GLOBALS['spx']["no_access"],$item)) return false;
	
	if($GLOBALS['spx']["show_hidden"]==false) {
		$dirs=explode("/",$dir);
		foreach($dirs as $i) if(substr($i,0,1)==".") return false;
	}
	
	return true;
}
//------------------------------------------------------------------------------
function copy_dir($source,$dest) {		// copy dir
	$ok = true;
	
	if(!@mkdir($dest,0777)) return false;
	if(($handle=@opendir($source))===false) show_error(basename($source).": "._T('spixplorer:opendir'));
	
	while(($file=readdir($handle))!==false) {
		if(($file==".." || $file==".")) continue;
		
		$new_source = $source."/".$file;
		$new_dest = $dest."/".$file;
		if(@is_dir($new_source)) {
			$ok=copy_dir($new_source,$new_dest);
		} else {
			$ok=@copy($new_source,$new_dest);
		}
	}
	closedir($handle);
	return $ok;
}
//------------------------------------------------------------------------------
function remove($item) {			// remove file / dir
	$ok = true;
	spip_log('spixplorer Delete' . $item);
	if(@is_link($item) || @is_file($item)) $ok=@unlink($item);
	elseif(@is_dir($item)) {
		if(($handle=@opendir($item))===false) show_error(basename($item).": "._T('spixplorer:opendir'));

		while(($file=readdir($handle))!==false) {
			if(($file==".." || $file==".")) continue;
			
			$new_item = $item."/".$file;
			if(!@file_exists($new_item)) show_error(basename($item).": "._T('spixplorer:readdir'));
			//if(!get_show_item($item, $new_item)) continue;
			
			if(@is_dir($new_item)) {
				$ok=remove($new_item);
			} else {
				$ok=@unlink($new_item);
			}
		}
		
		closedir($handle);
		$ok=@rmdir($item);
	}
	return $ok;
}
//------------------------------------------------------------------------------
function get_max_file_size() {			// get php max_upload_file_size
	$max = get_cfg_var("upload_max_filesize");
	if(@eregi("G$",$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1073741824);
	} elseif(@eregi("M$",$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1048576);
	} elseif(@eregi("K$",$max)) {
		$max = substr($max,0,-1);
		$max = round($max*1024);
	}
	
	return $max;
}
//------------------------------------------------------------------------------
function down_home($abs_dir) {			// dir deeper than home?
	$real_home = @realpath($GLOBALS['spx']["home_dir"]);
	$real_dir = @realpath($abs_dir);
	
	if($real_home===false || $real_dir===false) {
		if(@eregi("\\.\\.",$abs_dir)) return false;
	} else if(strcmp($real_home,@substr($real_dir,0,strlen($real_home)))) {
		return false;
	}
	return true;
}
//------------------------------------------------------------------------------
function id_browser() {
	$browser=$GLOBALS['spx']['__SERVER']['HTTP_USER_AGENT'];
	
	if(ereg('Opera(/| )([0-9].[0-9]{1,2})', $browser)) {
		return 'OPERA';
	} else if(ereg('MSIE ([0-9].[0-9]{1,2})', $browser)) {
		return 'IE';
	} else if(ereg('OmniWeb/([0-9].[0-9]{1,2})', $browser)) {
		return 'OMNIWEB';
	} else if(ereg('(Konqueror/)(.*)', $browser)) {
		return 'KONQUEROR';
	} else if(ereg('Mozilla/([0-9].[0-9]{1,2})', $browser)) {
		return 'MOZILLA';
	} else {
		return 'OTHER';
	}
}

// Fil d'Ariane
function link_all($dir)
{
	if (!$dir) {
		return '';
	}
	$ret = '';
	while (($pos = strrpos($dir, '/')) !== false) {
		$terminal = substr($dir, $pos + 1);
		$ret = '<a href="' . make_link('list', $dir) . '">' . $terminal . '</a>' . $ret;
		$dir = substr($dir, 0, $pos);
	}
	$ret = '<a href="' . make_link('list', $dir) . '">' . $dir . $sep . '</a>' . $ret;
	return $ret;
}
//------------------------------------------------------------------------------
?>
