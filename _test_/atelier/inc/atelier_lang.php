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

function inc_atelier_lang_dist($action,$arg=array()) {

	$fonction = 'atelier_'.$action.'_lang';
	if (function_exists($fonction)) {
		return call_user_func($fonction,$arg);
	}
	return false;
}

function atelier_verifier_repertoire_lang($a) {
	return file_exists(_DIR_PLUGINS.$a['prefixe'].'/lang');
}

function atelier_verifier_fichier_lang($a) {
	$fichier = $a['fichier'];
	$module = $a['module'];
	if ($fichier =='') return false;
	return file_exists(_DIR_PLUGINS.$module.'/lang/'.$fichier);
}

function atelier_creer_repertoire_lang($a) {

	$form = "<input type='hidden' name='creer_repertoire' value='oui' />\n"
	. ("<div align='center'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_creer_repertoire_lang')
	. "' /></div>");

	$arg =  $a['id_projet'];

	return generer_action_auteur("atelier_lang", $arg, '', $form, " method='post' name='formulaire'");
}

function atelier_edit_lang($a) {
	$module = $a['module'];
	$lang = $a['lang'];
	if ($lang=='') $lang='fr';

	$fichier_lang = '../plugins/'.$module.'/lang/'.$module.'_'.$lang.'.php';
	$GLOBALS['idx_lang']='i18n_'.$module.'_fr';

	include($fichier_lang);

	if (count($GLOBALS[$GLOBALS['idx_lang']]) == 0) return _T('atelier:pas_definition');

	$form = "<input type='hidden' name='edit_lang' value='oui' />\n"
		.'<table>';

	foreach ($GLOBALS[$GLOBALS['idx_lang']] as $key => $value) {
		$form .= '<tr><td>'.$key.'</td>'
		.'<td><input type="text" name="'.$key.'" value="'.entites_html($value).'" size="50" /></td></tr>';
	}
	$form .= '</table>'
	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_enregistrer')
	. "' /></div>");

	return generer_action_auteur("atelier_lang", $module, '', $form, " method='post' name='formulaire'");
}

function atelier_ajout_lang($a) {
	$module = $a['module'];
	$lang = $a['lang'];
	if ($lang=='') $lang='fr';
	$id_projet = $a['id_projet'];
	$form =  "<input type='hidden' name='ajout_lang' value='oui' />\n"
	. "<input type='hidden' name='lang' value='$lang' />\n"
	. "<input type='hidden' name='module' value='$module' />\n"
	. '<input type="text" name="key" value="ma_nouvelle_cle" /> => '
	. '<input type="text" name="value" value="ma_definition" />  '
	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_enregistrer')
	. "' /></div>");
	return generer_action_auteur("atelier_lang", $id_projet, '', $form, " method='post' name='formulaire'");
}

function atelier_creer_fichier_lang($a) {
	$form = "<input type='hidden' name='creer_fichier' value='oui' />\n"
	. atelier_choix_lang()
	. ("<div align='right'><input class='fondo' type='submit' value='"
	. _T('atelier:bouton_creer_fichier_lang')
	. "' /></div>");

	$arg =  $a['id_projet'];

	return generer_action_auteur("atelier_lang", $arg, '', $form, " method='post' name='formulaire'");
}

function atelier_choix_lang() {
	return _T('atelier:choisissez_lang').'<select name="choix_lang">'
		.'<option value="fr">FR (français)</option>'
		.'<option value="en">EN (english)</option>'
		.'<option value="es">ES (espagnol)</option>'
		.'</select>';
}
?>
