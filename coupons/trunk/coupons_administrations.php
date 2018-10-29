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

	$maj['create'] = array(array('maj_tables', array('spip_coupons', 'spip_coupons_commandes')));
	$maj['1.3.0']  = array(
		array('maj_tables', array('spip_coupons')),
		array('coupons_update_statut'),
	);
	$maj['1.4.0']  = array(
		array('maj_tables', array('spip_coupons')),
		array('coupons_update_date_validite'),
	);
	$maj['2.0.0']  = array(
		array('maj_tables', array('spip_coupons', 'spip_coupons_commandes')),
		array('coupons_update_coupons_commandes'),
	);
	$maj['2.0.1']  = array(
		array('maj_tables', array('spip_coupons')),
		array('coupons_update_coupons_id_commandes'),
	);
	$maj['2.1.0']  = array(
		array('maj_tables', array('spip_coupons')),
	);
	$maj['2.2.0']  = array(
		array('maj_tables', array('spip_coupons')),
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

/**
 * Mettre à jour le nouveau champ "actif" sur les coupons pas encore utilisés
 */
function coupons_update_statut() {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('coupons');
	if($desc['field']['id_commande']) {
		sql_updateq(
			'spip_coupons',
			array(
				'actif' => 'on',
			),
			'id_commande = 0'
		);
		sql_updateq(
			'spip_coupons',
			array(
				'actif' => '',
			),
			'id_commande <> 0'
		);
	}
}

/**
 * Mettre à jour le nouveau champ "date_validite" (par défaut +365 jours)
 */
function coupons_update_date_validite() {
	// durée de validite de 365 jours par défaut
	ecrire_config('coupons/duree_validite', 365);
	// on met à jour les dates de validité de tous les coupons
	sql_query('update spip_coupons set date_validite = DATE_ADD(DATE,INTERVAL 365 DAY)');
}

/**
 * Créer une ligne dans coupons_commandes par utilisation du coupon
 */
function coupons_update_coupons_commandes() {
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('coupons');
	if($desc['field']['id_commande']) {
		$coupons = sql_allfetsel('*', 'spip_coupons', 'id_commande <> 0');
		foreach ($coupons as $coupon) {
			$data = array(
				'id_coupon'   => $coupon['id_coupon'],
				'id_commande' => $coupon['id_commande'],
				//'id_auteur'   => '',
				'montant'     => $coupon['montant'],
				'maj'         => $coupon['maj'],
			);
			sql_insertq('spip_coupons_commandes', $data);
		}
		sql_query('ALTER TABLE spip_coupons DROP id_commande');
	}
}

/**
 * Renommer id_commandes_detail en id_commandes_detail
 */
function coupons_update_coupons_id_commandes() {
	sql_query('update spip_coupons set id_commandes_detail = id_commandes_detail_origine');
	sql_query('ALTER TABLE spip_coupons DROP id_commandes_detail_origine');
}