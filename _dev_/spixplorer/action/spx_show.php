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

     The Original Code is fun_show.php, released on 2003-03-31.

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
	Permission-Change Functions
	
	Have Fun...

	Adaptation spip, plugin spixplorer : bertrand@toggg.com © 2007

------------------------------------------------------------------------------*/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_spx_show()
{
	include_spip('inc/spx_init');
	show_item($GLOBALS['spx']["dir"], $GLOBALS['spx']["item"]);
}

//------------------------------------------------------------------------------
function show_item($dir, $item) {		// afficher un fichier
	if(($GLOBALS['spx']["permissions"]&01)!=01) show_error(_T('spixplorer:accessfunc'));
	if(!file_exists(get_abs_item($dir, $item))) show_error($item.": "._T('spixplorer:fileexist'));
	if(!get_show_item($dir, $item)) show_error($item.": "._T('spixplorer:accessfile'));

	show_header('<strong>' . $item . '</strong>', true);
	
	// Caracteristiques du fichier, TODO affichage en clair
	$stat = spx_stat($dir, $item);
//	echo nl2br(htmlentities(var_export($stat, true)));
	echo '
<p><strong>' . $item . '</strong>
<hr /></p>
';

	$link = $GLOBALS['spx']['home_url'] . '/' . $stat['rel'];
	// Montrer le fichier
	if ($stat['edit']) {
		echo '<div class="code">' . nl2br(htmlentities(file_get_contents($stat['abs']))) . '</div>';
	} elseif (get_is_image($dir, $item)) {
		echo '<p><a href="' . $link . '" alt="' . _T('spixplorer:pleine_page') .
		'"><img src="' . $link . '" /></a></p>';
	}
	
	show_footer();
}
//------------------------------------------------------------------------------
?>
