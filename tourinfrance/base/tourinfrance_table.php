<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function tourinfrance_declarer_tables_interfaces($interface){

	//Tableau des type d'offre.
	$tab_types_tourinfrance = array('hot', 'hpa', 'hlo', 'res', 'fma', 'pna', 'pcu', 'deg', 'loi', 'asc', 'iti', 'vil', 'org', 'mul');

   // 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['tourinfrance_flux']='tourinfrance_flux';
	
	for($i=0; $i<count($tab_types_tourinfrance); $i++){
		$nom_table = "tourinfrance_" . $tab_types_tourinfrance[$i];
    	$interface['table_des_tables'][$nom_table]=$nom_table;
	}

   return $interface;
}
	
function tourinfrance_declarer_tables_principales($tables_principales){
	
	//Tableau des type d'offre.
	$tab_types_tourinfrance = array('hot', 'hpa', 'hlo', 'res', 'fma', 'pna', 'pcu', 'deg', 'loi', 'asc', 'iti', 'vil', 'org', 'mul');
	
	/************************************************/
	/*****  TABLE TOURINFRANCE_FLUX  ****************/
	$tourinfrance_flux = array(
			"id_flux"	=> "bigint(21) NOT NULL",
	   	 	"url_flux"  => "text DEFAULT '' NOT NULL",
	  	  	"id_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"nom_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"description_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"datecrea"    => "text DEFAULT '' NOT NULL",
	  	  	"datemaj"    => "text DEFAULT '' NOT NULL",
	  	  	"id_objettour"    => "text DEFAULT '' NOT NULL",
	  	  	"id_type"    => "text DEFAULT '' NOT NULL",
	  	  	"id_cat_cam"    => "text DEFAULT '' NOT NULL"
			);
	
	$tourinfrance_flux_key = array(
			"PRIMARY KEY"	=> "id_flux"
			);
	
	$tables_principales['spip_tourinfrance_flux'] =
		array('field' => &$tourinfrance_flux, 'key' => &$tourinfrance_flux_key);

	
	/****************************************************************************/
	/*****  Creation de tables TOURINFRANCE_type par type d'offre  **************/
	
	for($i=0; $i<count($tab_types_tourinfrance); $i++){
	
		$nom_tab = "tourinfrance_" . $tab_types_tourinfrance[$i];
		$nom_tab_key = $nom_tab . "_key";
		
		${$nom_tab} = array(
      	  	"id"	=> "bigint(21) NOT NULL",
			"id_flux"  => "text DEFAULT '' NOT NULL",
	  	  	"id_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"nom_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"description_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"datecrea"    => "text DEFAULT '' NOT NULL",
	  	  	"datemaj"    => "text DEFAULT '' NOT NULL",
	  	  	"id_objettour"    => "text DEFAULT '' NOT NULL",
	  	  	"id_type"    => "text DEFAULT '' NOT NULL",
	  	  	"id_cat_cam"    => "text DEFAULT '' NOT NULL",
	  	  	"extra"    => "text DEFAULT '' NOT NULL",
      	  	"id_article"    => "bigint(21) NOT NULL"
      		);
      		
      	${$nom_tab_key} = array(
	        "PRIMARY KEY"   => "id"
       		);
       		
	    $tables_principales['spip_' . $nom_tab] = array(
	        'field' => &${$nom_tab},
	        'key' => &${$nom_tab_key}
	        );
	}
	
	
	/*
	// Extension de la table ARTICLES
	$tables_principales['spip_articles']['field']['type_tourinsoft'] = "text DEFAULT '' NOT NULL";	
	$tables_principales['spip_articles']['field']['donnees_tourinsoft'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_articles']['field']['identifiant_offre_tourinsoft'] = "text DEFAULT '' NOT NULL";
	*/
	
	return $tables_principales;
	
}
?>
