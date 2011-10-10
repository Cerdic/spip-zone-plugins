<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_config_sjcycle_pre_verifier(&$cfg){
	// verif des valeurs du formulaire 
	$erreurs = array();
		
	if (!lire_config('image_process')){
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_image_process');
		return $cfg->ajouter_erreurs($erreurs);
	}
	
	//Generation de miniatures des images inactive
	if (lire_config('creer_preview')!='oui') {
		$erreurs['message_erreur'] = _T('sjcycle:erreur_config_creer_preview');
		return $cfg->ajouter_erreurs($erreurs);
	}

	return $cfg->ajouter_erreurs($erreurs);
}

?>