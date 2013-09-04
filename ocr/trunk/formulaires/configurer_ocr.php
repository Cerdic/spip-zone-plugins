<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/config');

function formulaires_configurer_ocr_charger_dist(){
	//Recuperation de la configuration
	$ocr = lire_config('ocr',array());
	if (empty($ocr)) {
		$ocr = null;
	}

	/* Valeurs prealablement saisie ou par defaut/d'exemple 
	 * -* intervalle de 600s entre les lancements de CRON
	 * -* 5 fichiers analysés par CRON
	 * -* binaire de reconnaissance des caractères : /usr/bin/tesseract
	 * -* options du binaire : -l fra (modèle de langue : français)
	 * -* taille maximale du texte inséré dans la base de données
	 * */
	$valeur = array(
		'intervalle_cron' =>  $ocr['intervalle_cron'] ? $ocr['intervalle_cron'] : (defined('_OCR_INTERVALLE_CRON') ? _OCR_INTERVALLE_CRON : 600),
		'nb_docs' =>  $ocr['nb_docs'] ? $ocr['nb_docs'] : (defined('_OCR_NB_DOCS') ? _OCR_NB_DOCS : 5),
		'ocr_bin' => $ocr['ocr_bin'] ? $ocr['ocr_bin'] : (defined('_OCR_BIN') ? _OCR_BIN : '/usr/bin/tesseract'),
		'ocr_opt' => $ocr['ocr_opt'] ? $ocr['ocr_opt'] : (defined('_OCR_OPT') ? _OCR_OPT : '-l fra'),
		'taille_texte_max' => $ocr['taille_texte_max'] ? $ocr['taille_texte_max'] : (defined('_OCR_TAILLE_TEXTE_MAX') ? _OCR_TAILLE_TEXTE_MAX : 50000),
	);
}
function formulaires_configurer_ocr_verifier_dist(){
	$erreurs = array();
	//Il faut au moins une seconde
	if((!_request('intervalle_cron'))||(_request('intervalle_cron') < 1)){
		$erreurs['intervalle_cron'] = _T('ocr:erreur_intervalle_cron');
	}
	//Il faut au moins un document a la fois
	if((!_request('nb_docs'))||(_request('nb_docs') < 1)){
		$erreurs['nb_docs'] = _T('ocr:erreur_nb_docs');
	}
	//Il faut un nombre positif
	if((!_request('taille_texte_max'))||(_request('taille_texte_max') < 0)){
		$erreurs['taille_texte_max'] = _T('ocr:erreur_taille_texte_max');
	}
	/**
	 * On teste les binaires
	 */
	$binaire = 'ocr_bin';
	/**
	 * Pas de binaire => on doit en avoir un pour récupérer le contenu
	 */
	if(!_request($binaire)){
		$erreurs[$binaire] = _T('ocr:erreur_ocr_bin');
	}else{
		/**
		 * On teste avec la commande de base ...
		 * Le code de retour normal doit être 0
		 */
		@exec(_request($binaire),$retour_bin,$retour_bin_int);
		if($retour_bin_int != 0){
			/**
			 * Sinon on fait un test que le binaire est executable 
			 * Cela nécessite un chemin complet du binaire
			 */
			@exec('test -x '._request($binaire),$retour_bin,$retour_bin_int);
			if($retour_bin_int != 0){
				$erreurs[$binaire] = _T('ocr:erreur_binaire_indisponible');
			}
		}
	}
	
	if(count($erreurs) > 0){
		$erreurs['message_erreur'] = _T('ocr:erreur_verifier_configuration');
	}
	return $erreurs;
}

function formulaires_configurer_ocr_traiter_dist(){
	//Recuperation de la configuration et serialization
	$ocr = array(
		'intervalle_cron' => intval(_request('intervalle_cron')),
		'nb_docs' => intval(_request('nb_docs')),
		'taille_texte_max' => intval(_request('taille_texte_max')),
		
		'ocr_bin' => _request('ocr_bin'),
		'ocr_opt' => _request('ocr_opt'),
	);
	//Insere ou update ?
	ecrire_config('ocr',$ocr);
	$res = array('message_ok'=>_T('ocr:message_ok_configuration'));
	return $res;
	
}
?>
