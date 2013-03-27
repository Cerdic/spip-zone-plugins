<?php
/**
 * Fichier d'installation / upgrade et désinstallation du plugin Multilang
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'upgrade/maj
 * On crée une configuration par défaut
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function multilang_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	
	$maj['create'] = array(
		array('multilang_creer_config'),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

function multilang_creer_config(){
	include_spip('inc/config');
		$config = lire_config('multilang');
	if (!is_array($config))
		$config = array();
	$config_defaut = array_merge(array(
			'siteconfig' => 'on',
			'article' => '',
			'breve' => '',
			'rubrique' => 'on',
			'auteur' => 'on',
			'document' => 'on',
			'motcle' => '',
			'site' => '',
			'evenement' => ''
	), $config);
	ecrire_meta('multilang', serialize($config_defaut));
}
/**
 * Fonction de desinstallation
 * On efface uniquement la méta d'installation
 *
 * @param float $nom_meta_base_version
 */
function multilang_vider_tables($nom_meta_base_version) {
	effacer_meta('multilang');
	effacer_meta($nom_meta_base_version);
}

?>