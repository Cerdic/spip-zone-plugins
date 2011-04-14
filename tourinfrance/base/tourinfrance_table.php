<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function tourinfrance_declarer_tables_interfaces($interface){

	//Tableau des bordereaux.
	include_spip('base/tourinfrance_bordereaux');
	$tab_bordereaux_tourinfrance = creer_tab_bordereaux();

   // 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['tourinfrance_flux']='tourinfrance_flux';
	
	for($i=0; $i<count($tab_bordereaux_tourinfrance); $i++){
		$nom_table = "tourinfrance_" . $tab_bordereaux_tourinfrance[$i];
    	$interface['table_des_tables'][$nom_table]=$nom_table;
	}

   return $interface;
}
	
function tourinfrance_declarer_tables_principales($tables_principales){
	
	//Tableau des bordereaux.
	include_spip('base/tourinfrance_bordereaux');
	$tab_bordereaux_tourinfrance = creer_tab_bordereaux();
	
	/************************************************/
	/*****  TABLE TOURINFRANCE_FLUX  ****************/
	$tourinfrance_flux = array(
			"id_flux"	=> "bigint(21) NOT NULL",
	   	 	"nom_flux"  => "text DEFAULT '' NOT NULL",
	   	 	"url_flux"  => "text DEFAULT '' NOT NULL",
	   	 	"type_flux"  => "text DEFAULT '' NOT NULL",
	  	  	"id_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"nom_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"description_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"commune"    => "text DEFAULT '' NOT NULL",
	  	  	"datecrea"    => "text DEFAULT '' NOT NULL",
	  	  	"datemaj"    => "text DEFAULT '' NOT NULL",
	  	  	"id_type"    => "text DEFAULT '' NOT NULL",
	  	  	"id_cat_cam"    => "text DEFAULT '' NOT NULL"
			);
	
	$tourinfrance_flux_key = array(
			"PRIMARY KEY"	=> "id_flux"
			);
	
	$tables_principales['spip_tourinfrance_flux'] =
		array('field' => &$tourinfrance_flux, 'key' => &$tourinfrance_flux_key);

	
	/****************************************************************************/
	/*****  Creation de tables TOURINFRANCE_bordereau par bordereau  **************/
	
	for($i=0; $i<count($tab_bordereaux_tourinfrance); $i++){
	
		$nom_tab = "tourinfrance_" . $tab_bordereaux_tourinfrance[$i];
		$nom_tab_key = $nom_tab . "_key";
		
		${$nom_tab} = array(
      	  	"id"	=> "bigint(21) NOT NULL",
			"id_flux"  => "text DEFAULT '' NOT NULL",
	  	  	"id_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"nom_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"description_offre"    => "text DEFAULT '' NOT NULL",
	  	  	"commune"    => "text DEFAULT '' NOT NULL",
	  	  	"datecrea"    => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	  	  	"datemaj"    => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
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
	
	return $tables_principales;
	
}
?>
