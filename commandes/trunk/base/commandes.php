<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Commandes
 * @copyright  2013
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Pipelines
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function commandes_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['commandes'] = 'commandes';
	$interface['table_des_tables']['commandes_details'] = 'commandes_details';
	
	return $interface;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function commandes_declarer_tables_objets_sql($tables) {

	// Table principale SPIP_COMMANDES
	$tables['spip_commandes'] = array(
		'type' => 'commande',
		'principale' => "oui",
		'field'=> array(
			'id_commande'        => 'bigint(21) not null',
			'reference'          => 'varchar(255) not null default ""',
			'id_auteur'          => 'bigint(21) not null default 0',
			'statut'             => 'varchar(25) not null default "encours"', // pourra être "encours", "paye", "envoye", "retour", "retour_partiel"...
			'date'               => 'datetime not null default "0000-00-00 00:00:00"',
			'date_paiement'      => 'datetime not null default "0000-00-00 00:00:00"',
			'date_envoi'         => 'datetime not null default "0000-00-00 00:00:00"',
			'maj'                => 'timestamp'
		),
		'key' => array(
			"PRIMARY KEY"        => "id_commande",
			"KEY statut"         => "statut",
			"KEY id_auteur"      => "id_auteur",              
		),
		'date'                       => "date",
		'titre'                      => "reference AS titre, '' AS lang",
		'champs_editables'           => array('id_auteur', 'date_paiement', 'date_envoi'),
		'champs_versionnes'          => array('id_auteur', 'date_paiement', 'date_envoi'),
		'rechercher_champs'          => array('reference' => 8, 'id_commande' => 8),
		'rechercher_jointures'       => array(
		                                    'auteur' => array('nom' => 1),
		                                    'commandes_detail' => array('descriptif' => 4)
		                                    ),
		'tables_jointures'           => array(),
		'statut_textes_instituer'    => array(
			'encours'            => 'commandes:statut_encours',
			'attente'            => 'commandes:statut_attente',            
			'partiel'            => 'commandes:statut_partiel',
			'paye'               => 'commandes:statut_paye',            
			'envoye'             => 'commandes:statut_envoye',
			'retour'             => 'commandes:statut_retour',
			'retour_partiel'     => 'commandes:statut_retour_partiel',
			'erreur'             => 'commandes:statut_erreur',            
		),
		'statut_images' => array(
			'encours'            => 'puce-commande-encours.png',
			'attente'            => 'puce-commande-attente.png',
			'partiel'            => 'puce-commande-partiel.png',
			'paye'               => 'puce-commande-paye.png',
			'envoye'             => 'puce-commande-envoye.png',
			'retour'             => 'puce-commande-retour.png',
			'retour_partiel'     => 'puce-commande-retour_partiel.png',
			'erreur'             => 'puce-commande-erreur.png',
		),
		'statut'=> array(
			array(
				'champ'       => 'statut',
				'publie'      => 'paye,envoye',
				'previsu'     => 'paye,envoye',
				'post_date'   => 'date', 
				'exception'   => array('statut','tout')
			)
		),
		'texte_changer_statut'        => 'commandes:texte_changer_statut_commande',
		'texte_objets'                => 'commandes:commandes_titre',
		'texte_objet'                 => 'commandes:commande_titre',
		'texte_logo_objet'            => 'commandes:titre_logo_commande',
		'texte_modifier'              => 'commandes:commande_modifier',
		'info_aucun_objet'            => 'commandes:info_aucun_commande',
		'info_1_objet'                => 'commandes:info_1_commande',
		'info_nb_objets'              => 'commandes:info_nb_commandes',

	);

	// Table auxiliaire SPIP_COMMANDES_DETAILS
	$tables['spip_commandes_details'] = array(
		'principale' => "non",
		'field'=> array(
			'id_commandes_detail' => 'bigint(21) not null',
			'id_commande'         => 'bigint(21) not null default 0',
			'descriptif'          => 'text not null default ""',
			'quantite'            => 'int not null default 0',
			'prix_unitaire_ht'    => 'float not null default 0',
			'taxe'                => 'decimal(4,3) not null default 0',
			'statut'              => 'varchar(25) not null default ""',
			'objet'               => 'varchar(25) not null default ""',
			'id_objet'            => 'bigint(21) not null default 0',
			'maj'                 => 'timestamp'
		),
		'key' => array(
			'PRIMARY KEY'         => 'id_commandes_detail',
			'KEY id_commande'     => 'id_commande'              
		),
		'join' => array(
			'id_commandes_detail' => 'id_commandes_detail',
			'id_commande'         => 'id_commande'
		)

	);

	return $tables;
}


// definir la jointure commande_auteur qui n'est pas sur spip_commandes_auteurs
// cf. inc/rechercher.php
function inc_rechercher_joints_commande_auteur_dist($table, $table_liee, $ids, $serveur) {
	if (!autoriser('voir', 'commande')) {
		return array("id_commande", "id_auteur", array());
	}
	$s = sql_select("id_commande, id_auteur", "spip_commandes", sql_in("id_auteur", $ids), '','','','',$serveur);
	return array("id_commande", "id_auteur", $s);
}



?>
