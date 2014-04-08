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

function inc_supprimer_bouquinerie_dist() {

	$form = "<input type='hidden' name='supprimer_bouquinerie' value='oui' />\n"
	. ("<div align='center'><input class='fondo' type='submit' value='"
	. _T('bouq:bouton_supprimer_bouquinerie')
	. "' /></div>");

	return generer_action_auteur("supprimer_bouquinerie", "", "", $form, " method='post' name='formulaire'");
}
?>
