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

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_spx_archive()
{
	include_spip('inc/spx_init');
	archive_items($GLOBALS['spx']["dir"]);
}

//------------------------------------------------------------------------------

function zip_items($dir, $name, $aarchiver)
{
	$abs_dir=get_abs_dir($dir);
	$liste = array();
	for ($i=0; $i < count($aarchiver); ++$i) {
		$liste[$i] = get_abs_item($dir, stripslashes($aarchiver[$i]));
	}
	
	include_spip('inc/pclzip');
	spip_log('*** zipfile ***');
	spip_log($liste);
	$zipfile = new PclZip(_DIR_TMP . $name);
	$erreur = $zipfile->create($liste); // , PCLZIP_OPT_ADD_PATH, "spip");
	if ($erreur == 0) {
		show_error("Erreur : " . $zipfile->errorInfo(true));
	}
	
	header("Location: ".make_link("list",$dir,NULL));
}
//------------------------------------------------------------------------------
//function tar_items($dir, $name, $aarchiver) {
	// ...
//}
//------------------------------------------------------------------------------
//function tgz_items($dir, $name, $aarchiver) {
	// ...
//}
//------------------------------------------------------------------------------
function archive_items($dir) {
	if (($GLOBALS['spx']["permissions"]&01)!=01) {
		show_error(_T('spixplorer:accessfunc'));
	}
	if (!function_exists('zip_items')
	 && !function_exists('tar_items') && !function_exists('tgz_items')) {
		show_error(_T('spixplorer:miscnofunc'));
	}
	
	$aarchiver = _request('selitems');
	$cnt=count($aarchiver);
	if ($name = basename(_request('namearch'))) {
		if (!($name = stripslashes($name))) show_error(_T('spixplorer:miscnoname'));
		switch (substr(strrchr($name, "."), 1)) {
			case "zip":	zip_items($dir, $name, $aarchiver);	break;
			case "tar":	tar_items($dir, $name, $aarchiver);	break;
			default:		tgz_items($dir, $name, $aarchiver);
		}
	}
	header("Location: ".make_link("list",$dir,NULL));
}
//------------------------------------------------------------------------------
?>
