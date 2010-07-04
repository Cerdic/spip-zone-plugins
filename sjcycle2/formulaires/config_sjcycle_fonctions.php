<?php 

function cfg_config_sjcycle_pre_verifier(&$cfg){
   // vérif des valeurs 
	$erreurs = array();
		
	if (!lire_config('image_process')){
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
		return $cfg->ajouter_erreurs($erreurs);
	}

	if (lire_config('creer_preview')!='oui') {//Génération de miniatures des images inactive
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
		return $cfg->ajouter_erreurs($erreurs);
	}

	return $cfg->ajouter_erreurs($erreurs);
}

?>