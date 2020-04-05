<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function identifiants_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_identifiants')),
	);

	// ajout d'une colonne `id_identifiant`, modification de la clé primaire
	$maj['1.0.1'] = array(
		// supprimer la clé primaire actuelle `identifiant`
		array('sql_alter', 'TABLE spip_identifiants DROP PRIMARY KEY'),
		// ajout de la nouvelle colonne `id_identifiant`
		array('maj_tables', array('spip_identifiants')),
		// nouvelle clé primaire
		array('sql_alter', 'TABLE spip_identifiants ADD PRIMARY KEY (id_identifiant,identifiant,objet,id_objet)')
	);

	// suppression de la colonne `id_identifiant`, modification de la clé primaire
	$maj['1.0.2'] = array(
		// supprimer l'auto increment de id_identifiant
		array('sql_alter', 'TABLE spip_identifiants CHANGE id_identifiant id_identifiant int'),
		// supprimer les clés primaires
		array('sql_alter', 'TABLE spip_identifiants DROP PRIMARY KEY'),
		// supprimer la colonne `id_identifiant`
		array('sql_alter', 'TABLE spip_identifiants DROP COLUMN id_identifiant'),
		// nouvelles clés primaires
		array('sql_alter', 'TABLE spip_identifiants ADD PRIMARY KEY (identifiant,objet,id_objet)')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function identifiants_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_identifiants');

	effacer_meta($nom_meta_base_version);
}
