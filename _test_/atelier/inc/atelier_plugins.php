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

function inc_atelier_plugins_dist($action,$arg='') {

	$fonction = 'atelier_'.$action.'_plugin';
	if (function_exists($fonction)) {
		return call_user_func($fonction,$arg);
	}
	return false;
}

function atelier_verifier_repertoire_plugin($prefixe)  {
	return file_exists(_DIR_PLUGINS.'/'.$prefixe);
}

function atelier_creer_repertoire_plugin($id_projet) {

	$form = "<input type='hidden' name='creer_repertoire' value='oui' />\n"
	. ("<div align='center'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_creer_repertoire')
	. "' /></div>");

	$arg =  $id_projet;

	return generer_action_auteur("atelier_plugins", $arg, '', $form, " method='post' name='formulaire'");

}

function atelier_verifier_droits_plugin($arg) {
	return is_writeable(_DIR_PLUGINS);
}

?>
