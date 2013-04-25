<?php
/**
 * Plugin Smush
 * 
 * Auteur :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * @package SPIP\Smushit\Administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'installation du plugin 
 * 
 * @param string $nom_meta_base_version
 * 		Le nom de la meta d'installation
 * @param float $version_cible
 * 		La version du schéma d'installation
 * @return void
 */
function smush_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('smush_install_recuperer_infos',array())
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * 
 * Désinstallation du plugin
 * 
 * On supprime : 
 * -* Les metas de configuration
 * -* Les metas de vérification des programmes
 * 
 * @param float $nom_meta_base_version
 * @return void
 */
function smush_vider_tables($nom_meta_base_version) {
	effacer_meta('imagick_casse');
	effacer_meta('pngnq_casse');
	effacer_meta('optipng_casse');
	effacer_meta('jpegtran_casse');
	effacer_meta('smush_casse');
	effacer_meta('smush');
	effacer_meta($nom_meta_base_version);
}

function smush_install_recuperer_infos(){
	/**
	 * On vire ces metas qui peuvent exister
	 */
	effacer_meta('imagick_casse');
	effacer_meta('pngnq_casse');
	effacer_meta('optipng_casse');
	effacer_meta('jpegtran_casse');
	effacer_meta('smush_casse');
	
	include_spip('inc/smush_verifier_binaires');
	
	tester_convert();
	tester_jpegtran();
	tester_optipng();
	tester_pngnq();
	tester_global();
	
	/**
	 * On invalide le cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("1");
}
?>