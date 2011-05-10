<?php
// Pipelines.
// Objectifs : 
//	- Declarer et ajouter des tables dans la base de donnees
// Voir la doc suivante : http://doc.spip.org/@Ajouter-des-tables-et-des-boucles
//
// Attention, il est imperatif de distinguer :
//	- ce qu'on appelle une *table* : son id ou son nom complet (ex: spip_vu_annonces)
//	- ce qu'on appelle le *nom* d'une table : son diminutif (ex: vu_annonces)
//	- ce qu'on appelle un *objet* : nom de la table sans suffixe et au singuler (ex: annonce)


if (!defined("_ECRIRE_INC_VERSION")) return;

// ---------- 1/3 - Declaration des tables principales
function vu_declarer_tables_principales($tables_principales){
	
	// Table 'spip_vu_annonces'
	$annonces = array(		// quels sont les differents champs de la table        	"id_annonce" => "bigint(21) NOT NULL auto_increment",
        	"titre" => "text NOT NULL",
        	"lien" => "text NOT NULL",
		"annonceur" => "text NOT NULL",
        	"peremption" => "date NOT NULL",
        	"type" => "text NOT NULL",
		"descriptif" => "text NOT NULL",
        	"source_nom" => "text NOT NULL",
        	"source_lien" => "text NOT NULL",
        	"date" => "datetime NOT NULL",
        	"statut" => "varchar(10) NOT NULL",
	);
	$annonces_key = array(		// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_annonce",
		"KEY peremption" => "peremption",
	);

	$annonces_join = array(		// quels champs sont candidats à la jointure
	        "id_annonce" => "id_annonce"
	);

	// Table 'spip_vu_evenements'
	$evenements = array(		// quels sont les differents champs de la tables
        	"id_evenement" => "bigint(21) NOT NULL auto_increment",
        	"titre" => "text NOT NULL",
        	"lien" => "text NOT NULL",
        	"date_evenement" => "date NOT NULL",
		"lieu_evenement" => "text NOT NULL",
		"organisateur" => "text NOT NULL",
		"type" => "text NOT NULL",
		"descriptif" => "text NOT NULL",
        	"source_nom" => "text NOT NULL",
        	"source_lien" => "text NOT NULL",
        	"date" => "datetime NOT NULL",
        	"statut" => "varchar(10) NOT NULL",
	);

	$evenements_key = array(	// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_evenement"
	);

	$evenements_join = array(	// quels champs sont candidats à la jointure
	        "id_evenement" => "id_evenement"
	);

	// Table 'spip_vu_publications'
	$publications = array(		// quels sont les differents champs de la tables
        	"id_publication" => "bigint(21) NOT NULL auto_increment",
        	"titre" => "text NOT NULL",
        	"lien" => "text NOT NULL",
		"auteur" => "text NOT NULL",
		"editeur" => "text NOT NULL",
		"date_publication" => "date NOT NULL",
		"type" => "text NOT NULL",
		"descriptif" => "text NOT NULL",
        	"langpub" => "text NOT NULL",
        	"source_nom" => "text NOT NULL",
		"source_lien" => "text NOT NULL",
		"langue" => "text NOT NULL",
        	"date" => "datetime NOT NULL",
        	"statut" => "varchar(10) NOT NULL",
	);

	$publications_key = array(	// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_publication"
	);

	$publications_join = array(	// quels champs sont candidats à la jointure
	        "id_publication" => "id_publication"
	);

	// Table des tables

	$tables_principales['spip_vu_annonces'] = array(
		'field' => &$annonces,
		'key' => &$annonces_key,
		'join' => &$annonces_join
	);
	
	$tables_principales['spip_vu_evenements'] = array(
		'field' => &$evenements,
		'key' => &$evenements_key,
		'join' => &$evenements_join
	);
	$tables_principales['spip_vu_publications'] = array(
		'field' => &$publications,
		'key' => &$publications_key,
		'join' => &$publications_join
	);

	return $tables_principales;
}

// ---------- 2/3 - Declarations des tables auxiliaires (de jointures)
function vu_declarer_tables_auxiliaires($tables_auxiliaires){
	
	// Table de jointure mots_vu_annonces
        $annonces_mots = array(
                "id_mot" => "BIGINT(21) NOT NULL",
                "id_annonce" => "BIGINT(21) NOT NULL"
        );
        $annonces_mots_key = array(
                "PRIMARY KEY" => "id_mot, id_annonce"
        );

	// Table de jointure mots_vu_evenements
        $evenements_mots = array(
                "id_mot" => "BIGINT(21) NOT NULL",
                "id_evenement" => "BIGINT(21) NOT NULL"
        );
        $evenements_mots_key = array(
                "PRIMARY KEY" => "id_mot, id_evenement",
        );

	// Table de jointure mots_vu_publications
        $publications_mots = array(
                "id_mot" => "BIGINT(21) NOT NULL",
                "id_publication" => "BIGINT(21) NOT NULL"
        );
        $publications_mots_key = array(
                "PRIMARY KEY" => "id_mot, id_publication",
        );

	// Table des tables        
        $tables_auxiliaires['spip_mots_vu_annonces'] = array(
                'field' => &$annonces_mots,
                'key' => &$annonces_mots_key
        );
        $tables_auxiliaires['spip_mots_vu_evenements'] = array(
                'field' => &$evenements_mots,
                'key' => &$evenements_mots_key
        );
        $tables_auxiliaires['spip_mots_vu_publications'] = array(
                'field' => &$publications_mots,
                'key' => &$publications_mots_key
        );
     
        return $tables_auxiliaires;
}

// ---------- 3/3 - Declatation des interfaces, modalites d'utilisation des tables

function vu_declarer_tables_interfaces($interface){

	// Definir le nom des nouvelles boucles
		// La table ['vu_*'] aura en fait pour nom 'vu_*'
		$interface['table_des_tables']['vu_annonces']= 'vu_annonces';
		$interface['table_des_tables']['vu_evenements'] = 'vu_evenements';
		$interface['table_des_tables']['vu_publications'] = 'vu_publications';

	// Indiquer les jointures possibles
		// de tables vu_* vers mots_vu_* : sur le champ id_*
		$interface['tables_jointures']['spip_vu_annonces']['id_annonce'] = 'mots_vu_annonces';
        	$interface['tables_jointures']['spip_vu_evenements']['id_evenement'] = 'mots_vu_evenements';
        	$interface['tables_jointures']['spip_vu_publications']['id_publication'] = 'mots_vu_publications';

		// de tables mots_vu_* vers vu_* : sur le champ id_*
	        $interface['tables_jointures']['spip_mots_vu_annonces']['id_annonce'] = 'vu_annonces';
	        $interface['tables_jointures']['spip_mots_vu_evenements']['id_evenement'] = 'vu_evenements';
	        $interface['tables_jointures']['spip_mots_vu_publications']['id_publication'] = 'vu_publications';

		// de spip_mots vers mots_vu_* : sur le champ id_mot
	        $interface['tables_jointures']['spip_mots']['id_mot'] = 'spip_mots_vu_annonces';
	        $interface['tables_jointures']['spip_mots']['id_mot'] = 'spip_mots_vu_evenements';
	        $interface['tables_jointures']['spip_mots']['id_mot'] = 'spip_mots_vu_publications';

		// de vu_*_mots vers spip_mots : sur le champ id_mot
		$interface['tables_jointures']['spip_mots_vu_annonces']['id_mot'] = 'spip_mots';
		$interface['tables_jointures']['spip_mots_vu_evenements']['id_mot'] = 'spip_mots';
		$interface['tables_jointures']['spip_mots_vu_publications']['id_mot'] = 'spip_mots';

        // Titre pour url
        $interface['table_titre']['spip_vu_annonces'] = "titre, '' AS lang";
        $interface['table_titre']['spip_vu_evenements'] = "titre, '' AS lang";
        $interface['table_titre']['spip_vu_publications'] = "titre, '' AS lang";

	// On definit les traitements par défaut sur les champs qui le necessitent
	//$interface['table_des_traitements']['MON_CHAMPS'][]= 'ma_fonction(%s)';
	$interfaces['PEREMPTION'][]= 'normaliser_date(%s)';
	$interfaces['DATE_EVENEMENT'][]= 'normaliser_date(%s)';
	$interfaces['DATE_PUBLICATION'][]= 'normaliser_date(%s)';

	// On indique les champs 'date' pour accelerer les requetes SQL
	// Autorise l'utilisation des criteres 'age' et 'age_relatif' sur les nouveaux objets
		$interface['table_date']['vu_annonces'] = 'peremption';
		$interface['table_date']['vu_annonces'] = 'date';

		$interface['table_date']['vu_evenements'] = 'date_evenement';
		$interface['table_date']['vu_evenements'] = 'date';

		$interface['table_date']['vu_publications'] = 'date_publication';
		$interface['table_date']['vu_publications'] = 'date';

       return $interface;

}

function vu_declarer_tables_objets_surnoms($surnoms) {
	// Le type ['*'] correspond a la table nommee ['vu_*s'] (!= id de la table)
	$surnoms['annonce'] = 'vu_annonces';
	$surnoms['evenement'] = 'vu_evenements';
	$surnoms['publication'] = 'vu_publications';
	return $surnoms;
}


?>
