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
	
	/**
	 * Vérifier que ce token est un token admin
	 * Si non : mettre une meta comme quoi il n'est pas admin pour créer des sites
	 */
	$method_verif_user = 'UsersManager.getUsers';
	$datas_user = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method_verif_user,'PHP');
	$datas_user = unserialize($datas_user);
	if(is_array($datas_user) && ($datas_user['result'] == 'error')){
		ecrire_meta('piwik_admin', 'non');
	}else{
		ecrire_meta('piwik_admin', 'oui');
	}
	
	/**
	 * Récupération de la liste des sites où cet utilisateur 
	 * a les droits d'admin
	 */
	$method = 'SitesManager.getSitesWithAdminAccess';
	$datas = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method,'PHP');
	if(!is_array(unserialize($datas))){
		$erreur['urlpiwik'] = _T('piwik:cfg_erreur_recuperation_data');
	}
	
	ecrire_meta('piwik_sites_dispo', $datas);
	
	return $erreur;
}
?>