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
	$piwik_site = _request('urlsite');
	$piwik_token = _request('token');
	
	$piwik_url = 'http://'.$piwik_site.'/';
	
	if (!preg_match('/^[a-f0-9]{32}$/i',$piwik_token)) {
		$erreur = 'Invalid Piwik Token.';
	}
	$format = _request('format')?_request('format'):'PHP';
	$method = 'SitesManager.getSitesWithAdminAccess';
	
	$piwik_api = charger_fonction('inc/piwik_recuperer_data');
	$content = $piwik_api($piwik_url,$piwik_token,'API',$method,$format)
	
	ecrire_meta('piwik_sites_dispo', $content);
}
?>