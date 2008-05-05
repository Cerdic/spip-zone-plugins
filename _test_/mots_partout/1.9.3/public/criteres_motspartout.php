<?

//ajout d'un critére branchemot basé sur critére branche
// {branchemot ?}
// http://www.spip.net/@branche
// http://doc.spip.org/@critere_branche_dist
function critere_branchemot($idb, &$boucles, $crit) {
	$not = $crit->not;
	$boucle = &$boucles[$idb];

	$arg = calculer_argument_precedent($idb, 'id_groupe', $boucles);

	$c = "calcul_mysql_in('" .
	  $boucle->id_table .
	  ".id_groupe', calcul_branchemot($arg), '')";
	if ($crit->cond && true) $c = "($arg ? $c : 1)";
			
	if ($not)
		$boucle->where[]= array("'NOT'", $c);
	else
		$boucle->where[]= $c;
}	
 
?>