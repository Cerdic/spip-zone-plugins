<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne la liste des champs d'identité extra du site
 * 
 * @pipeline_appel
 * @return array Liste des champs
 **/
function identite_extra_champs() {
	static $champs = null;
	
	if (is_null($champs)) {
		// Les champs par défaut
		$champs = array('nom_organisation', 'telephone', 'adresse', 'ville', 'code_postal', 'region', 'pays');
		
		// On garde la compatibilité avec l'ancienne manière de les lister
		if (isset($GLOBALS['identite_extra']) and is_array($GLOBALS['identite_extra'])) {
			$champs = array_merge($champs, $GLOBALS['identite_extra']);
		}
		
		// On passe dans un pipeline pour augmenter plus proprement qu'avec une globale
		$champs = pipeline('identite_extra_champs', $champs);
		
		// On vire doublons éventuels
		$champs = array_unique($champs);
	}
	
	return $champs;
}
