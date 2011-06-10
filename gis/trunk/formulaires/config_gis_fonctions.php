<?php 
/**
 * Fonctions spécifiques au formulaire de configuration de GIS
 * Utilisé notamment pour l'insertion dans les pipelines de CFG
 */

/**
 * Fonction de verification du formulaire de configuration CFG
 * - On vérifie si dans les cas de cloudmade ou de google (v2), une clé a 
 * été fournie
 */
function cfg_config_gis_verifier(&$cfg){
	if(in_array($cfg->val['api'],array('cloudmade','google'))){
		$obligatoire = "api_key_".$cfg->val['api'];
		if(!$cfg->val[$obligatoire]){
			$erreur[$obligatoire] = _T('info_obligatoire');
		}
	}
	
	return $erreur;
}

/**
 * Fonction de post-traitement du formulaire de configuration CFG
 * - Si l'API de carto a été mofifiée, on invalide et recharge la page
 */
function cfg_config_gis_post_traiter(&$cfg){
	$modifs = $cfg->log_modif;
	if(preg_match('/api/', $modifs, $matches)){
		include_spip('inc/invalideur');
		suivre_invalideur('1');
		/**
		 * On redirige le formulaire pour rafraichir la page
		 */
		$cfg->messages['redirect'] = self();
	}
}

?>