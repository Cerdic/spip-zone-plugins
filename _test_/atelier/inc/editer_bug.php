<?php

/*
 *  Plugin Atelier pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_editer_bug_dist($new,$id_bug=0,$row=array()) {

	$form = "<input type='hidden' name='editer_bug' value='oui' />\n"
	. '<input type="hidden" name="id_projet" value="'.$row['id_projet'].'" />\n'
	. editer_bug_titre($row['titre'])
	. editer_bug_descriptif($row['descriptif'])
	. editer_bug_version($row['version'])
	. editer_bug_version_spip($row['version_spip']);

	$form .= ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("editer_bug", $id_bug, '', $form, " method='post' name='formulaire'");
}


function editer_bug_titre($titre) {

	return	"\n<p>" .
		_T('texte_titre_obligatoire') .
		"\n<br /><input type='text' name='titre' class='formo spip_small' value=\"" .
	  	entites_html($titre) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_bug_descriptif($descriptif) {
	return ("\n<p>" . _T('atelier:texte_descriptif') .
		"<br />\n" . 
		"<textarea name='descriptif' class='forml' rows='2' cols='40'>" .
		entites_html($descriptif) .
		"</textarea></p>");
}

function editer_bug_version($version) {

	return	"\n<p>" .
		_T('texte_version_obligatoire') .
		"\n<br /><input type='text' name='version' class='formo spip_small' value=\"" .
	  	entites_html($version) .
		"\" size='40' " .
		  " />\n</p>";
}
function editer_bug_version_spip($version_spip) {

	return	"\n<p>" .
		_T('texte_version_spip_obligatoire') .
		"\n<br /><input type='text' name='version_spip' class='formo spip_small' value=\"" .
	  	entites_html($version_spip) .
		"\" size='40' " .
		  " />\n</p>";
}

?>
