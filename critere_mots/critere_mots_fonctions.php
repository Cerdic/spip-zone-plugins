<?php

// Critere {mots} : "l'article est lie a tous les mots demandes"
// {mots?} ne s'applique que si au moins un mot est demande
// on passe dans l'url &mots[]=titre1&mots[]=titre2
// et/ou &mots[]=11 etc
// parametre optionnel : {mots score} ou score est un nombre entre 0 et 1
// qui indique le pourcentage de mots a valider
// ex: {mots? 0.66} selectionne tous les articles qui ont au moins 2/3
// de mots en commun avec ceux demandes par le contexte (ou l'URL)
// par defaut score=1 (tous les mots demandes doivent figurer)
function critere_mots_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];

	if (isset($crit->param[0])) {
		$score = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	} else
		$score = '1';

	$quoi = '@$Pile[0]["mots"]';

	$boucle->hash .= '
	// {MOTS}
	$prepare_mots = charger_fonction(\'prepare_mots\', \'inc\');
	$mots_where = $prepare_mots('.$quoi.', "'.$boucle->id_table.'", "'.$crit->cond.'", '.$score.', "' . $boucle->sql_serveur . '");
	';

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$idb]->select))
	  $boucle->select[]= $t; # pour postgres, neuneu ici

	$boucle->where[] = "\n\t\t".'$mots_where';

}


function inc_prepare_mots_dist($mots, $table='articles', $cond=false, $score, $serveur='') {
    
	if (!is_array($mots)
	OR !$mots = array_filter($mots)) {
		// traiter le cas {mots?}
		if ($cond)
			return '';
		else
		// {mots} mais pas de mot dans l'url
			return '0=1';
	}


	$_table = str_replace('spip_', '', table_objet_sql($table));
	$_id_table = id_table_objet($table);
	$where = array();

	foreach($mots as $mot) {
		if (preg_match(',^[1-9][0-9]*$,', $mot))
			$id_mot = $mot;
		else
			$id_mot = sql_getfetsel('id_mot', 'spip_mots', 'titre='.sql_quote($mot));
		$where[] = 'id_mot='.sql_quote($id_mot);
	}

	// on analyse la jointure spip_mots_$_table
	// sans regarder spip_mots ni les groupes
	// (=> faire attention si on utilise les mots techniques)
	
	// si on a un % dans le score, c'est que c'est une fraction ou %age
	if (ereg ('%',$score)){
	       if ($score>1){
	           $score = $score/100;
	       } // si exprime en %
	   $having = ' HAVING SUM(1) >= '.ceil($score * count($where)) ;
	}
	else{
	   $having = ' HAVING SUM(1) >= '. $score;
	   }
	
	$wh = "$_table.$_id_table IN (
		SELECT $_id_table FROM spip_mots_$_table WHERE "
		. join(' OR ', $where)
		. ' GROUP BY '.$_id_table
		. $having
		. "\n\t)";

	return $wh;
}

