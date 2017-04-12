<?php
// Pipelines.
// Objectifs : 
//	- Declarer et ajouter des tables dans la base de donnees
// Voir la doc suivante : http://code.spip.net/@Ajouter-des-tables-et-des-boucles

if (!defined("_ECRIRE_INC_VERSION")) return;

global $tables_principales;
global $tables_auxilliaires;
global $interface;



// ---------- 1/3 - Declaration des tables principales
function vu_declarer_tables_principales($tables_principales){
	
	// Table 'vu_annonces'
	$annonces = array(		// quels sont les differents champs de la table        	"id_annonce" => "bigint(21) NOT NULL auto_increment",
        	"type" => "text NOT NULL",
        	"titre" => "text NOT NULL",
        	"lien" => "text NOT NULL",
        	"date_peremption" => "date NOT NULL",
        	"nom_source" => "text NOT NULL",
        	"lien_source" => "text NOT NULL",
		"date_vue" => "date NOT NULL",
        	"date_redac" => "datetime NOT NULL",
        	"statut" => "varchar(10) NOT NULL",
	);
	$annonces_key = array(		// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_annonce",
	);

	$annonces_join = array(		// quels champs sont candidats à la jointure
	        "id_annonce" => "id_annonce"
	);

	// Table 'vu_evenements'
	$evenements = array(		// quels sont les differents champs de la tables
        	"id_evenement" => "bigint(20) NOT NULL auto_increment",
        	"type" => "text NOT NULL",
        	"titre" => "text NOT NULL",
        	"lien_evenement" => "text NOT NULL",
        	"date_evenement" => "date NOT NULL",
		"lieu" => "text NOT NULL",
        	"nom_source" => "text NOT NULL",
        	"lien_source" => "text NOT NULL",
		"date_vue" => "datetime NOT NULL",
        	"date_redac" => "datetime NOT NULL",
        	"statut" => "varchar(10) NOT NULL"
	);

	$evenements_key = array(	// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_evenement"
	);

	$evenements_join = array(	// quels champs sont candidats à la jointure
	        "id_evenement" => "id_evenement"
	);

	// Table 'vu_publications'
	$publications = array(		// quels sont les differents champs de la tables
        	"id_publication" => "bigint(20) NOT NULL auto_increment",
        	"titre" => "text NOT NULL",
		"auteur" => "text NOT NULL",
        	"lien_publication" => "text NOT NULL",
        	"nom_source" => "text NOT NULL",
		"lien_source" => "text NOT NULL",
        	"date_vue" => "date NOT NULL",
        	"date_redac" => "datetime NOT NULL",
        	"statut" => "varchar(10) NOT NULL",
	);

	$publications_key = array(	// quels sont les champs qui possede les cles
        	"PRIMARY KEY" => "id_publication"
	);

	$publications_join = array(	// quels champs sont candidats à la jointure
	        "id_publication" => "id_publication"
	);

	// Table des tables
	$tables_principales['vu_annonces'] = array(
		'field' => &$annonces,
		'key' => &$annonces_key,
		'join' => &$annonces_join
	);
	
	$tables_principales['vu_evenements'] = array(
		'field' => &$evenements,
		'key' => &$evenements_key,
		'join' => &$evenements_join
	);
	$tables_principales['vu_publications'] = array(
		'field' => &$publications,
		'key' => &$publications_key,
		'join' => &$publications_join
	);

	return $tables_principales;
}

// ---------- 2/3 - Declarations des tables auxiliaires (de jointures)
function vu_declarer_tables_auxiliaires($tables_auxiliaires){
	
	// Table de jointure vu_annonces_mots
        $annonces_mots = array(
                "id_mot" => "BIGINT(21) NOT NULL",
                "id_annonce" => "BIGINT(21) NOT NULL"
        );
        $annonces_mots_key = array(
                "PRIMARY KEY" => "id_mot, id_annonce"
        );

	// Table de jointure vu_evenements_mots
        $evenements_mots = array(
                "id_mot" => "BIGINT(21) NOT NULL",
                "id_evenement" => "BIGINT(21) NOT NULL"
        );
        $evenements_mots_key = array(
                "PRIMARY KEY" => "id_mot, id_evenement",
        );

	// Table de jointure vu_publications_mots
        $publications_mots = array(
                "id_mot" => "BIGINT(21) NOT NULL",
                "id_publication" => "BIGINT(21) NOT NULL"
        );
        $publications_mots_key = array(
                "PRIMARY KEY" => "id_mot, id_publication",
        );

	// Table des tables        
        $tables_auxiliaires['vu_annonces_mots'] = array(
                'field' => &$annonces_mots,
                'key' => &$annonces_mots_key
        );
        $tables_auxiliaires['vu_evenements_mots'] = array(
                'field' => &$evenements_mots,
                'key' => &$evenements_mots_key
        );
        $tables_auxiliaires['vu_publications_mots'] = array(
                'field' => &$publications_mots,
                'key' => &$publications_mots_key
        );
        
        return $tables_auxiliaires;
}


// ---------- 3/3 - Declatation des interfaces, modalites d'utilisation des tables

function vu_declarer_tables_interfaces($interface){
	
	// definir les jointures possibles
		// de tables vu_* vers vu_*_mots : sur le champ id_*
	        $interface['tables_jointures']['vu_annonces']['id_annonce'] = 'vu_annonces_mots';
        	$interface['tables_jointures']['vu_evenements']['id_evenement'] = 'vu_evenements_mots';
        	$interface['tables_jointures']['vu_publications']['id_publication'] = 'vu_publications_mots';

		// de tables vu_*_mots vers vu_* : sur le champ id_*
	        $interface['tables_jointures']['vu_annonces_mots']['id_annonce'] = 'vu_annonces';
	        $interface['tables_jointures']['vu_evenements_mots']['id_evenement'] = 'vu_evenements';
	        $interface['tables_jointures']['vu_publications_mots']['id_publication'] = 'vu_publications';

		// de spip_mots vers vu_*_mots : sur le champ id_mot
	        $interface['tables_jointures']['spip_mots']['id_mot'] = 'vu_annonces_mots';
	        $interface['tables_jointures']['spip_mots']['id_mot'] = 'vu_evenements_mots';
	        $interface['tables_jointures']['spip_mots']['id_mot'] = 'vu_publications_mots';

		// de vu_*_mots vers spip_mots : sur le champ id_mot
		$interface['tables_jointures']['vu_annonces_mots']['id_mot'] = 'spip_mots';
		$interface['tables_jointures']['vu_evenements_mots']['id_mot'] = 'spip_mots';
		$interface['tables_jointures']['vu_publications_mots']['id_mot'] = 'spip_mots';

        // Titre pour url
        //$interface['table_titre']['spip_annonces'] = "titre, '' AS lang";

	// On definit les traitements par défaut sur les champs qui le necessitent
	//$interface['table_des_traitements']['MON_CHAMPS'][]= 'ma_fonction(%s)';

	// On indique les champs 'date' pour accelerer les requetes SQL
	$interface['table_date']['vu_annonces'] = 'date_peremption';
	$interface['table_date']['vu_annonces'] = 'date_vue';
	$interface['table_date']['vu_annonces'] = 'date_redac';

	$interface['table_date']['vu_evenement'] = 'date_evenement';
	$interface['table_date']['vu_evenement'] = 'date_vue';
	$interface['table_date']['vu_evenement'] = 'date_redac';

	$interface['table_date']['vu_publications'] = 'date_redac';
	$interface['table_date']['vu_publications'] = 'date_vue'; 

        return $interface;

}

?>
