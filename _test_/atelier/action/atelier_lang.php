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

function action_atelier_lang_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	if (!$id_auteur) redirige_par_entete('./');

	$id_projet = $arg;
	$r = sql_fetsel('prefixe','spip_projets','id_projet='.$id_projet);
	$prefixe = $r['prefixe'];
	$type = $r['type'];

	$creer_repertoire = _request('creer_repertoire');
	$creer_fichier = _request('creer_fichier');
	$ajout_lang = _request('ajout_lang');
	$edit_lang = _request('edit_lang');

	if (isset($creer_repertoire)) {
		atelier_creer_repertoire_lang($prefixe);
	}
	if (isset($creer_fichier)) {
		$lang = _request('choix_lang');
		atelier_creer_fichier_lang($type,$prefixe,$lang);
	}
        $redirect = parametre_url(urldecode(generer_url_ecrire('atelier_lang')),
				'id_projet', $id_projet, '&') . $err;

	if (isset($ajout_lang)) {
		$lang = _request('lang');
		$module = _request('module');
		$key = _request('key');
		$value = _request('value');
		$type = _request('type');
		atelier_ajout_lang($module,$lang,$type,$enreg = array('key' => $key, 'value' => $value));
	        $redirect = parametre_url(urldecode(generer_url_ecrire('atelier_lang',"id_projet=$id_projet")),
				'fichier', $module.'_'.$lang.'.php', '&') . $err;
	}

	if (isset($edit_lang)) {
	
		$arg = explode('-',$arg);
		$id_projet = $arg[0];
		$module = $arg[1];
		$lang = $arg[2];
		$type = $arg[3];
	
		atelier_edit_lang($module,$lang,$type);
	        $redirect = parametre_url(urldecode(generer_url_ecrire('atelier_lang',"id_projet=$id_projet")),
				'fichier', $module.'_'.$lang.'.php', '&') . $err;
	}

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}


function atelier_creer_repertoire_lang($prefixe) {
	mkdir(_DIR_PLUGINS.$prefixe.'/lang');
}

function atelier_lire_fichier_langue($fichier) {
	$lignes = file($fichier,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$lang = array();

	foreach($lignes as $l)
		if (preg_match('#(\')(.*)(\')\s*=>\s*(\')(.*)(\',)#',$l,$match))
			$lang[$match[2]] = $match[5];
	return $lang;
}

function atelier_creer_contenu_langue($c) {
	$contenu = '';
	if (array_multisort(array_keys($c),SORT_STRING,$c))
		foreach($c as $key => $value)
			$contenu .= '\''.$key.'\' => \''.$value.'\','."\n";
	return $contenu;
}

function atelier_ecrire_fichier_langue($fichier,$contenu) {
	// on ajoute les commentaires repére ?
	$debut_squel = 
'<?php 

// Fichier generer par le plugin Atelier

if(!defined("_ECRIRE_INC_VERSION")) return; 

$GLOBALS[$GLOBALS[\'idx_lang\']] = array (

';

	$fin_squel = 
'

);

?>';
	$contenu = $debut_squel . $contenu . $fin_squel;
	ecrire_fichier($fichier,$contenu);
}

function atelier_edit_lang($module,$lang,$type) {
	if ($type == 'plugin') $fichier = _DIR_PLUGINS.$module.'/lang/'.$module.'_'.$lang.'.php';
	else {
		global $repertoire_squelettes_alternatifs; // plugin switcher
		$fichier = './'.$repertoire_squelettes_alternatifs.'/'.$module.'/lang/'.$module.'_'.$lang.'.php';
	}

	$lang = atelier_lire_fichier_langue($fichier);

	foreach ($lang as $key => $value) {
		if ($key == 'action') {
			if ($value) $c[$key] = text_to_php(_request('ATELIER$action'));
		}
		else {
			if ($value) $c[$key] = text_to_php(_request($key));
		}
	}

	$contenu = atelier_creer_contenu_langue($c);
	atelier_ecrire_fichier_langue($fichier,$contenu);
}

function atelier_ajout_lang($module,$lang,$type,$enreg) {
	if ($type == 'plugin') $fichier = _DIR_PLUGINS.$module.'/lang/'.$module.'_'.$lang.'.php';
	else {
		global $repertoire_squelettes_alternatifs; // plugin switcher
		$fichier = './'.$repertoire_squelettes_alternatifs.'/'.$module.'/lang/'.$module.'_'.$lang.'.php';
	}

	$lang = atelier_lire_fichier_langue($fichier);

	$lang[$enreg['key']] = text_to_php($enreg['value']);

	$contenu = atelier_creer_contenu_langue($lang);
	atelier_ecrire_fichier_langue($fichier,$contenu);
}

// prepare un texte pour inclusion dans fichier php
function text_to_php($value) {
	$value = preg_replace("#'#","\'",$value);
	$value = preg_replace("#é#","&eacute;",$value);
	$value = preg_replace("#è#","&egrave;",$value);
	$value = preg_replace("#à#","&agrave;",$value);
	$value = preg_replace("#ê#","&ecirc;",$value);
	$value = preg_replace("#â#","&acirc;",$value);
	$value = preg_replace("#î#","&icirc;",$value);
	$value = preg_replace("#ï#","&iuml;",$value);
	$value = preg_replace("#œ#","&oelig;",$value);
	$value = preg_replace("#ù#","&ugrave;",$value);
	$value = preg_replace("#û#","&ucirc;",$value);
	$value = preg_replace("#ç#","&ccedil;",$value);
	$value = preg_replace("#É#","&Eacute;",$value);
	$value = preg_replace("#È#","&Egrave;",$value);
	$value = preg_replace("#À#","&Agrave;",$value);
	$value = preg_replace("#Ê#","&Ecirc;",$value);
	$value = preg_replace("#Â#","&Acirc;",$value);
	$value = preg_replace("#Î#","&Icirc;",$value);
	$value = preg_replace("#Ï#","&Iuml;",$value);
	$value = preg_replace("#Œ#","&OElig;",$value);
	$value = preg_replace("#Ù#","&Ugrave;",$value);
	$value = preg_replace("#Û#","&Ucirc;",$value);
	$value = preg_replace("#Ç#","&Ccedil;",$value);
	return $value;
}

function atelier_creer_fichier_lang($type,$prefixe,$lang) {

	$squel = 
'<?php 

// Fichier generer par le plugin Atelier

if(!defined("_ECRIRE_INC_VERSION")) return; 

$GLOBALS[$GLOBALS[\'idx_lang\']] = array (

);

?>';
	if ($type == 'plugin') $fichier = _DIR_PLUGINS.$prefixe.'/lang/'.$prefixe.'_'.$lang.'.php';
	else {
		global $repertoire_squelettes_alternatifs; // plugin switcher
		$fichier = './'.$repertoire_squelettes_alternatifs.'/'.$prefixe.'/lang/'.$prefixe.'_'.$lang.'.php';
	}


	ecrire_fichier($fichier,$squel);

}
