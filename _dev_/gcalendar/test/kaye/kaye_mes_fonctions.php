<?php
/**
	 * Kayé
	 * Le cahier de texte électronique spip spécial primaire
	 * Copyright (c) 2007
	 * Cédric Couvrat
	 * http://alecole.ac-poitiers.fr/
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
**/


// Boucles SPIP-listes
global $tables_principales;

//Ensuite, donner le format des tables ajoutées. Par exemple :
$tables_principales['kaye']= array(
 'field' => array(
   "id_kaye" => "int NOT NULL AUTO_INCREMENT",
   "id_classe" => "int",
   "id_auteur" => "int",
   "titre" => "varchar(100)",
   "discipline" => "tinytext",
   "descriptif" => "TEXT",
   "date_jour" => "DATE NOT NULL DEFAULT '0000-00-00'",
   "date_echeance" => "DATE NOT NULL DEFAULT '0000-00-00'",
   "statut" => "TINYTEXT",
 ),
 'key' => array("PRIMARY KEY" => "id_kaye")
);
$tables_principales['documents_kaye']= array(
 'field' => array(
						"id_document" => "BIGINT(21) NOT NULL",
						"id_kaye" => "BIGINT(21) NOT NULL",
						),
					
	'key' => array("PRIMARY KEY" => "id_document")
	);
	
$tables_principales['classekaye']= array(
 'field' => array(
 
 	"id_classe" => "int NOT NULL AUTO_INCREMENT",
	"id_auteur" => "int",
	"id_zone" => "int",
	"id_parent" => "int NOT NULL DEFAULT '0'",
	"titre" => "varchar(100)",
	"niveau" => "varchar(100)",
	"descriptif" => "TEXT",
 ),
 'key' => array("PRIMARY KEY" => "id_prof")
);
						
//
// <BOUCLE(KAYE)>
//
function boucle_KAYE($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_kaye";  
        return calculer_boucle($id_boucle, $boucles);
}

//
// <BOUCLE(PROFKAYE)>
//
function boucle_CLASSEKAYE($id_boucle, &$boucles) {
        $boucle = &$boucles[$id_boucle];
        $id_table = $boucle->id_table;
        $boucle->from[$id_table] =  "spip_classekaye";  
        return calculer_boucle($id_boucle, $boucles);
}


	include_spip('inc/plugin');
	function Kaye_header_prive($flux){
	$flux .= '<link rel="stylesheet" type="text/css" href="'.direction_css(find_in_path('style01.css')).'" />';
	//appel de datepicker
	$flux .= '<script src="'._DIR_PLUGIN_KAYE.'js/calendar.js" type="text/javascript" language="javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_KAYE.'css/calendar.css" type="text/css" media="all" />';
		return $flux;
}
function Kaye_rediriger_javascript($url) {
		echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
		exit();
	}


?>