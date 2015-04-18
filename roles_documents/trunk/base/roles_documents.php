<?php
/**
 * Plugin Rôles de documents
 * (c) 2015
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclarer la liste des rôles
 *
 * @param array $tables
 * 		Description des tables
 * @return array
 * 		Description complétée des tables
 */
function roles_documents_declarer_tables_objets_sql($tables){

	// la liste des roles peut être complétée via un pipeline
	$roles_documents = pipeline('lister_roles_documents', array(
		'logo'  => 'roles_documents:role_logo',
		'logo_survol'  => 'roles_documents:role_logo_survol',
	));
	// pour l'instant tous les rôles sont affichés pour tous les cas
	$choix = is_array($roles_documents) ? array_keys($roles_documents) : array();

	array_set_merge($tables, 'spip_documents', array(
		"roles_colonne" => "role",
		"roles_titres" => $roles_documents,
		"roles_objets" => array(
			'*' => array(
				'choix' => $choix,
				'defaut' => ''
			)
		)
	));

	return $tables;
}

/**
 * Ajouter la colonne de rôle
 *
 * @param array $tables
 * 		Description des tables auxiliaires
 * @return array
 * 		Description complétée
**/
function roles_documents_declarer_tables_auxiliaires($tables) {
	$tables['spip_documents_liens']['field']['role']        = "varchar(30) NOT NULL DEFAULT ''";
	$tables['spip_documents_liens']['key']['PRIMARY KEY']   = "id_document,id_objet,objet,role";
	return $tables;
}
?>
