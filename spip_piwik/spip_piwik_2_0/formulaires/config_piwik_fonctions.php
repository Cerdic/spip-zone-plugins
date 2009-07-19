<?php
/**
 * 
 * Fonction de verification du formulaire de configuration CFG
 * 
 */
function cfg_config_piwik_verifier(&$cfg){
	$piwik_token = $cfg->val['token'];
	if (!preg_match('/^[a-f0-9]{32}$/i',$piwik_token)) {
		$erreur['token'] = _T('piwik:cfg_erreur_token');
		return $erreur;
	}
	$piwik_url = 'http://'.$cfg->val['urlpiwik'].'/';
	
	$piwik_recuperer_data = charger_fonction('piwik_recuperer_data','inc');
	
	$method = 'SitesManager.getSitesWithAdminAccess';
	$datas = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method,'PHP');
	if(!is_array(unserialize($datas))){
		$erreur['urlpiwik'] = _T('piwik:cfg_erreur_recuperation_data');
	}
	
	ecrire_meta('piwik_sites_dispo', $datas);
	
	return $erreur;
}
?>