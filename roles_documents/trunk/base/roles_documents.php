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

	// par défaut 2 rôles 'logo' et 'logo de survol'
	$roles_documents = array(
		'logo'  => 'roles_documents:role_logo',
		'logo_survol'  => 'roles_documents:role_logo_survol',
	);
	// ces 2 rôles sont affichés dans tous les cas
	$choix = array_keys($roles_documents);

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
