<?php

function filtre_puce_statut_dist($id_objet, $statut, $id_rubrique, $type){
	$puce_statut = charger_fonction('puce_statut', 'inc');
	return $puce_statut($id_objet, $statut, $id_rubrique, $type);
}


/**
 * {where}
 * tout simplement
 *
 * @param <type> $idb
 * @param <type> $boucles
 * @param <type> $crit
 */
function critere_where_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if (isset($crit->param[0]))
		$_where = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	else
		$_where = '@$Pile[0]["where"]';

	if ($crit->cond)
		$_where = "(($_where) ? ($_where) : '')";
	
	if ($crit->not)
		$_where = "array('NOT',$_where)";

	$boucle->where[]= $_where;
}

?>