<?php

/*
on va s'appuyer du le tableau global $table_date pour les date de debut
on va donc créer le tableau global $table_date_fin pour gérer les date 
de fin d'evenement
*/

//critere pour lister les evenements qui sont passes
// date de fin inferieure a la date de reference, maintenant par defaut
function critere_revolus($idb, &$boucles, $crit) {
  global $table_date_fin;
  $not = $crit->not;
  $boucle = &$boucles[$idb];
  $date_orig = $table_date_fin[$boucle->type_requete];
  $date_orig = $boucle->id_table . '.' . $date_orig;
  $arg = kwote(calculer_argument_precedent($idb, 'date', $boucles));
  $c = array("'<'", "'$date_orig'", $arg);
  $boucle->where[]= ($crit->not ? array("'NOT'", $c) : $c);
}

//critere pour lister les evenements qui sont passes
// date de debut superieure a la date de reference, maintenant par defaut
function critere_a_venir($idb, &$boucles, $crit) {
  global $table_date;
  $not = $crit->not;
  $boucle = &$boucles[$idb];
  $date_orig = $table_date[$boucle->type_requete];
  $date_orig = $boucle->id_table . '.' . $date_orig;
  $arg = kwote(calculer_argument_precedent($idb, 'date', $boucles));
  $c = array("'>'", "'$date_orig'", $arg);
  $boucle->where[]= ($crit->not ? array("'NOT'", $c) : $c);
}

//critere pour lister les evenements qui sont en cours
// date de debut inferieure a la date de reference
// ET date de fin superieure a la date de reference, maintenant par defaut
// non revolus et non avenir
function critere_en_cours($idb, &$boucles, $crit) {
  global $table_date_fin, $table_date;
  $not = $crit->not;
  $boucle = &$boucles[$idb];
  $date_fin = $table_date_fin[$boucle->type_requete];
  $date_fin = $boucle->id_table . '.' . $date_fin;
  $date_orig = $table_date[$boucle->type_requete];
  $date_orig = $boucle->id_table . '.' . $date_orig;
  $arg = kwote(calculer_argument_precedent($idb, 'date', $boucles));
  $c1 = array("'<='", "'$date_orig'", $arg);
  $c2 = array("'>='", "'$date_fin'", $arg);
  $c = array("'AND'", $c1, $c2);
  $boucle->where[]= ($crit->not ? array("'NOT'", $c) : $c);
}

//
// <BOUCLE(ANNONCES)>
//
function boucle_ANNONCES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_messages";
	$mstatut = $id_table .'.statut';
	$rv = $id_table .'.rv';
	$type = $id_table .'.type';

	// Restreindre aux elements publics et evenementiels
	$boucle->where[]= array("'='", "'$rv'", "'\"oui\"'");
	$boucle->where[]= array("'='", "'$type'", "'\"affich\"'");
	// Restreindre aux elements publies
	if (!$boucle->modificateur['criteres']['statut']) {
		if (!$GLOBALS['var_preview']) {
			$boucle->where[]= array("'='", "'$mstatut'", "'\"publie\"'");
		} else
			$boucle->where[]= array("'IN'", "'$mstatut'", "'(\"publie\",\"redac\")'");
	}
	return calculer_boucle($id_boucle, $boucles); 
}


?>