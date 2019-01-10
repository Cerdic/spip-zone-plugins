<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Prix Objets
 *
 * @plugin     Prix Objets
 * @copyright  2012 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Prix Objets.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL
 *
 * @param string $nom_meta_base_version
 *        	Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *        	Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 *
 */
function prix_objets_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('inc/config');

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_prix_objets'))
	);
	$maj['1.1.0']  = array(
		array('sql_alter','TABLE spip_prix_objets RENAME TO spip_prix_objets')
	);
	$maj['1.1.2']  = array(
		array('sql_alter','TABLE spip_prix_objets CHANGE prix prix_ht float (38,2) NOT NULL'),
		array('maj_tables', array('spip_prix_objets')),
	);
	$maj['1.1.3']  = array(
		array('sql_alter','TABLE spip_prix_objets CHANGE prix prix float (38,2) NOT NULL'),
	);
	$maj['1.1.4']  = array(
		array('sql_alter','TABLE spip_prix_objets CHANGE id_prix id_prix_objet bigint(21) NOT NULL'),
	);
	$maj['1.1.5'] = array(array('maj_tables', array('spip_prix_objets')));
	$maj['1.2.4'] = array(array('maj_tables', array('spip_prix_objets')));
	$maj['1.3.0'] = array(array('maj_tables', array('spip_prix_objets')));
	$maj['1.4.0'] = array(
		array('ecrire_config', 'prix_objets',lire_config('shop_prix',array())),
		array('ecrire_config', 'shop_prix',array()),
		array('effacer_meta', 'shop_prix_base_version')
	);
	$maj['1.5.0']  = array(
		array('sql_alter','TABLE spip_prix_objets CHANGE prix prix decimal(15,2) NOT NULL DEFAULT "0.00"'),
		array('sql_alter','TABLE spip_prix_objets CHANGE prix_ht prix_ht decimal(15,2) NOT NULL DEFAULT "0.00"'),
	);
	$maj['2.0.0']  = array(
		array('maj_tables', array('spip_prix_objets')),
		array('sql_alter','TABLE spip_prix_objets DROP INDEX id_objet'),
		array('sql_alter','TABLE spip_prix_objets ADD INDEX `id_objet` (`id_objet`,`id_prix_objet_source`,`objet`,`id_extension`,`extension`)'),
		array('po_upgrade','2.0.0'),
	);
	$maj['2.1.1']  = array(
		array('maj_tables', array('spip_prix_objets')),
	);
	$maj['2.1.4']  = array(
		array('maj_tables', array('spip_prix_objets')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Prix Objets.
 *
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin.
 *
 * @param string $nom_meta_base_version
 *        	Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 *
 */
function prix_objets_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_prix_objets");

	effacer_meta($nom_meta_base_version);
}
