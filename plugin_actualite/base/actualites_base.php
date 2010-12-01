<?php
// Pipelines.
// Objectifs : 
//	- Declarer et ajouter des tables dans la base de donnees
// Voir la doc suivante : http://doc.spip.org/@Ajouter-des-tables-et-des-boucles
//
// Attention, il est imperatif de distinguer :
//	- ce qu'on appelle une *table* : son id ou son nom complet (ex: spip_actualites)
//	- ce qu'on appelle le *nom* d'une table : son diminutif (ex: actualites)
//	- ce qu'on appelle un *objet* : nom de la table sans suffixe et au singuler (ex: actualite)


if (!defined("_ECRIRE_INC_VERSION")) return;

// ---------- 1/3 - Declaration des tables principales
function actualites_declarer_tables_principales($tables_principales){
	
	// Table 'spip_actualites'
	$actualites = array(		// quels sont les differents champs de la table
        	"id_actualite"	=> "bigint(21) NOT NULL auto_increment",
        	"titre"			=> "text NOT NULL",
        	"maj"			=> "TIMESTAMP",
        	"date"			=> "datetime NOT NULL",
        	"statut"		=> "varchar(10) NOT NULL",
	);
	$actualites_key = array(		// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_actualite"
	);

	$actualites_join = array(		// quels champs sont candidats à la jointure
	        "id_actualite" => "id_actualite"
	);


	// Table des tables
	$tables_principales['spip_actualites'] = array(
		'field' => &$actualites,
		'key' => &$actualites_key,
		'join' => &$actualites_join
	);

	return $tables_principales;
}

// ---------- 2/3 - Declarations des tables auxiliaires (de jointures)
function actualites_declarer_tables_auxiliaires($tables_auxiliaires){
	
	// Table de jointure mots_actualites
	$actualites_mots = array(
			"id_mot" => "BIGINT(21) NOT NULL",
			"id_actualite" => "BIGINT(21) NOT NULL"
	);
	$actualites_mots_key = array(
			"PRIMARY KEY" => "id_mot, id_actualite"
	);
	

	$actualites_liens = array(
			"id_actualite"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
			"vu"	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL"); //le vu je ne sais pas a quoi il sert ... 
	
	$actualites_liens_key = array(
			"PRIMARY KEY"		=> "id_actualite,id_objet,objet",
			"KEY id_actualite"	=> "id_actualite"
	);


	// Table des tables        
	$tables_auxiliaires['spip_mots_actualites'] = array(
			'field' => &$actualites_mots,
			'key' => &$actualites_mots_key
	);

	$tables_auxiliaires['spip_actualites_liens'] = array(
	'field' => $actualites_liens,
	'key' => $actualites_liens_key);


	

	return $tables_auxiliaires;
}

// ---------- 3/3 - Declatation des interfaces, modalites d'utilisation des tables
function actualites_declarer_tables_interfaces($interface){

	// Definir le nom des nouvelles boucles
		// La table ['actualites_*'] aura en fait pour nom 'actualites_*'
		$interface['table_des_tables']['actualites']= 'actualites';

	// Indiquer les jointures possibles
		// de tables actualites_* vers mots_actualites : sur le champ id_*
		$interface['tables_jointures']['spip_actualites']['id_actualite'] = 'mots_actualites';
 
		// de tables mots_actualites vers actualites : sur le champ id_*
	    $interface['tables_jointures']['spip_mots_actualites']['id_actualite'] = 'actualites';

		// de spip_mots vers mots_actualites : sur le champ id_mot
        $interface['tables_jointures']['spip_mots']['id_mot'] = 'spip_mots_actualites';

		// de mots_actualites vers spip_mots : sur le champ id_mot
		$interface['tables_jointures']['spip_mots_actualites']['id_mot'] = 'spip_mots';
		
		//permet de faire les jointures et donc permettre les criteres {id_article} et {id_rubrique} dans les boucles
			//si on le lie aux articles
			$interface['tables_jointures']['spip_articles'][]= 'spip_actualites_liens';
			//si on le lie aux rubriques
			$interface['tables_jointures']['spip_rubriques'][]= 'spip_actualites_liens';
			
			$interface['tables_jointures']['spip_actualites'][]= 'spip_actualites_liens';
			
        // Titre pour url
        $interface['table_titre']['actualites'] = "titre, '' AS lang";

	// On indique les champs 'date' pour accelerer les requetes SQL
	// Autorise l'utilisation des criteres 'age' et 'age_relatif' sur les nouveaux objets
	$interface['table_date']['actualites'] = 'date';

	return $interface;

}

function actualites_declarer_tables_objets_surnoms($surnoms) {
	// Le type ['actualite'] correspond a la table nommee ['actualites'] (!= id de la table)
	$surnoms['actualite'] = 'actualites';
	return $surnoms;
}


?>
