<?php

/*
 *  Plugin Bouquinerie pour SPIP
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

include_spip('inc/actions');

function inc_editer_catalogue_dist($new, $id_catalogue=0, $retour='', $row=array()) {

	$form = "<input type='hidden' name='editer_catalogue' value='oui' />\n"
	. editer_catalogue_titre($row['titre'])
	. editer_catalogue_descriptif($row['descriptif'])

	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("editer_catalogue", $id_catalogue, $retour, $form, " method='post' name='formulaire'");
}

function editer_catalogue_titre($titre) {
	return	"\n<p>" .
		_T('texte_titre_obligatoire') .
		"\n<br /><input type='text' name='titre' class='formo spip_small' value=\"" .
	  	entites_html($titre) .
		"\" size='40' " .
		  " />\n</p>";
}

function editer_catalogue_descriptif($descriptif) {

	return ("\n<p>" . _T('bouq:texte_descriptif') .
		"<br />\n" . 
		"<textarea name='descriptif' class='forml' rows='2' cols='40'>" .
		entites_html($descriptif) .
		"</textarea></p>");
}

?>
