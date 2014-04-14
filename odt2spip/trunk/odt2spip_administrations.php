<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'installation du plugin
 */
function odt2spip_upgrade($nom_meta_base_version,$version_cible) {
	$maj = array();

	// Déclaration des valeurs par défaut de chaque variable de config
	$defaut = odt2spip_declarer_config();

	// On considère que la configuration existante n'est plus utile étant donnés les changements
	// donc on se contente de la supprimer tout simplement (permet d'éviter un souci si le plugin
	// n'a pas été désinstallé comme précisé dans la documentation)
	$maj['create'] = array(
		array('effacer_meta', 'odt2spip'),
		array('effacer_meta', 'odt2spip_creation'),
		array('ecrire_config','odt2spip', $defaut),
	);
	$maj['2.1.1'] = array(
		array('ecrire_config','odt2spip/defaut_attacher', 'oui'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function odt2spip_declarer_config() {
	$config =array(
		'defaut_attacher' => 'oui',
	);

	return $config;
}


/**
 * Fonction de désinstallation
 * On supprime les trois metas du plugin :
 * - saveauto : la meta de configuration
 * - saveauto_base_version : la meta du numero de version de la base
 */
function odt2spip_vider_tables($nom_meta_base_version) {
	effacer_meta('odt2spip');
	effacer_meta($nom_meta_base_version);
}
?>
