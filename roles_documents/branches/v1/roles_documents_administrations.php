<?php
/**
 * Plugin Rôles de documents
 * (c) 2015
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function roles_documents_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Ajout des rôles à la table de liens des documents
	$maj['create'] = array(
		// supprimer la clé primaire actuelle pour pouvoir en changer en ajoutant la colonne rôle
		array('sql_alter', 'TABLE spip_documents_liens DROP PRIMARY KEY'),
		// ajout de la colonne role
		array('maj_tables', array('spip_documents_liens')),
		// la nouvelle colonne est la, mettre sa nouvelle clé primaire
		array('sql_alter', 'TABLE spip_documents_liens ADD PRIMARY KEY (id_document,id_objet,objet,role)'),
		// Mettre un rôle 'document' par défaut aux liens dépourvus de rôle
		array('sql_update', 'spip_documents_liens', array('role' => sql_quote('document')), 'role=' . sql_quote('')),
	);

	// Mettre un rôle 'document' par défaut aux liens dépourvus de rôle
	$maj['1.0.1'] = array(
		array('sql_update', 'spip_documents_liens', array('role' => sql_quote('document')), 'role=' . sql_quote('')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function roles_documents_vider_tables($nom_meta_base_version) {

	// tant qu'il existe des doublons, on supprime une ligne doublonnée
	// sinon on ne pourra pas modifier la cle primaire ensuite
	// cet algo est certainement a optimiser
	while ($doublons = sql_allfetsel(
		array('id_document', 'id_objet', 'objet', 'role'),
		array('spip_documents_liens'),
		'',
		'id_document,id_objet,objet',
		'',
		'',
		'COUNT(*) > 1'
	)) {
		foreach ($doublons as $d) {
			$where = array();
			foreach ($d as $cle => $valeur) {
				$where[] = "$cle=".sql_quote($valeur);
			}
			sql_delete('spip_documents_liens', $where);
		}
	}

	// supprimer la clé primaire, la colonne rôle, et remettre l'ancienne clé primaire
	sql_alter('TABLE spip_documents_liens DROP PRIMARY KEY');
	sql_alter('TABLE spip_documents_liens DROP COLUMN role');
	sql_alter('TABLE spip_documents_liens ADD PRIMARY KEY (id_document,id_objet,objet)');

	effacer_meta($nom_meta_base_version);
}
