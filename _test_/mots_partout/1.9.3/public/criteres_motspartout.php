<?

//ajout d'un critére branchemot basé sur critére branche
// {branchemot ?}
// http://www.spip.net/@branche
// http://doc.spip.org/@critere_branche_dist
function critere_branchemot($idb, &$boucles, $crit)
{
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_groupe', $boucles);

	$c = "sql_in('" . $boucle->id_table . ".id_groupe', calcul_branchemot($arg)"
	  . ($crit->not ? ", 'NOT'" : '')
	  . ")";

	$boucle->where[]= $crit->cond ? "($arg ? $c : 1=1)" : $c;
}	
 
?>
