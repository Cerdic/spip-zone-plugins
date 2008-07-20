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


function inc_atelier_todo_dist($id_projet) {

	$form = "<input type='hidden' name='atelier_todo' value='oui' />\n"
	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_generer_todo')
	. "' /></div>");

	return generer_action_auteur("atelier_todo", $id_projet, '', $form, " method='post' name='formulaire'");

}

?>
