<?php
/**
 * Fonction de création de site dans le serveur piwik
 * 
 * Utilise l'api de piwik pour ajouter un site dans le serveur 
 * Le nouveau site aura le nom du site SPIP (la traduction dans la langue 
 * en cours si le site est dans un bloc multi)
 * 
 * @return 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_piwik_creer_site_dist(){
	include_spip('inc/filtres');
	$piwik_site = _request('urlsite');
	$piwik_token = _request('token');

	$piwik_url = 'http://'.$piwik_site.'/';

	$options['siteName'] = extraire_multi($GLOBALS['meta']['nom_site']);
	$options['urls'] = $GLOBALS['meta']['adresse_site'];
	
	$piwik_recuperer_data = charger_fonction('piwik_recuperer_data','inc');
	
	$methode = 'SitesManager.addSite';
	$datas = $piwik_recuperer_data($piwik_url,$piwik_token,'',$methode,'PHP',$options);
	
	$methode_bis = 'SitesManager.getSitesWithAdminAccess';
	$datas_bis = $piwik_recuperer_data($piwik_url,$piwik_token,'',$methode_bis,'PHP');
	ecrire_meta('piwik_sites_dispo', $datas_bis);
}
?>