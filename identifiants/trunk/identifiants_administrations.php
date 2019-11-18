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

include_spip('inc/identifiants');
include_spip('identifiants_fonctions');

/**
 * Fonction d'installation et de mise à jour du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function identifiants_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array();

	// 1.0.1 : ajout d'une colonne `id_identifiant`, modification de la clé primaire
	$maj['1.0.1'] = array(
		// supprimer la clé primaire actuelle `identifiant`
		array('sql_alter', 'TABLE spip_identifiants DROP PRIMARY KEY'),
		// ajout de la nouvelle colonne `id_identifiant`
		array('maj_tables', array('spip_identifiants')),
		// nouvelle clé primaire
		array('sql_alter', 'TABLE spip_identifiants ADD PRIMARY KEY (id_identifiant,identifiant,objet,id_objet)')
	);

	// 1.0.2 : suppression de la colonne `id_identifiant`, modification de la clé primaire (facepalm)
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

	// 2.0.0 : refactoring
	$maj['2.0.0'] = array(
		// Répertorier les tables qui ont nativement une colonne `identifiant`
		array('identifiants_repertorier_tables_natives'),
		// Ajoute la colonne `identifiant` sur les objets configurés
		array('identifiants_adapter_tables'),
		// Migrer les identifiants de l'ancienne table
		array('identifiants_migrer_anciens_identifiants') ,
		// Supprimer l'ancienne table
		array('sql_drop_table', 'spip_identifiants'),
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
 */
function identifiants_vider_tables($nom_meta_base_version) {
	identifiants_nettoyer_tables();
	effacer_meta('identifiants');
	effacer_meta($nom_meta_base_version);
}

/**
 * Maj 2.0.0 : migrer les identifiants présents dans l'ancienne table
 *
 * @return void
 */
function identifiants_maj_200() {

	include_spip('inc/config');
	include_spip('base/abstract_sql');

	// Répertorier les tables qui ont nativement une colonne `identifiant`
	identifiants_repertorier_tables_natives();
	// Ajoute la colonne `identifiant` sur les objets configurés
	identifiants_adapter_tables();
	// Migrer les identifiants de l'ancienne table
	identifiants_migrer_anciens_identifiants();
	// Supprimer l'ancienne table
	sql_query('DROP TABLE spip_identifiants');
}



/**
 * Maj 2.0.0 : migrer les identifiants présents dans l'ancienne table
 *
 * @return void
 */
function identifiants_migrer_anciens_identifiants() {
	$echec = array();
	if ($identifiants = sql_allfetsel('identifiant,objet,id_objet', 'spip_identifiants')) {
		include_spip('base/abstract_sql');
		include_spip('base/objets');
		foreach ($identifiants as $res_identifiant) {
			$objet       = $res_identifiant['objet'];
			$id_objet    = $res_identifiant['id_objet'];
			$identifiant = $res_identifiant['identifiant'];
			$table       = table_objet_sql($objet);
			$cle         = id_table_objet($objet);
			$set         = array('identifiant' => $identifiant);
			$where       = $cle.'='.intval($id_objet);
			if (!sql_updateq($table, $set, $where)) {
				$echec[] = "$identifiant($objet-$id_objet)";
			}
		}
	}
	if ($echec) {
		spip_log('Mise à jour v1 à v2 : échec de migration des identifiants suivants : ' . join(', ', $echec), 'identifiants'._LOG_ERREUR);
	}
}
