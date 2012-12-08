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

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function saveauto_declarer_config() {
	include_spip('base/dump');

	// On determine la liste des tables exportées par défaut.
	$exclude = lister_tables_noexport();
	$tables = base_lister_toutes_tables('', array(), $exclude);

	$config =array(
		'prefixe_save'			=> 'sav',
		'max_zip'				=> 75,
		'sauvegarde_reguliere'	=> 'non',
		'frequence_maj'			=> 1,
		'structure'				=> 'true',
		'donnees'				=> 'true',
		'ecrire_succes'			=> 'true',
		'nettoyage_journalier'	=> 'oui',
		'jours_obso'			=> 15,
		'notif_active'			=> 'non',
		'notif_mail'			=> '',
		'mail_max_size'			=> 2,
		'tout_saveauto'			=> 'oui',
		'tables_saveauto'		=> $tables,
	);

	return $config;
}


/**
 * Fonction de désinstallation
 * On supprime les trois metas du plugin :
 * - saveauto : la meta de configuration
 * - saveauto_creation : la meta de la date de dernière création d'archive
 * - saveauto_base_version : la meta du numero de version de la base
 */
function saveauto_vider_tables($nom_meta_base_version) {
	effacer_meta('saveauto');
	effacer_meta($nom_meta_base_version);
}
?>