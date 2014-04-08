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

function inc_importer_catalogue_dist($retour='', $row=array()) {

	$form = "<input type='hidden' name='importer_catalogue' value='oui' />\n"
	. importer_catalogue_type()
	. importer_catalogue_id()
	. importer_criteres_dicriminants()
	. importer_images()
	. importer_motcles()
	. importer_fichier()

	. ("<div align='right'><input name='ajout' class='fondo' type='submit' value='"
	. _T('bouton_telecharger')
	. "' /></div>");

	return generer_action_auteur("importer_catalogue", $id_catalogue, $retour, $form," method='post' enctype='multipart/form-data' class='form_upload'");



}

function importer_images() {
	$opt = '<select name="import_image">';
	$opt .= '<option selected value="non">'._T('bouq:import_image_non').'</option>';
	$opt .= '<option value="oui">'._T('bouq:import_image_oui').'</option>';
	$opt .= '<option value="url">'._T('bouq:import_image_url').'</option>';
	$opt .= '<option value="distant">'._T('bouq:import_image_distant').'</option>';
	$opt .= '</select><br />';


	$msg = _T('bouq:titre_cadre_interieur_images_import');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
	
}
function importer_catalogue_type() {

	$msg = "\n<p>"._T('bouq:texte_importer_type') . "\n<br />";

	$opt = '<select name="type">'.
		'<option value="bouquinerie">'._T('bouq:catalogue_bouquinerie').'</option>'.
		'<option value="priceminister">'._T('bouq:catalogue_priceminister').'</option>'.
	//	'<option value="alapage">Un catalogue Alapage</option>'.
	//	'<option value="abebooks">Un catalogue AbeBooks</option>'.
		'</select></p><br />';

	return $msg . $opt;
}

function importer_catalogue_id() {
	$opt = '<select name="id_catalogue">';
	$query = sql_select("titre, id_catalogue", "spip_catalogues");
	while ($row = sql_fetch($query)) {
		$opt .= '<option value="'.$row['id_catalogue'].'">'.$row['titre'].'</option>';
	}
	$opt .= '<option value="new">'._T('bouq:nouveau_catalogue').'</option>';
	$opt .= '</select><br />';


	$msg = _T('bouq:titre_cadre_interieur_catalogue_import');
	$logo = "racine-site-24.gif";
	return debut_cadre_couleur($logo, true, "", $msg) . $opt . fin_cadre_couleur(true);
}

function importer_fichier() {
	return 	'<br />' . _T('bouq:import_fichier') . "<br /><input name='fichier' type='file' class='forml spip_xx-small' size='15' />"
	. "\n\t\t";
}

function importer_criteres_dicriminants() {

	$msg = 	'<input type="checkbox" name="DoublonTitre" value="oui">'._T('bouq:critere_doublon_titre').'</input><br />'
		.'<input type="checkbox" name="DoublonIsbn" value="oui">'._T('bouq:critere_doublon_isbn').'</input>'
		.'<p>'._T('bouq:explication_doublons_import').'</p>'
		.'<input type="checkbox" name="total" value="oui" checked="yes">'._T('bouq:exclusion_toute_bdd').'</input><br />'
		.'<p>'._T('bouq:explication_exclusion_toute_bdd').'</p>';
	$logo = "racine-site-24.gif";
	$titre = _T('bouq:exclure_doublons');
	return debut_cadre_couleur($logo, true, "", $titre) . $msg . fin_cadre_couleur(true);
}


function importer_motcles() {

	$msg = 	'<input type="checkbox" name="MotsCles" value="oui">'._T('bouq:creer_motscles').'</input><br />';
	$logo = "racine-site-24.gif";
	$titre = _T('bouq:titre_creer_motscles');
	return debut_cadre_couleur($logo, true, "", $titre) . $msg . fin_cadre_couleur(true);
}
?>
