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

function inc_atelier_plugin_xml_dist($id_projet=0,$arbre=array()) {

	$form = "<input type='hidden' name='editer_plugin_xml' value='oui' />\n"
	. '<table>'
	. editer_plugin_nom($arbre['plugin'][0]['nom'][0])
	. editer_plugin_auteur($arbre['plugin'][0]['auteur'][0])
	. editer_plugin_version($arbre['plugin'][0]['version'][0])
	. editer_plugin_description($arbre['plugin'][0]['description'][0])
	. editer_plugin_etat($arbre['plugin'][0]['etat'][0])
	. editer_plugin_lien($arbre['plugin'][0]['lien'][0])
	. editer_plugin_options($arbre['plugin'][0]['options'][0])
	. editer_plugin_fonctions($arbre['plugin'][0]['fonctions'][0])
	. editer_plugin_prefixe($arbre['plugin'][0]['prefix'][0])
	. '</table>'
	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("atelier_plugin_xml", $id_projet, '', $form, " method='post' name='formulaire'");
}

function editer_plugin_nom($nom) {
	return '<tr><td>'._T('atelier:plugin_nom').'</td><td><input name="nom" type="text" value="'.$nom.'"/></td></tr>';
}

function editer_plugin_auteur($auteur) {
	return '<tr><td>'._T('atelier:plugin_auteur').'</td><td><input name="auteur" type="text" value="'.$auteur.'" /></td></tr>';
}

function editer_plugin_version($version) {
	return '<tr><td>'._T('atelier:plugin_version').'</td><td><input name="version" type="text" value="'.$version.'" /></td></tr>';
}

function editer_plugin_description($description) {
	return '<tr><td>'._T('atelier:plugin_description').'</td><td><input name="description" type="text" value="'.$description.'" /></td></tr>';
}

function editer_plugin_etat($etat) {
	return '<tr><td>'._T('atelier:plugin_etat').'</td><td><input name="etat" type="text" value="'.$etat.'" /></td></tr>';
}

function editer_plugin_lien($lien) {
	return '<tr><td>'._T('atelier:plugin_lien').'</td><td><input name="lien" type="text" value="'.$lien.'" /></td></tr>';
}

function editer_plugin_options($options) {
	return '<tr><td>'._T('atelier:plugin_options').'</td><td><input name="options" type="text" value="'.$options.'" /></td></tr>';
}

function editer_plugin_fonctions($fonctions) {
	return '<tr><td>'._T('atelier:plugin_fonctions').'</td><td><input name="fonctions" type="text" value="'.$fonctions.'" /></td></tr>';
}

function editer_plugin_prefixe($prefixe) {
	return '<tr><td>'._T('atelier:plugin_prefixe').'</td><td><input name="prefixe" type="text"  value="'.$prefixe.'"/></td></tr>';
}

?>
