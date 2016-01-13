<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin cp
 *
 * @plugin     codes_postaux
 * @copyright  2014
 * @author     guillaumeW
 * @licence    GNU/GPL
 * @package    SPIP\Codes_postaux\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin cp.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/

function codes_postaux_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_codes_postaux')),
		array('codes_postaux_peupler_base')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}



function codes_postaux_peupler_base()
{
include_spip('inc/config');

}

/**
 * Fonction de désinstallation du plugin cp.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function codes_postaux_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_codes_postaux");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('code_postal')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('code_postal')));
	sql_delete("spip_forum",                 sql_in("objet", array('code_postal')));


	effacer_meta($nom_meta_base_version);
}



?>