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

	$creer_repertoire = _request('creer_repertoire');
	$creer_fichier = _request('creer_fichier');
	$ajout_lang = _request('ajout_lang');

	if (isset($creer_repertoire)) {
		atelier_creer_repertoire_lang($prefixe);
	}
	if (isset($creer_fichier)) {
		$lang = _request('choix_lang');
		atelier_creer_fichier_lang($prefixe,$lang);
	}
	if (isset($ajout_lang)) {
		$lang = _request('lang');
		$module = _request('module');
		$key = _request('key');
		$value = _request('value');
		atelier_ajout_lang($module,$lang,$enreg = array('key' => $key, 'value' => $value));
	}


        $redirect = parametre_url(urldecode(generer_url_ecrire('atelier_lang')),
				'id_projet', $id_projet, '&') . $err;

	include_spip('inc/headers');
	redirige_par_entete($redirect);
}


function atelier_creer_repertoire_lang($prefixe) {
	mkdir(_DIR_PLUGINS.$prefixe.'/lang');
}

function atelier_ajout_lang($module,$lang,$enreg) {
	$fichier = _DIR_PLUGINS.$module.'/lang/'.$module.'_'.$lang.'.php';

	$value = text_to_php($enreg['value']);

	$ligne = '\''.$enreg['key'].'\' => \''.$value.'\',';

	$lignes = file($fichier,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	array_splice($lignes, -2, 0, $ligne);

	$contenu = '';
	foreach ($lignes as $l) $contenu .= $l ."\n";

	ecrire_fichier($fichier,$contenu);
}

// prepare un texte pour inclusion dans fichier php
function text_to_php($value) {
	$value = preg_replace("#'#","\'",$value);
	$value = preg_replace("#é#","&eacute;",$value);
	$value = preg_replace("#è#","&egrave;",$value);
	$value = preg_replace("#à#","&agrave;",$value);
	return $value;
}

function atelier_creer_fichier_lang($prefixe,$lang) {

	$squel = 
'<?php 

// Fichier generer par le plugin Atelier

if(!defined("_ECRIRE_INC_VERSION")) return; 

$GLOBALS[$GLOBALS[\'idx_lang\']] = array (

);

?>';

	$fichier = _DIR_PLUGINS.$prefixe.'/lang/'.$prefixe.'_'.$lang.'.php';

	ecrire_fichier($fichier,$squel);

}
