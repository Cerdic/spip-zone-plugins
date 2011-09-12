<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;



// Declaration des tables principales
function simplecal_declarer_tables_principales($tables_principales){
	
	// Table 'spip_evenements'
	$evenements = array(        "id_evenement" => "bigint(21) NOT NULL auto_increment",
        "id_secteur"   => "bigint(21) NOT NULL DEFAULT '0'",
        "id_rubrique"  => "bigint(21) NOT NULL DEFAULT '0'",
        "id_objet"     => "bigint(21) NOT NULL DEFAULT '0'",
        "type"         => "varchar(25) NOT NULL",
        "titre"        => "varchar(255) NOT NULL",
        "date_debut"   => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
        "date_fin"     => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
        "lieu"         => "varchar(255) NOT NULL",
        "descriptif"   => "text NOT NULL",
        "texte"        => "text NOT NULL",
        "date"         => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'", // création ou publication (selon statut) 
        "statut"       => "varchar(8) NOT NULL",
        "maj"          => "timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
	);
    
    // champs qui possede les cles
	$evenements_key = array(
        "PRIMARY KEY"     => "id_evenement",
        "KEY id_secteur"  => "id_secteur",
        "KEY id_rubrique" => "id_rubrique"
	);

    // champs candidats à la jointure
	$evenements_join = array(
	    "id_evenement" => "id_evenement",
        "id_secteur"   => "id_secteur",
        "id_rubrique"  => "id_rubrique"
	);
	
	// Table des tables
	$tables_principales['spip_evenements'] = array(
		'field' => &$evenements,
		'key' => &$evenements_key,
		'join' => &$evenements_join
	);	

	return $tables_principales;
}

// Declarations des tables auxiliaires (de jointures)
function simplecal_declarer_tables_auxiliaires($tables_auxiliaires){
    
	// Table de jointure auteurs_evenements
	$evenements_auteurs = array(
		"id_auteur"    => "BIGINT(21) NOT NULL",
		"id_evenement" => "BIGINT(21) NOT NULL"
	);
	$evenements_auteurs_key = array(
		"PRIMARY KEY" => "id_auteur, id_evenement"
	);

    // Table de jointure mots_evenements
	$evenements_mots = array(
		"id_mot"       => "BIGINT(21) NOT NULL",
		"id_evenement" => "BIGINT(21) NOT NULL"
	);
	$evenements_mots_key = array(
		"PRIMARY KEY" => "id_mot, id_evenement"
	);
	

	// Table des tables
    $tables_auxiliaires['spip_auteurs_evenements'] = array(
		'field' => &$evenements_auteurs,
		'key' => &$evenements_auteurs_key
	);
    
	$tables_auxiliaires['spip_mots_evenements'] = array(
		'field' => &$evenements_mots,
		'key' => &$evenements_mots_key
	);

	return $tables_auxiliaires;
}

// cf. http://programmer.spip.org/declarer_tables_interfaces,379
function simplecal_declarer_tables_interfaces($interface){

    // ----------------------------------------
	// Definir des alias pour les boucles SPIP
    // ----------------------------------------
	
    // Boucle ['evenements'] sur la table spip_evenements
	$interface['table_des_tables']['evenements'] = 'evenements';

    // ---------------------------------
	// Indiquer les jointures possibles
    // ---------------------------------
  
    // Jointures entre auteurs et evenements
	// ci-dessous : INCOMPATIBILITE si d'autres plugin font aussi ['spip_auteurs']['id_auteur'] = ...
    //$interface['tables_jointures']['spip_auteurs']['id_auteur'] = 'spip_auteurs_evenements'; // permet de faire <BOUCLEn(AUTEURS){id_evenement}>  ATTENTION : incompatible si un autre plugin fait qqch de similaire sur une autre table !!
	//$interface['tables_jointures']['spip_evenements']['id_evenement'] = 'spip_auteurs_evenements'; // permet de faire <BOUCLEn(EVENEMENTS){id_auteur=123}>
    $interface['tables_jointures']['spip_auteurs']['id_evenement'] = 'spip_auteurs_evenements';
    $interface['tables_jointures']['spip_evenements']['id_auteur'] = 'spip_auteurs_evenements';
    $interface['tables_jointures']['spip_auteurs_evenements']['id_evenement'] = 'spip_evenements';
    $interface['tables_jointures']['spip_auteurs_evenements']['id_auteur'] = 'spip_auteurs';

    // Jointures entre mots clés et evenements
    $interface['tables_jointures']['spip_mots']['id_evenement'] = 'mots_evenements'; // permet de faire <BOUCLEn(MOTS){id_evenement}>
	$interface['tables_jointures']['spip_evenements']['id_mot'] = 'mots_evenements'; // permet de faire <BOUCLEn(EVENEMENTS){id_mot=123}>
    $interface['tables_jointures']['spip_mots_evenements']['id_evenement'] = 'evenements';
    $interface['tables_jointures']['spip_mots_evenements']['id_mot'] = 'mots';
    
    
    

    // ------------------------------
	// Titre pour URL propres
    // ------------------------------
    $interface['table_titre']['evenements'] = "titre, '' AS lang";

    // -------------------------------------------
	// Traitements par défaut sur certains champs
    // -------------------------------------------
    $interface['table_des_traitements']['DATE_DEBUT'][] = 'normaliser_date(%s)';
    $interface['table_des_traitements']['DATE_FIN'][] = 'normaliser_date(%s)';
    
   
    // ---------------------------------------------------------------------------
	// Champs de type 'date' pour la gestion des critères age, age_relatif, etc.
    // ---------------------------------------------------------------------------
    // Note : provoque l'enregistrement de la date de publication (lors de sa modif) dans date
    $interface['table_date']['evenements'] = 'date'; 
    // -------
	return $interface;
}

function simplecal_declarer_tables_objets_surnoms($surnoms) {
	// Le type 'evenement' correspond a la table nommee 'evenements'
	$surnoms['evenement'] = 'evenements';
	return $surnoms;
}


?>
