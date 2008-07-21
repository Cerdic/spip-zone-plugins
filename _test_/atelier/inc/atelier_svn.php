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

function inc_atelier_svn_dist($action,$arg=array()) {
	$fonction = 'atelier_'.$action.'_svn';
	if (function_exists($fonction)) {
		return call_user_func($fonction,$arg);
	}
	return false;
}

function atelier_verifier_subversion() {
	$return = 0;
	exec("svn",&$output,&$return);
	if ($return != 1) return false;
	return true;
}

function atelier_verifier_projet_svn($prefixe) {
	return file_exists(_DIR_PLUGINS.$prefixe.'/.svn');
}

function atelier_update_svn($arg) {
	$form = "<input type='hidden' name='update_projet' value='oui' />\n"
	. '<input type="hidden" name="nom" value="'.$arg['nom'].'" />'
	. ("<div align='center'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_update_projet')
	. "' /></div>");
	return generer_action_auteur("atelier_svn", $arg['id_projet'], '', $form, " method='post' name='formulaire'");
}

function atelier_checkout_svn($arg) {

	$form = "<input type='hidden' name='checkout_projet' value='oui' />\n"
	. svn_choix_nom()
	. svn_choix_type()
	. svn_choix_etat()
	. svn_choix_creer_projet()

	. ("<div align='center'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_checkout_projet')
	. "' /></div>");

//	$arg =  $a['id_projet'];

	return generer_action_auteur("atelier_svn", '', '', $form, " method='post' name='formulaire'");
}

function svn_choix_type() {
	return _T('atelier:type_projet').'<select name="type"><option selected value="plugin">'._T('atelier:plugin').'</option></select><br />';
}

function svn_choix_etat() {
	return _T('atelier:etat_projet').'<select name="etat">'
		.'<option selected value="stable">'._T('atelier:stable').'</option>'
		.'<option value="test">'._T('atelier:test').'</option>'
		.'<option value="dev">'._T('atelier:dev').'</option>'
		.'</select><br />';
}

function svn_choix_nom() {
	return _T('atelier:nom_projet').'<input type="text" name="nom" value="" /><br />';
}

function svn_choix_creer_projet() {
	return '<input name="creer_projet" type="checkbox" value="oui" /> '._T('atelier:creer_projet');
}

?>
