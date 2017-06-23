<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define ('_CRITERE_MOTS_OPTIMISE',1);// pour pouvoir revenir aux anciennes requetes si besoin

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
function critere_mots_dist($idb, &$boucles, $crit,$id_ou_titre=false) {

	$boucle = &$boucles[$idb];
	$_table = table_objet($boucle->id_table);
	$objet_delatable=objet_type($_table);
	$id_objet = id_table_objet($boucle->id_table);
	$tri = false;

	// pouvoir utiliser plusieurs fois le critère dans une même boucle.
	$hash = substr(uniqid(), -4);

	if (isset($crit->param[0][2]) and ($crit->param[0][2]->texte == "tri" or $crit->param[0][2]->texte=="!tri")){
			$tri = true;
	}
	
	if (isset($crit->param[0][0])) {
		$score = calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		$score = "'100%'";
	}

	if (isset($crit->param[0][1])) {
		$quoi = calculer_liste(array($crit->param[0][1]), array(), $boucles, $boucles[$idb]->id_parent);
	} else {
		$quoi = '@$Pile[0]["mots"]';
	}
	$boucle->hash .= '
	// {MOTS}
	$prepare_mots = charger_fonction(\'prepare_mots\', \'inc\');
	$mots_where_' . $hash . ' = $prepare_mots('.$quoi.', "'.$boucle->id_table.'", "'.$crit->cond.'", '.$score.', "' . $boucle->sql_serveur . '","'.$id_ou_titre.'");
	';

	$t = $boucle->id_table . '.' . $boucle->primary;
	if (!in_array($t, $boucles[$idb]->select)) {
		$boucle->select[]= $t; # pour postgres, neuneu ici
	}

	$boucle->where[] = "\n\t\t".'$mots_where_' . $hash;
	if ($tri == true) {

		$boucle->jointures[]="mots_liens" ;
		$boucle->from['mots_liens'] = "spip_mots_liens";
		$boucle->join["mots_liens"] = array(
		    "'$boucle->id_table'",
		    "'id_objet'",
		    "'$id_objet'",
		    "'mots_liens.objet='.sql_quote('$objet_delatable')");
		$boucle->where[] = "\n\t\t".'sql_in(\'mots_liens.id_mot\',sql_quote('.$quoi.'))';
		$boucle->group[] = "mots_liens.id_objet";
		if ($crit->param[0][2]->texte == "tri") // si dans le sens ascendant
		    $boucle->order[] = "'COUNT(mots_liens.id_objet) ASC'";
		else
		    $boucle->order[] = "'COUNT(mots_liens.id_objet) DESC'";
		
		// Pseudo critère "Si"
		$boucle->hash .= "\n\tif (!isset(\$si_init)) { \$command['si'] = array(); \$si_init = true; }\n";
		$boucle->hash .= "\t\$command['si'][] = (count($quoi) > '0');";
	}


}



function critere_mots_selon_id_dist($idb, &$boucles, $crit){
    critere_mots_dist($idb, $boucles, $crit,'id');
}
function critere_mots_selon_titre_dist($idb, &$boucles, $crit){
    critere_mots_dist($idb, $boucles, $crit,'titre');
}


function inc_prepare_mots_dist($mots, $table='articles', $cond=false, $score, $serveur='',$id_ou_titre=false) {
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


  $_table = table_objet($table);
  $objet_delatable=objet_type($_table);

	$_id_table = id_table_objet($table);
	$where = array();
    
  //selon le cas, on sélectionne sur les titres ou sur les id
  if (!$id_ou_titre){
      foreach($mots as $mot) {
          if (preg_match(',^[1-9][0-9]*$,', $mot))
              $id_mot = $mot;
          else
              $id_mot = sql_getfetsel('id_mot', 'spip_mots', 'titre='.sql_quote($mot));
          $where[] = 'id_mot='.sql_quote($id_mot).' and objet='.sql_quote($objet_delatable);
      }
  }
	elseif($id_ou_titre == 'id'){
	   foreach($mots as $mot) {
	       $where[] = 'id_mot='.sql_quote($mot).' and objet='.sql_quote($objet_delatable);
	   }
	}
	elseif($id_ou_titre == 'titre'){
	   foreach($mots as $mot) {
	        $id_mot = sql_getfetsel('id_mot', 'spip_mots', 'titre='.sql_quote($mot));
            $where[] = 'id_mot='.sql_quote($id_mot) .' and objet='.sql_quote($objet_delatable);
	   }
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
	
	$in = array();
	// Normalement on devrait faire un sous-select, mais mysql 5* a un bug (ttp://bugs.mysql.com/bug.php?id=32665)
	// du coup on calcule d'abord le résultat de la sous-select, et on intègre cela inc_prepare_mots_dist
  $s = sql_query("SELECT id_objet as i FROM spip_mots_liens WHERE "
    . join(' OR ', $where)
    . ' GROUP BY id_objet,objet'
    . $having);
  while($t = sql_fetch($s)){
			$in[] = $t['i'];
	}
	if ($in){
	  $wh = sql_in("$_table.$_id_table", $in);
  }
	else{
		$wh = '22=0';//1=0 est automatiquement filtré par le compilateur
	}
	return $wh;
}

function critere_mots_enleve_mot_de_liste($listemots, $id_mot) {
	if (!is_array($listemots) OR !$listemots)
		return $listemots;
	$listemots = array_unique($listemots);
	$listemots = array_diff($listemots,array($id_mot));
	return $listemots;
}
