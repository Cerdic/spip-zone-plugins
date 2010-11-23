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
	 * Vérifier la correspondance nom d'utilisateur/ token
	 * Qui nous permettra par la suite de définir d'autres choses
	 */
	$method_verif_user = 'UsersManager.getUser';
	$options_user = array('userLogin'=>$cfg->val['user']);
	$datas_user = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method_verif_user,'PHP',$options_user);
	if(is_array($datas_user = unserialize($datas_user))){
		if($datas_user['result'] == 'error'){
			$erreur['user'] = _T('piwik:cfg_erreur_user_token');
		}else{
			/**
			 * Vérifier que ce token est un token admin
			 * Si non : mettre une meta comme quoi il n'est pas admin pour créer des sites
			 */
			$method_verif_user_bis = 'UsersManager.getUsers';
			$datas_user_bis = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method_verif_user_bis,'PHP');
			$datas_user_bis = unserialize($datas_user_bis);
			if(is_array($datas_user_bis) && ($datas_user_bis['result'] == 'error')){
				ecrire_meta('piwik_admin', 'non');
			}else{
				ecrire_meta('piwik_admin', 'oui');
			}
		}	
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