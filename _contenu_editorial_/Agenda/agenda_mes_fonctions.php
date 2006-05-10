<?php

//include_spip('inc/agenda_filtres'); // declaration directe dans le xml pour eviter un find_in_path

// {branche ?}
// http://www.spip.net/@branche
function critere_branche($idb, &$boucles, $crit) {
	$not = $crit->not;
	$boucle = &$boucles[$idb];
  $type = $boucle->type_requete;
  $nom = $table_des_tables[$type];
  if ($boucle->id_table!='evenements')
  	critere_branche_dist($idb, &$boucles, $crit);
  else{
		$arg = calculer_argument_precedent($idb, 'id_rubrique', $boucles);
		$champ = 'id_rubrique';

    $type = $boucle->type_requete;
    $nom = $table_des_tables[$type];
    list($nom, $desc) = trouver_def_table($nom ? $nom : $type, $boucle);

    $cle = trouver_champ_exterieur($champ, $boucle->jointures, $boucle);
    if ($cle) 
      $cle = calculer_jointure($boucle, array($boucle->id_table, $desc), $cle, false);
    if ($cle) $t = "L$cle";
		// faire la jointure sur id_rubrique
		
		$c = "calcul_mysql_in('" .
		  $t .
		  ".id_rubrique', calcul_branche($arg), '')";
		if ($crit->cond) $c = "($arg ? $c : 1)";
				
		if ($not)
			$boucle->where[]= array("'NOT'", $c);
		else
			$boucle->where[]= $c;
  }
}

?>