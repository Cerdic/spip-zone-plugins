<?php
/**
 * Plugin Piwik
 * 
 * @package SPIP\Piwik\Installation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Action d'installation et de mise à jour
 * 
 * @param string $nom_meta_version_base
 * 		Le nom de la méta de version dans spip_meta
 * @param float $version_cible
 * 		Le numéro de version vers laquelle effectuer la maj
 */
function piwik_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0','=')){
			/**
			 * Si la configuration est présente on récupère la liste des sites
			 */
			if(is_array($config = lire_config('piwik',''))
				&& isset($config['urlpiwik']) && isset($config['token'])
			){
				$piwik_recuperer_data = charger_fonction('piwik_recuperer_data','inc');
				
				/**
				 * Récupération de la liste des sites où cet utilisateur 
				 * a les droits d'admin
				 */
				$method = 'SitesManager.getSitesWithAdminAccess';
				$datas = $piwik_recuperer_data($config['urlpiwik'],$config['token'],'',$method,'PHP');
				if(is_array(unserialize($datas))){
					ecrire_meta('piwik_sites_dispo', $datas);
				}
			}

			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
	}
}

/**
 * Action de désinstallation
 * -* Effacer la configuration
 * -* Effacer la liste des sites dispo
 * -* Effacer la meta de version
 * 
 * @param float $nom_meta_version_base
 */
function piwik_vider_tables($nom_meta_version_base){
	effacer_meta('piwik');
	effacer_meta('piwik_sites_dispo');
	effacer_meta($nom_meta_version_base);
}

?>