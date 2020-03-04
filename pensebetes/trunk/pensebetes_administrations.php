<?php
/**
 * Définit l'administration du plugin Pensebetes
 *
 * Installation et désinstallation du plugin
 *
 * @plugin     Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\Pensebetes\Administrations
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Création et mise à jour du plugin Pensebetes
 *
 * @param  string $nom_meta_base_version version du schéma de données du plugin installé
 * @param  string $version_cible Version déclarée dans paquet.xml
 * @return void
**/

function pensebetes_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_pensebetes', 'spip_pensebetes_liens')),
		array('ecrire_config','pensebetes/mes_objets', array('article')),
		array('ecrire_config','pensebetes/mes_lieux', array('accueil')),
		array('ecrire_config','pensebetes/mes_boites', array('accueil','auteur')),
	);
	$maj['1.0.1'] = array(
		array('ecrire_config','pensebetes/mes_objets', array('article')),
		array('ecrire_config','pensebetes/mes_lieux', array('accueil')),
		array('ecrire_config','pensebetes/mes_boites', array('accueil','auteur')),
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Faire le ménage lors de la désinstallation du plugin Pensebetes
 *
 * @param  string $nom_meta_base_version version du schéma de données du plugin installé
 * @return void
**/

function pensebetes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_pensebetes");
	sql_drop_table("spip_pensebetes_liens");
	effacer_config("pensebetes/mes_objets");
	effacer_config("pensebetes/mes_lieux");
	effacer_config("pensebetes/mes_boites");
	effacer_meta($nom_meta_base_version);
}

