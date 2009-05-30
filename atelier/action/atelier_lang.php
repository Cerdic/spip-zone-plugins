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
	$r = sql_fetsel('prefixe, type','spip_projets','id_projet='.$id_projet);
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
	$contenu = array();
	if (array_multisort(array_keys($c),SORT_STRING,$c))
		foreach($c as $key => $value)
			$contenu[strtoupper($key[0])][] = "\t".'\''.$key.'\' => \''.$value.'\','."\n";
	return $contenu;
}

// contenu array[A (array=> lignes) B C ...]
function atelier_ecrire_fichier_langue($fichier,$contenu) {
	$squel = '';$texte='';
	lire_fichier(_DIR_PLUGINS .'atelier/gabarits/lang.txt',&$squel);

	foreach ($contenu as $lettre => $lignes) {
		$texte .= "\n\t// $lettre\n\n";
		foreach ($lignes as $ligne) {
			$texte .= $ligne;
		}
	}
	$squel = preg_replace('#\[definition\]#',$texte,$squel);
	ecrire_fichier($fichier,$squel);
}

function atelier_edit_lang($module,$lang,$type) {
	include_spip('inc/atelier_fonctions');
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
	include_spip('inc/atelier_fonctions');
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



function atelier_creer_fichier_lang($type,$prefixe,$lang) {

	$squel = '';
	lire_fichier(_DIR_PLUGINS .'atelier/gabarits/lang.txt',&$squel);
	$squel = preg_replace('#\[definition\]#',"",$squel);

	if ($type == 'plugin') $fichier = _DIR_PLUGINS.$prefixe.'/lang/'.$prefixe.'_'.$lang.'.php';
	else {
		global $repertoire_squelettes_alternatifs; // plugin switcher
		$fichier = './'.$repertoire_squelettes_alternatifs.'/'.$prefixe.'/lang/'.$prefixe.'_'.$lang.'.php';
	}


	ecrire_fichier($fichier,$squel);

}
