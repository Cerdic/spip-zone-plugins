<?php
// ajoute le critere {archive x}
function critere_archive_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;
	$marchive = $id_table .'.archive';

	$boucle->modificateur['criteres']['archive'] = true;

    //reduit le critére à la boucle articles uniquement
    if ($boucle->type_requete == 'articles') {
        //recherche la valeur de x dans {critere x}
        //si x vaut "seulement" alors on indique uniquement les articles archivés
        if ($crit->param[0][0]->texte == "seulement") {
	        $boucle->where[]= array("'='", "'$marchive'", "1");
	    //sinon tous les articles sont retournés archivé ou non
	    } else {
	        //ne fait rien
	    }
    }
}

//
// <BOUCLE(ARTICLES)>
// modifie le comportement de la boucle article en fonction de {archive}
function boucle_ARTICLES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$marchive = $id_table .'.archive';

    //si le critere {archive} est absent on affiche uniquement les elements non archivé
	if (!$boucle->modificateur['criteres']['archive']) {
 		if (!$GLOBALS['var_preview']) {
 			//ajoute le critere de selection
			$boucle->where[]= array("'IS'", "'$marchive'", "'NULL'");
		}
	} else {      
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
