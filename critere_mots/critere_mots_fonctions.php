<?php

// Critere {mots} : "l'article est lie a tous les mots demandes"
// {mots?} ne s'applique que si au moins un mot est demande
// on passe dans l'url &mots[]=titre1&mots[]=titre2
// et/ou &mots[]=11 etc
// parametre optionnel : {mots score} ou score est un nombre entre 0 et 1
// qui indique le pourcentage de mots a valider
// ex: {mots? 0.66} selectionne tous les articles qui ont au moins 2/3
// ou encore {mots score} qui indique le nombre de mots communs (par exemple 2) si score >= 1
// ex: {mots 2} indique qu'il doit y avoir 2 mots communs
// ou encore {mots 66%} indique qu'on doit avoir 66% de mot en commun (identique à {mots 0.66} 
// de mots en commun avec ceux demandes par le contexte (ou l'URL)
// par defaut score=100% (tous les mots demandes doivent figurer)
function critere_mots_dist($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];

	if (isset($crit->param[0][0])) {
		$score = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$idb]->id_parent);
	} else{
		$score = "'100%'";
    }
    if (isset($crit->param[0][1])){
        $quoi = calculer_liste(array($crit->param[0][1]), array(), $boucles, $boucles[$idb]->id_parent);
        }
    else{
        $quoi = '@$Pile[0]["mots"]';
    }

	
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

    $score = trim($score);
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
    
	// si on a un % dans le score, c'est que c'est un %age
	if (substr($score,-1)=='%'){
        
	   $score = str_replace('%','',$score);
	   $having = ' HAVING SUM(1) >= '.ceil($score/100 * count($where)) ;
	}
	elseif ((0 < $score) and ($score < 1)){
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

