<?php
/**
 * Plugin Piwik
 * 
 * @package SPIP\Piwik\Pipelines
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline insert_head (SPIP)
 * 
 * Ajout du code de piwik dans le head si configuré comme tel
 * 
 * @param string $flux
 * 		Le contenu de la balise #INSERT_HEAD
 * @return string $flux
 * 		Le contenu de la balise #INSERT_HEAD modifié
 * 
 */
function piwik_insert_head($flux){
	$options = array();
	
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	if(lire_config('piwik/mode_insertion') == 'pipeline'){
		$options['type'] = 'public';
		$flux .= piwik_head_js($options);
	}

	return $flux;
}

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * 
 * Insertion du code de Piwik dans l'espace privé si configuré comme tel
 *
 * @param string $flux
 * 		Le contenu du head privé
 * @return string $flux
 * 		Le contenu du head privé modifié
 */
function piwik_header_prive($flux){
	$options = array();
	
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	
	if(lire_config('piwik/piwik_prive')){
		if(is_array(lire_config('piwik/restreindre_statut_prive'))){
			$options['statuts_restreints'] = lire_config('piwik/restreindre_statut_prive');
		}
		if(is_array(lire_config('piwik/restreindre_auteurs_prive'))){
			$options['auteurs_restreints'] = lire_config('piwik/restreindre_auteurs_prive');
		}
		$options['type'] = 'prive';
		$flux .= piwik_head_js($options);
	}
	return $flux;
}

/**
 * La fonction de génération du code du tracker javascript
 *
 * @param array $options [optional]
 * 		
 * @return
 */
function piwik_head_js($options=array()){
	if(!function_exists('lire_config'))
		include_spip('inc/config');

	$config = lire_config('piwik',array('id_piwik'=>false,'urlpiwik'=>false));
	$id_piwik = $config['idpiwik'];
	$url_piwik = $config['urlpiwik'];
	$afficher_js = true;

	$ret = '';

	if($url_piwik && $id_piwik){
		if((isset($options['statut_restreint']) && $options['statut_restreint']) || (isset($options['auteurs_restreints']) && $options['auteurs_restreints'])){
			$statut = isset($GLOBALS['visiteur_session']['statut']) ? $GLOBALS['visiteur_session']['statut'] : '';
			$id_auteur = isset($GLOBALS['visiteur_session']['id_auteur']) ? $GLOBALS['visiteur_session']['id_auteur'] : '';
			if(in_array($statut,$options['statuts_restreints'])){
				$afficher_js = false;
			}
			if($afficher_js && in_array($id_auteur,$options['auteurs_restreints'])){
				$afficher_js = false;
			}
		}

		if($afficher_js){
			$ret .= "
				<script type='text/javascript'>var _paq = _paq || []; 
					(function(){ var u=(('https:' == document.location.protocol) ? 'https://$url_piwik/' : 'http://$url_piwik/'); 
					_paq.push(['setSiteId', $id_piwik]); 
					_paq.push(['setTrackerUrl', u+'piwik.php']); 
					_paq.push(['trackPageView']); 
					_paq.push(['enableLinkTracking']); 
					var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.defer=true; g.async=true; g.src=u+'piwik.js'; 
					s.parentNode.insertBefore(g,s); })();
				</script>";
		}
	}

	return $ret;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * On vérifie les valeurs des champs fournis
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié auquel on a ajouté nos erreurs potentielles
 */
function piwik_formulaire_verifier($flux){
	if($flux['args']['form'] == 'configurer_piwik'){
		$obligatoires = array('token','user','urlpiwik');
		foreach($obligatoires as $obligatoire){
			if(!_request($obligatoire)){
				$flux['data'][$obligatoire] = _T('info_obligatoire');
			}
		}
		
		$piwik_token = _request('token');
		if (!$flux['data']['token'] && !preg_match('/^[a-f0-9]{32}$/i',$piwik_token)) {
			$flux['data']['token'] = _T('piwik:cfg_erreur_token');
			return $flux;
		}
		$piwik_url = 'http://'._request('urlpiwik').'/';
		
		$piwik_recuperer_data = charger_fonction('piwik_recuperer_data','inc');
		
		/**
		 * Vérifier la correspondance nom d'utilisateur/ token
		 * Qui nous permettra par la suite de définir d'autres choses
		 */
		$method_verif_user = 'UsersManager.getUser';
		$options_user = array('userLogin'=>_request('user'));
		$datas_user = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method_verif_user,'PHP',$options_user);
		if(is_array($datas_user = unserialize($datas_user))){
			if(!$flux['data']['user'] && $flux['data']['result'] == 'error'){
				$flux['data']['user'] = _T('piwik:cfg_erreur_user_token');
			}
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
				unset($flux['data']['user']);
			}
		}
	
		/**
		 * Récupération de la liste des sites où cet utilisateur 
		 * a les droits d'admin
		 */
		$method = 'SitesManager.getSitesWithAdminAccess';
		$datas = $piwik_recuperer_data($piwik_url,$piwik_token,'',$method,'PHP');
		if(!$flux['data']['urlpiwik'] && !is_array(unserialize($datas))){
			$flux['data']['urlpiwik'] = _T('piwik:cfg_erreur_recuperation_data');
		}else{
			ecrire_meta('piwik_sites_dispo', $datas);	
		}
	}
	return $flux;
}
?>
