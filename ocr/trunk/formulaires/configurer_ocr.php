<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_ocr_charger_dist(){
	//Recuperation de la configuration
	$ocr = @unserialize($GLOBALS['meta']['ocr']);
	if(!is_array($ocr)){
		$ocr = array();
	}
	//Valeurs prealablement saisie ou par defaut/d'exemple 
	$valeur = array(
		'intervalle_cron' =>  $ocr['intervalle_cron'] ? $ocr['intervalle_cron'] : 600,
		'nb_docs' =>  $ocr['nb_docs'] ? $ocr['nb_docs'] : 5,
		'ocr_bin' => $ocr['ocr_bin'] ? $ocr['ocr_bin'] : '/usr/bin/tesseract',
		'ocr_opt' => $ocr['ocr_opt'] ? $ocr['ocr_opt'] : '-fra',
	);
	return $valeur;
}
function formulaires_configurer_ocr_verifier_dist(){
	$erreurs = array();
	//Il faut au moins une seconde
	if((!_request('intervalle_cron'))||(_request('intervalle_cron') < 1)){
		$erreurs['intervalle_cron'] = _T('ocr:erreur_intervalle_cron');
	}
	//Il faut au moins une documents a la fois
	if((!_request('nb_docs'))||(_request('nb_docs') < 1)){
		$erreurs['nb_docs'] = _T('ocr:erreur_nb_docs');
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
	$ocr = serialize(array(
		'intervalle_cron' => intval(_request('intervalle_cron')),
		'nb_docs' => intval(_request('nb_docs')),
		
		'ocr_bin' => _request('ocr_bin'),
		'ocr_opt' => _request('ocr_opt'),
	));
	//Insere ou update ?
	ecrire_meta('ocr',$ocr);
	$res = array('message_ok'=>_T('ocr:message_ok_configuration'));
	return $res;
	
}
?>
