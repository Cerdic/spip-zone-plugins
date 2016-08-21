<?php
/**
 * Fichier gérant l'installation et la désinstallation du plugin Code de Langues
 *
 * @package    SPIP\ISOCODE\ADMINISTRATION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin.
 * Le schéma du plugin est composé des tables `spip_iso639xxxx` et d'une configuration.
 *
 * @param string $nom_meta_base_version
 *        Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *        Version du schéma de données (déclaré dans paquet.xml)
 *
 * @return void
 **/
function isocode_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

//	$config_defaut = configurer_isocode();

	// Liste des tables créées par le plugin
	include_spip('isocode_fonctions');
	$tables = array();
	foreach (isocode_lister_tables() as $_table) {
		$tables[] = "spip_${_table}";
	}

	$maj['create'] = array(
		array(
			'maj_tables',
			$tables
		),
		//		array('ecrire_config', 'isocode', $config_defaut)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

	// Ajout systématique des données iso dans la base de données, quelque soit l'action en cours.
	// Ces données n'étant pas modifiables, il n'y a pas de risque à recharger ces tables.
//	isocode_charger_tables();
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *        Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP.
 *
 * @return void
 **/
function isocode_vider_tables($nom_meta_base_version) {

	// Liste des tables créées par le plugin
	include_spip('isocode_fonctions');
	$tables = isocode_lister_tables();

	// Supprimer les tables ISO créées par le plugin
	foreach ($tables as $_table) {
		sql_drop_table("spip_${_table}");
	}

	// Effacer la meta de configuration et de stockage du plugin
	effacer_meta('isocode');

	// Effacer la meta du schéma de la base
	effacer_meta($nom_meta_base_version);
}

/**
 * Initialise la configuration du plugin.
 *
 * @return array
 *        Le tableau de la configuration par défaut qui servira à initialiser la meta `isocode`.
 */
function configurer_isocode() {
	$config = array();

	return $config;
}
