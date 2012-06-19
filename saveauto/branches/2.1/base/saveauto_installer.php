<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'installation du plugin
 */
function saveauto_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;

	/**
	 * Installation de base
	 */
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		/**
		 * On insere une configuration de base pour que le plugin
		 * soit actif des son activation
		 */
		if(!is_array(unserialize($GLOBALS['meta']['saveauto']))){
			$config = array(
				'gz' => 'false',
				'structure' => 'true',
				'donnees' => 'true',
				'ecrire_succes' => 'true',
				'base' => $GLOBALS['connexions'][0]['db'],
				'jours_obso' => 15,
				'rep_bases' => 'tmp/',
				'prefixe_save' => 'saveauto_',
				'frequence_maj' => 1,
				'destinataire_save' => $GLOBALS['meta']['email_webmaster'],
				'eviter' => '_index;_temp;_cache',
				'mail_max_size' => 2
			);
			ecrire_meta('saveauto',serialize($config));
		}
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
	}
}

/**
 * Fonction de désinstallation
 * On supprime les trois metas du plugin :
 * - saveauto : la meta de configuration
 * - saveauto_creation : la meta de la date de dernière création d'archive
 * - saveauto_base_version : la meta du numero de version de la base
 */
function saveauto_vider_tables($nom_meta_base_version) {
	effacer_meta('saveauto');
	effacer_meta('saveauto_creation');
	effacer_meta($nom_meta_base_version);
}
?>