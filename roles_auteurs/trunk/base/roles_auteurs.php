<?php
/**
 * Plugin Rôles d'auteurs
 * (c) 2012 Marcillaud Matthieu
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
function roles_auteurs_declarer_tables_objets_sql($tables){

	array_set_merge($tables, 'spip_auteurs', array(
		"roles_colonne" => "role",
		"roles_titres" => array(
			'redacteur'  => 'info_statut_redacteur',
			'traducteur' => 'roles_auteurs:traducteur',
			'correcteur' => 'roles_auteurs:correcteur',
			'relecteur'  => 'roles_auteurs:relecteur',
		),
		"roles_objets" => array(
			'articles' => array(
				'choix' => array('redacteur', 'traducteur', 'correcteur', 'relecteur'),
				'defaut' => 'redacteur'
			)
			#'*' => array()
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
function roles_auteurs_declarer_tables_auxiliaires($tables) {
	$tables['spip_auteurs_liens']['field']['role']        = "varchar(30) NOT NULL DEFAULT ''";
	$tables['spip_auteurs_liens']['key']['PRIMARY KEY']   = "id_auteur,id_objet,objet,role";
	return $tables;
}
?>
