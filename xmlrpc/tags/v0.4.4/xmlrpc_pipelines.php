<?php
/**
 * Plugin xmlrpc
 * 
 * Auteurs : kent1 (http://www.kent1.info)
 * © 2011 - GNU/GPL v3
 * 
 * Fichier des pipelines du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * On ajoute le link rel vers le RSD que si le serveur est ouvert
 * 
 * @param $flux le contexte du pipeline
 * @return $flux le contexte modifié
 */
function xmlrpc_insert_head($flux){
	$options = @unserialize($GLOBALS['meta']['xmlrpc']);
	if(!is_array($options) OR (($options['ferme'] != 'on') && ($options['desactiver_rsd'] != 'on'))){
		$url_rsd = url_absolue(parametre_url(generer_url_action('xmlrpc_serveur'),'rsd','rsd','&'));
		$flux .= '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="'.$url_rsd.'" />';
	}
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_pre_methode (pipeline xmlrpc)
 * On s'insère avant l'appel d'une méthode
 * 
 * @param $flux le contexte du pipeline
 * @return $flux le contexte modifié
 */
function xmlrpc_xmlrpc_pre_methode($flux){
	global $spip_xmlrpc_serveur;
	if(!$spip_xmlrpc_serveur)
		return false;
		
	/**
	 * On vérifie d'abord si le serveur est ouvert
	 */
	$access = $spip_xmlrpc_serveur->verifier_access();
	if(!$access){
		$flux = $spip_xmlrpc_serveur->error;
		return $flux;
	}
	
	if(is_array($flux['args']['arguments'][0])){
		$flux['args']['arguments'] = $flux['args']['arguments'][0];
	}

	/**
	 * Ensuite on vérifie l'identification
	 * Elle crée une session
	 */
	$username = isset($flux['args']['arguments']['login']) ? $flux['args']['arguments']['login'] : $flux['args']['arguments'][1];
	$password = isset($flux['args']['arguments']['pass']) ? $flux['args']['arguments']['pass'] : $flux['args']['arguments'][2];
	if(!is_array($GLOBALS['visiteur_session']) && isset($username) && isset($password)){
		$args_aut = array($username,$password);
		$auth = $spip_xmlrpc_serveur->auth($args_aut);
		if(!$auth)
			$flux = $spip_xmlrpc_serveur->error;
	}
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_post_methode (pipeline xmlrpc)
 * On s'insère après l'appel d'une méthode
 * 
 * @param $flux le contexte du pipeline
 * @return $flux le contexte modifié
 */
function xmlrpc_xmlrpc_post_methode($flux){
	global $spip_xmlrpc_serveur;
	
	return $flux;
}

/**
 * Insertion dans le pipeline xmlrpc_apis (xmlrpc)
 * On ajoute la prise en compte de l'API SPIP
 * 
 * @param $flux : le contexte du pipeline, un array des APIs disponibles
 * @return $flux : le contexte du pipeline auquel on a ajouté nos nouvelles APIs
 */
function xmlrpc_xmlrpc_apis($flux){
	$flux[] = 'spip';
	
	return $flux;
}
?>