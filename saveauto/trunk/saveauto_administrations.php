<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'installation du plugin
 */
function saveauto_upgrade($nom_meta_base_version,$version_cible) {
	$maj = array();

	// Déclaration des valeurs par défaut de chaque variable de config
	$defaut = saveauto_declarer_config();

	// On considère que la configuration existante n'est plus utile étant donnés les changements
	// donc on se contente de la supprimer tout simplement (permet d'éviter un souci si le plugin
	// n'a pas été désinstallé comme précisé dans la documentation)
	$maj['create'] = array(
		array('effacer_meta', 'saveauto'),
		array('effacer_meta', 'saveauto_creation'),
		array('ecrire_config','saveauto', $defaut),
	);
	$maj['0.1.1'] = array(
		array('ecrire_config','saveauto/repertoire_save', _DIR_DUMP),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function saveauto_declarer_config() {
	include_spip('base/dump');

	// On determine la liste des tables exportées par défaut.
	$exclude = lister_tables_noexport();
	$tables = base_lister_toutes_tables('', array(), $exclude, true);

	$config =array(
		'prefixe_save'			=> 'sav',
		'max_zip'				=> 75,
		'sauvegarde_reguliere'	=> 'non',
		'frequence_maj'			=> 1,
		'structure'				=> 'true',
		'donnees'				=> 'true',
		'nettoyage_journalier'	=> 'oui',
		'jours_obso'			=> 15,
		'notif_active'			=> 'non',
		'notif_mail'			=> '',
		'mail_max_size'			=> 5,
		'tout_saveauto'			=> 'oui',
		'tables_saveauto'		=> $tables,
		'repertoire_save'		=> _DIR_DUMP,
	);

	return $config;
}


/**
 * Fonction de désinstallation
 * On supprime les trois metas du plugin :
 * - saveauto : la meta de configuration
 * - saveauto_base_version : la meta du numero de version de la base
 */
function saveauto_vider_tables($nom_meta_base_version) {
	effacer_meta('saveauto');
	effacer_meta($nom_meta_base_version);
}
?>
