<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Coupons de réduction
 *
 * @plugin     Coupons de réduction
 * @copyright  2017
 * @author     Nicolas Dorigny
 * @licence    GNU/GPL
 * @package    SPIP\Coupons\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('base/upgrade');
include_spip('base/coupons');
include_spip('inc/cextras');
include_spip('base/create');

/**
 * Fonction d'installation et de mise à jour du plugin Coupons de réduction.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 *
 * @return void
 **/
function coupons_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_coupons')));
	$maj['1.3.0'] = array(
		array('maj_tables', array('spip_coupons')),
		array('coupons_update_statut')
	);

	// Créer les champs extras
	cextras_api_upgrade(coupons_declarer_champs_extras(), $maj['create']);

	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin Coupons de réduction.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 *
 * @return void
 **/
function coupons_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_coupons');

	cextras_api_vider_tables(coupons_declarer_champs_extras());
	
	effacer_meta($nom_meta_base_version);
}

function coupons_update_statut(){
	sql_updateq(
		'spip_coupons',
		array(
			'actif' => 'on'
		),
		'id_commande = 0'
	);
	sql_updateq(
		'spip_coupons',
		array(
			'actif' => ''
		),
		'id_commande <> 0'
	);
}