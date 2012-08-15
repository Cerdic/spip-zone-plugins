<?php
/**
 * Récupère la liste des sites accessibles par l'utilisateur 
 * sur le serveur Piwik configuré
 * 
 * Elle crée une meta spécifique 'piwik_sites_dispo' qui est un array serialisé
 * Utilise la fonction de communication avec l'API
 * 
 * @return 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_piwik_recuperer_liste(){
	include_spip('inc/config');
	$config = lire_config('piwik',array());
	$piwik_site = isset($config['urlpiwik']) ? $config['urlpiwik'] : false;
	$piwik_token = isset($config['token']) ? $config['token'] : false;
	
	if($piwik_site && $piwik_token){
		$piwik_url = 'http://'.$piwik_site.'/';
		
		$format = _request('format')?_request('format'):'PHP';
		
		$piwik_api = charger_fonction('piwik_recuperer_data','inc');
		
		/**
		 * Récupération de la liste des sites où cet utilisateur 
		 * a les droits d'admin
		 */
		$method = 'SitesManager.getSitesWithAdminAccess';
		$datas = $piwik_api($piwik_url,$piwik_token,'',$method,$format);
		ecrire_meta('piwik_sites_dispo', $datas);
	}
}
?>