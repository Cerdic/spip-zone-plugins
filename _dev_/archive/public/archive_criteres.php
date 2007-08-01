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
?>
