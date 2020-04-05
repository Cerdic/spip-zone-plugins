<?php

/**
 * Factures par années
 * 
 * Sorte de 'annee' comme un peu le critere agenda de SPIP.
**/
function critere_fannees_dist($idb, &$boucles, $crit){
	$params = $crit->param;

	if (count($params)<1)
		return (array('zbug_critere_inconnu', array('critere' => $crit->op." ?")));

	$parent = $boucles[$idb]->id_parent;

	// les valeurs $date et $type doivent etre connus a la compilation
	// autrement dit ne pas etre des champs

	$date = array_shift($params);
	$date = $date[0]->texte;

	$annee = $params ? array_shift($params) : "";
	$annee = "\n".'(($x = '.
	         calculer_liste($annee, array(), $boucles, $parent).
	         ') ? $x : "%")';

	$boucle = &$boucles[$idb];
	$date = $boucle->id_table.".$date";
	$quote_end = ",'".$boucle->sql_serveur."','text'";
	
	$boucle->where[] = array("'LIKE'", "'DATE_FORMAT($date, \'%Y\')'",
	                         ("sql_quote($annee$quote_end)"));

}
