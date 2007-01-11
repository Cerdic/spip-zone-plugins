<?php

// ajoute le critere {archive}
function critere_archive_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['archive'] = true;
}


//
// <BOUCLE(ARTICLES)>
// modifie le comportement de la boucle article en fonction de {archive}
function boucle_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$marchive = $id_table .'.archive';

	if (!$boucle->modificateur['archive']) {
 		if (!$GLOBALS['var_preview']) {
 			//ajoute le critere de selection
			//$boucle->where[]= array("'<>'", "'$marchive'", "'\'1\''");
			$boucle->where[]= array("'IS'", "'$marchive'", "'NULL'");
		}
	}
	
	return boucle_ARTICLES_dist($id_boucle, $boucles);
}

/*
[17:16:04] cerdic: 	$marchive = $id_table .'.archive';
[17:16:05] cerdic: 	if (!$boucle->modificateur['criteres']['archive']) {
[17:16:05] cerdic: 		if (!$GLOBALS['var_preview']) {
[17:16:05] cerdic: 			$boucle->where[]= array("'='", "'$marchive'", "'\"non\"'");
[17:16:05] cerdic: 			//$boucle->where[]= array("'>'", "'$id_table" . ".date_archive'", "'NOW()'");
[17:16:07] cerdic: 			$boucle->where[]= array("'($id_table.date_archive > NOW() OR $id_table.date_archive=0)'");
[17:16:10] cerdic: 	}
*/


?>	
