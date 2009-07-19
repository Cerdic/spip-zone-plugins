<?php
    function action_piwik_creer_site(){
    	$piwik_site = _request('urlsite');
		$piwik_token = _request('token');
	
		$piwik_url = 'http://'.$piwik_site.'/';
	
    	$options['siteName'] = $GLOBALS['meta']['nom_site'];
		$options['urls'] = $GLOBALS['meta']['adresse_site'];
    	$methode = 'SitesManager.addSite';
		
		$piwik_recuperer_data = charger_fonction('piwik_recuperer_data','inc');

		$datas = $piwik_recuperer_data($piwik_url,$piwik_token,'',$methode,'PHP',$options);
		
		$methode_bis = 'SitesManager.getSitesWithAdminAccess';
		$datas_bis = $piwik_recuperer_data($piwik_url,$piwik_token,'',$methode_bis,'PHP');
		ecrire_meta('piwik_sites_dispo', $datas_bis);
    }
?>