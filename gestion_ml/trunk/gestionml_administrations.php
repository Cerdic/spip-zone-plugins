<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_version_base
 * @param string $version_cible
 */
function gestionml_upgrade($nom_meta_version_base,$version_cible){
	$maj = array();

	include_spip('inc/config');
   $maj = array();
	$maj['create'] = array(
			array('ecrire_config','gestionml', array(
				'hebergeur' => '0',
				'serveur_distant' => 'https://www.ovh.com/soapi/soapi-re-1.38.wsdl',
				'domaine' => 'mondomaine.tld',
				'identifiant' => '',
				'mot_de_passe' => '',
				'cacher_admin_restreints' => '',
	)));
	include_spip('base/upgrade');
   maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 *
 * @param float $nom_meta_version_base
 */
function gestionml_vider_tables($nom_meta_version_base) {
   effacer_meta('gestionml');
	effacer_meta($nom_meta_version_base);
}

?>