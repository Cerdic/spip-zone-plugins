<?php 

function cfg_config_gestionml_verifier(&$cfg){
   // vérif des valeurs 
	$erreurs = array() ;
	
	if( $cfg->val['hebergeur'] != "0" ) {
		$erreurs = gestionml_api_tester($cfg->val['serveur_distant'], $cfg->val['identifiant'], $cfg->val['mot_de_passe']) ;
	}
	return $cfg->ajouter_erreurs($erreurs);
}

?>