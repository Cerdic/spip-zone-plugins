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

     The Original Code is header.php, released on 2003-02-07.

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
	Header File
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

-------------------------------------------------------------------------------*/
//------------------------------------------------------------------------------
function show_header($title, $js = false) {			// header for html-page
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	header("Content-Type: text/html; charset=".$GLOBALS['spx']["charset"]);
	
	//echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\"";
	//echo "\"http://www.w3.org/TR/REC-html40/loose.dtd\">\n";
	echo 
'<HTML lang="' . $GLOBALS['spip_lang'] . '" dir="' .
			($GLOBALS['spip_lang_rtl'] ? 'rtl' : 'ltr') .'">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=' . $GLOBALS['meta']['charset'] . '">
<title>SpiXplorer: QuiXplorer ' . $GLOBALS['spx']["version"] . ' pour Spip</title>
<LINK href="' . _DIR_PLUGIN_SPIXPLORER . '_style/style.css" rel="stylesheet" type="text/css">
';	
	// Javascript functions:
	if ($js) {
		include_spip("inc/spx_javascript");
	}
	echo '
</HEAD>
<BODY>
<table border="0" width="100%" cellspacing="0" cellpadding="5"><tbody>
<tr><td class="title">' .
	($GLOBALS['spx']["require_login"] && isset($GLOBALS['spx']['__SESSION']["s_user"]) ?
		'[' . $GLOBALS['spx']['__SESSION']["s_user"] . '] - ' : '') .
'<a href="' . make_link('list', '') . '">
<img border="0" width="22" height="22" src="' . _DIR_PLUGIN_SPIXPLORER . '_img/home.png"
alt="' . _T('spixplorer:homelink') . '" title="' . _T('spixplorer:homelink') . '">
</a>' . link_all($GLOBALS['spx']['dir']) . $title . '
</td>
<td  width="10%" class="title"><a href="ecrire/">' . _T('retour_ecrire') . '</td>
<td  width="10%" class="title"><a href="spip.php">' . _T('retour_spip') . '</td></tr></tbody></table>

';
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
		$terminal = strlen($terminal) > 50 ? '...' . substr($terminal,-47) : $terminal;
		$ret = '<a href="' . make_link('list', $dir) . '">' . $terminal . '</a>' . $ret;
		$dir = substr($dir, 0, $pos);
	}
	$ret = '<a href="' . make_link('list', $dir) . '">' . $dir . $sep . '</a>' . $ret;
	return $ret;
}
//------------------------------------------------------------------------------
?>
