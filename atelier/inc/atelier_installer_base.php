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

function atelier_verifier_base() {

	$q = sql_showtable('spip_projets');
	if (!isset($q['field'])) return false;

	$q = sql_showtable('spip_taches');
	if (!isset($q['field'])) return false;

	$q = sql_showtable('spip_taches_projets');
	if (!isset($q['field'])) return false;

	return true;
}

function inc_atelier_installer_base_dist() {

	$form = "<input type='hidden' name='atelier_installer_base' value='oui' />\n"
		. ("<div align='center'><input name='atelier_installer_base' class='fondo' type='submit' value='"
		. _T('atelier:bouton_installer_base')
		. "' /></div>");
	
	return generer_action_auteur("atelier_installer_base", '', '', $form," method='post' enctype='multipart/form-data' class='form_upload'");
}
?>
