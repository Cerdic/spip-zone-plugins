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

function inc_atelier_objets_dist($a=array()) {
	$form = "<input type='hidden' name='atelier_objet' value='oui' />\n"
	. atelier_objet_type($a['type'])
	. atelier_objet_nom()
	. ("<div align='center'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_creer_objet')
	. "' /></div>");

	$arg =  $a['id_projet'];

	return generer_action_auteur("atelier_objets", $arg, '', $form, " method='post' name='formulaire'");


}

function atelier_objet_nom() {
	return 'Nom de l\'objet : <input type="text" name="nom" /><br /><br />';
}

function atelier_objet_type($type) {
	$res = 'Type d\'objet : <br />'
		. '<select name="type">';

	if ($type == 'plugin')
		$res .= '<option value="exec">Feuille dans l\'espace privée (exec)</option>'
			. '<option value="inc">Formulaire dans l\'espace privée (inc)</option>'
			. '<option value="action">Action dans l\'espace privée (action)</option>'
			. '<option value="table">Table supplémentaire dans la base de donnée</option>';

	$res .= '<option value="html">Page dans l\'espace publique (html)</option>'
		. '</select><br /><br />';


	return $res;
}
?>
