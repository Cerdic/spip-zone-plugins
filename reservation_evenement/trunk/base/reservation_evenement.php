<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */

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
function reservation_evenement_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['reservations'] = 'reservations';
	$interfaces['table_des_tables']['reservations_details'] = 'reservations_details';    

	return $interfaces;
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
function reservation_evenement_declarer_tables_objets_sql($tables) {

	$tables['spip_reservations'] = array(
		'type' => 'reservation',
		'principale' => "oui",
		'field'=> array(
			"id_reservation"     => "bigint(21) NOT NULL",
			"id_auteur"          => "bigint(21) NOT NULL DEFAULT '0'",
            "reference"          => "varchar(255) NOT NULL DEFAULT ''",			
			"date_paiement"      => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"type_paiement"      => "varchar(50) NOT NULL DEFAULT ''",
			"nom"                => "varchar(255) NOT NULL DEFAULT ''",
			"email"              => "varchar(255) NOT NULL DEFAULT ''",
			"maj"                => "timestamp",
			"donnees_auteur"     => "text NOT NULL DEFAULT ''",
			"date"               => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", 
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_reservation",
			"KEY statut"         => "statut,id_auteur", 
		),
		'titre' => "reference AS titre, '' AS lang",
		'date' => "date",
		'champs_editables'  => array('id_auteur', 'date_paiement', 'nom', 'email', 'donnees_auteur', 'reference'),
		'champs_versionnes' => array('id_auteur', 'date_paiement', 'nom', 'email', 'donnees_auteur', 'reference'),
		'rechercher_champs' => array("reference" => 8,"id_reservation"=>8),
		'tables_jointures'  => array('id_reservation','id_auteur'),
        'statut_textes_instituer' => array(
            'attente'    => 'reservation:texte_statut_attente',
            'attente_paiement'    => 'reservation:texte_statut_attente_paiement',            
            'accepte'     => 'reservation:texte_statut_accepte',
            'encours'     => 'reservation:texte_statut_encours',            
            'refuse'   => 'reservation:texte_statut_refuse',
            'poubelle' => 'reservation:texte_statut_poubelle',
        ),
        'statut_images' => array(
            'attente'          => 'puce-reservation-attente-16.png', 
            'attente_paiement'          => 'puce-reservation-attente_paiement-16.png',             
            'accepte'          => 'puce-reservation-accepte-16.png',
            'encours'             => 'puce-reservation-encours-16.png',            
            'refuse'             => 'puce-reservation-refuse-16.png',
            'poubelle'           => 'puce-reservation-poubelle-16.png',
        ),
        'statut'=> array(
            array(
                'champ'     => 'statut',
                'publie'    => 'accepte',
                'previsu'   => 'accepte,attente,attente_paiement',
                'post_date' => 'date', 
                'exception' => array('statut','tout')
            )
        ),
		'texte_changer_statut' => 'reservation:texte_changer_statut_reservation', 
		

	);
    


    $tables['spip_reservations_details'] = array(
        'type' => 'reservations_detail',
        'principale' => "oui", 
        'table_objet_surnoms' => array('reservationsdetail'), // table_objet('reservations_detail') => 'reservations_details' 
        'field'=> array(
            "id_reservations_detail" => "bigint(21) NOT NULL",
            "id_reservation"     => "bigint(21) NOT NULL DEFAULT '0'",
            "id_evenement"       => "bigint(21) NOT NULL DEFAULT '0'",
            "descriptif"         => "text NOT NULL",
            "quantite"           => "int(11) NOT NULL DEFAULT '0'",
            "prix_ht"   => "float NOT NULL DEFAULT '0'",
            "prix"   => "float NOT NULL DEFAULT '0'",            
            "taxe"               => "decimal(4,3) NOT NULL DEFAULT '0.000'",
            "statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
            "maj"                => "TIMESTAMP"
        ),
        'key' => array(
            "PRIMARY KEY"        => "id_reservations_detail",
            "KEY statut"         => "statut,id_reservation,id_evenement",           
            
        ),
        'titre' => "descriptif AS titre, '' AS lang",
         #'date' => "",
        'champs_editables'  => array('id_reservation','id_evenement','descriptif','quantite','prix_ht','prix','taxe'),
        'champs_versionnes' => array(),
        'rechercher_champs' => array(),
        'tables_jointures'  => array('id_evenement','id_reservation'),
        'statut_textes_instituer' => array(
            'attente'    => 'reservation:texte_statut_attente',
            'attente_paiement'    => 'reservation:texte_statut_attente_paiement',            
            'accepte'     => 'reservation:texte_statut_accepte',       
            'refuse'   => 'reservation:texte_statut_refuse',
            'poubelle' => 'reservation:texte_statut_poubelle',
        ),
        'statut_images' => array(
            'attente'          => 'puce-reservation-attente-16.png', 
            'attente_paiement'          => 'puce-reservation-attente_paiement-16.png',             
            'accepte'          => 'puce-reservation-accepte-16.png',         
            'refuse'             => 'puce-reservation-refuse-16.png',
            'poubelle'           => 'puce-reservation-poubelle-16.png',
        ),
        'statut'=> array(
            array(
                'champ'     => 'statut',
                'publie'    => 'accepte',
                'previsu'   => 'accepte,attente,attente_paiement',
                'post_date' => 'date', 
                'exception' => array('statut','tout')
            )
        ),
        'texte_changer_statut' => 'reservations:texte_changer_statut_reservations_detail', 
        

    );
   if(test_plugin_actif('shop_prix')) {
       $tables['spip_reservations_details']=array_merge($tables['spip_reservations_details'],array(
        'field'=>array('id_prix_objet'=>"bigint(21) NOT NULL DEFAULT '0'"),
        'champs_editables'  => array(
            'id_reservation',
            'id_evenement',
            'descriptif',
            'quantite',
            'prix_ht',
            'prix',
            'taxe',
            'id_prix_objet'),
        ));
    }
	return $tables;
}
?>