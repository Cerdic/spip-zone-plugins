<?php
/**
 * Plugin Smush
 * 
 * Auteur :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * Formulaire de test sur une image
 * 
 * @package SPIP\SPIPicious\Formulaires
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement du formulaire
 * 
 * @return array $valeurs
 * 		Les valeurs chargées dans le formulaire
 */
function formulaires_smush_testeur_charger() {
	$valeurs = array();
	return $valeurs;
}

/**
 * Verification du formulaire
 * 
 * @return array $valeurs
 * 		Les valeurs chargées dans le formulaire
 */
function formulaires_smush_testeur_verifier() {
	$erreurs = array();
	return $erreurs;
}

/**
 * Traitement du formulaire
 * 
 * @return array
 * 		Le tableau de tous les CVT avec editable et message
 */
function formulaires_smush_testeur_traiter() {
	$res = array('editable'=>true);
	if($source = _request('url_test')){
		include_spip('inc/distant');
		include_spip('inc/documents');
		include_spip('inc/renseigner_document');
		
		if (is_array($a = renseigner_source_distante($source))) {
			spip_log($a,'test.'._LOG_ERREUR);
			if(in_array($a['extension'],array('jpg','png','gif'))){
				if(!file_exists($a['fichier']))
					$fichier = copie_locale($a['fichier']);
				else
					$fichier = $a['fichier'];
				if($fichier)
					$res['message_ok'] = find_in_path($fichier);
				else
					$res['message_erreur'] = _T('smush:erreur_copie_locale',array('src'=> _request('url_test')));
			}
			else {
				if(file_exists($a['fichier']))
					spip_unlink($a['fichier']);
				$res['message_erreur'] = _T('smush:erreur_pas_image',array('src'=> _request('url_test')));
			}
		}else
			$res['message_erreur'] = _T('smush:erreur_copie_locale',array('src'=> _request('url_test')));
	}
	return $res;
}
?>