<?php
// Securite
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_sjcycle_charger(){

	// verifier avant tout la config de SPIP
	$erreurs = array();
	// choix de la methode de traitement des images par le serveur
	if (!lire_config('image_process')){
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
		return $erreurs;
	}
	// Generation de miniatures des images inactive
	if (lire_config('creer_preview')!='oui') {
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
		return $erreurs;
	}
	return $erreurs;
	
}
?>