<?php
/**
 * Récupère la liste des sites accessibles par l'utilisateur sur le serveur Piwik configuré
 * 
 * Elle crée une meta spécifique 'piwik_sites_dispo' qui est un array serialisé
 * 
 * @return 
 */
	
function action_piwik_recuperer_liste(){
	$piwik_site = _request('urlsite');
	$piwik_token = _request('token');
	
	$piwik_url = 'http://'.$piwik_site.'/';
	
	if (!preg_match('/^[a-f0-9]{32}$/i',$piwik_token)) {
		$erreur = 'Invalid Piwik Token.';
	}
	$format = _request('format')?_request('format'):'PHP';
	$method = 'SitesManager.getSitesWithAdminAccess';
	
	$url = parametre_url($piwik_url,'token_auth',$piwik_token);
	$url = parametre_url($url,'module','API','&');
	$url = parametre_url($url,'format',$format,'&');
	$url = parametre_url($url,'method',$method,'&');
	$url = parametre_url($url,'format',$format,'&');
	spip_log('URL = '.$url,'piwik');
	include_spip('inc/distant');
	$content = recuperer_page($url);
	ecrire_meta('piwik_sites_dispo', $content);
	spip_log($content,'piwik');
}
?>