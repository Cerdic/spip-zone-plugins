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

function inc_exporter_catalogues_dist() {

	$form = "<input type='hidden' name='exporter_catalogues' value='oui' />\n"
	. exporter_choix_catalogues()
	. exporter_tout()
	. ("<div align='right'><input name='exporter' class='fondo' type='submit' value='"
	. _T('bouq:bouton_exporter')
	. "' /></div>");

	return generer_action_auteur("exporter_catalogues", "", "", $form," method='post' enctype='multipart/form-data' class='form_upload'");
}

function exporter_choix_catalogues() {

	$msg = '<p>'._T('bouq:explication_exportation').'</p>';
	$q = sql_select("titre,id_catalogue","spip_catalogues");
	while ($r = sql_fetch($q)) {
		$msg .=  '<input type="checkbox" name="'.$r['id_catalogue'].'" value="oui">'.$r['titre'].'</input><br />';
	}
	return $msg;
}

function exporter_tout() {
	return '<hr /><input type="checkbox" name="tout" value="oui">'._T('bouq:tout_exporter').'</input><br />';
}

?>
