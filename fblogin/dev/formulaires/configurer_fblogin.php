<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_fblogin_verifier_dist(){
	$erreurs = array();
	
	foreach (array('app_id', 'secret_key') as $obligatoire){
		if (!_request($obligatoire)){
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}
	
	return $erreurs;
}

?>
