<?php

include_spip('inc/objets_fonctions');




function objets_declarer_tables_interfaces($interface){
	
	//on récupére uin tableau des objets déjà installés
	$objets_installes=liste_objets_meta();
	
	global $table_des_tables;
	foreach ($objets_installes as $objet) {
		if($objet!=""){
			$interface['table_des_tables'][$objet] = $objet;
			$interface['table_des_traitements']['TITRE'][$objet] = _TRAITEMENT_TYPO; // corrections de francais
		  
			//permet de faire les jointures et donc permettre les criteres {id_article} et {id_rubrique} dans les boucles
			//si on le lie aux articles
			$interface['tables_jointures']['spip_articles'][]= $objet.'_liens';
			//si on le lie aux rubriques
			$interface['tables_jointures']['spip_rubriques'][]= $objet.'_liens';
			
			$interface['tables_jointures']['spip_'.$objet][]= $objet.'_liens';
			
			//permet de prendre le titre dans la génération des urls
			$interface['table_titre'][$objet] = "titre,'' as lang";

		}
	}
		
	return $interface;
}


function objets_declarer_tables_principales($tables_principales){

	//-- Chaque table des objets est enregistré dans spip_meta séparé par des virgules ---------
	
	$objets_installes=liste_objets_meta();
	
	foreach ($objets_installes as $objet) {
		if($objet!=""){
			$nom_objet=objets_nom_objet($objet);
			
			$objets = array(
				"id_".$nom_objet	=> "bigint(21) NOT NULL",
				"titre"	=> "text DEFAULT '' NOT NULL",
				"statut"	=> "VARCHAR(10) DEFAULT 'prepa' NOT NULL",
				"id_secteur"	=> "bigint(21) NOT NULL",
				"maj"	=> "TIMESTAMP",
			  "date"  => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
				);
		
		 $objets_key = array(
				"PRIMARY KEY"	=> "id_".$nom_objet
				);
		
		$tables_principales['spip_'.$objet] =
			array('field' => $objets, 'key' => $objets_key);
		
			
		}
		
	}
	
	return $tables_principales;
}


function objets_declarer_tables_auxiliaires($tables_auxiliaires){
	
	$objets_installes=liste_objets_meta();
	
	foreach ($objets_installes as $objet) {
		if($objet!=""){
			$nom_objet=objets_nom_objet($objet);
			
			$spip_objets_liens = array(
			"id_".$nom_objet	=> "bigint(21) DEFAULT '0' NOT NULL",
			"id_objet"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"objet"	=> "VARCHAR (25) DEFAULT '' NOT NULL",
			"vu"	=> "ENUM('non', 'oui') DEFAULT 'non' NOT NULL"); //le vu je ne sais pas a quoi il sert ... 
	
			$spip_objets_liens_key = array(
					"PRIMARY KEY"		=> "id_".$nom_objet.",id_objet,objet",
					"KEY id_".$nom_objet	=> "id_".$nom_objet
			);
			
			$tables_auxiliaires['spip_'.$objet.'_liens'] = array(
			'field' => $spip_objets_liens,
			'key' => $spip_objets_liens_key);
		}
	}
	return $tables_auxiliaires;
	
}




?>