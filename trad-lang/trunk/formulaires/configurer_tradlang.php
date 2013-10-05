<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_tradlang_charger_dist(){
	$valeurs = @unserialize($GLOBALS['meta']['tradlang']);
	if (!is_array($valeurs))
		$valeurs = array();

	include_spip('inc/lang_liste');
	include_spip('tradlang_fonctions');
	$valeurs['_langues_possibles'] = $GLOBALS['codes_langues'];
	return $valeurs;
}

function formulaires_configurer_tradlang_verifier_dist(){
	$erreurs = array();
	if(($langues_autorisees = _request('langues_autorisees')) && (count($langues_autorisees)<2))
		$erreurs['langues_autorisees'] = _T('tradlang:erreur_langues_autorisees_insuffisantes');
	$limite_trad = _request('seuil_export_tradlang');
	if(!is_numeric($limite_trad) || (intval($limite_trad) < 0) || (intval($limite_trad) > 100))
		$erreurs['seuil_export_tradlang'] = _T('tradlang:erreur_limite_trad_invalide');
	return $erreurs;
}

function formulaires_configurer_tradlang_traiter_dist(){
	$res = array('editable'=>true);
	foreach(array(
		"sauvegarde_locale",
		"sauvegarde_post_edition",
		"seuil_export_tradlang",
		"langues_autorisees",
		"limiter_langues_bilan",
		"limiter_langues_bilan_nb",
		"configurer_type",
		"configurer_statuts",
		"configurer_auteurs",
		"modifier_type",
		"modifier_statuts",
		"modifier_auteurs",
		"voir_type",
		"voir_statuts",
		"voir_auteurs"
		) as $m){
			if (!is_null($v=_request($m)))
				$config[$m] = _request($m);
		}
		$priorites = explode(';',_TRAD_PRIORITES);
		foreach($priorites as $priorite){
			$priorite = str_replace(' ','_',supprimer_numero($priorite));
			if (!is_null($v=_request($priorite)))
				$config[$priorite] = _request($priorite);
		}
		ecrire_meta('tradlang',serialize($config));
	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

function tradlang_test_repertoire_local(){
	global $dossier_squelettes;
	if(!$dossier_squelettes && !is_dir(_DIR_RACINE.'squelettes'))
		return false;
	else
		$squelettes = $dossier_squelettes ? $dossier_squelettes : _DIR_RACINE.'squelettes/';
	if(!is_dir($dir_lang=$squelettes.'lang'))
		return false;
	
	return $dir_lang;
}