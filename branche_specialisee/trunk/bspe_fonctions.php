<?php

/**
 * Déclarations de fonctions
 * 
 * @plugin Branches Specialisées pour SPIP
 * @license GPL
 * @package SPIP\BSPE\Fonctions
**/


/**
 * Critère de specialisation d'une branche
 * 
 * @critere specialise
 * @example
 * {!specialise} ou implicite
 * {specialise s1}
 * {specialise s1,s2}
 * {specialise}
 * {specialise sauf, s1}
 * {specialise sauf, s1,s2}
 * {!specialise sauf, s1}
 * {!specialise sauf, s1,s2}
 * {specialise #ENV{liste}}
 * {specialise sauf, #ENV{liste}}
 *
 * @param string $idb     Identifiant de la boucle
 * @param array $boucles  AST du squelette
 * @param Critere $crit   Paramètres du critère dans cette boucle
 * @return void
 */
function critere_specialise_dist($idb, &$boucles, $critere) {

	$not = $critere->not;
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_rubrique', $boucles);

	//Trouver une jointure
	$champ = "id_rubrique";
	$desc = $boucle->show;
	//Seulement si necessaire
	if (!array_key_exists($champ, $desc['field'])){
		$cle = trouver_jointure_champ($champ, $boucle);
		$trouver_table = charger_fonction("trouver_table", "base");
		$desc = $trouver_table($boucle->from[$cle]);
		if (count(trouver_champs_decomposes($champ, $desc))>1){
			$decompose = decompose_champ_id_objet($champ);
			$champ = array_shift($decompose);
			$boucle->where[] = array("'='", _q($cle.".".reset($decompose)), '"'.sql_quote(end($decompose)).'"');
		}
	}
	else $cle = $boucle->id_table;

	$c = "sql_in('$cle".".$champ', calculer_specialise_in($arg)".")";
	$boucle->where[] = !$critere->cond ? $c :
		("($arg ? $c : ".($not ? "'0=1'" : "'1=1'").')');

}

function calculer_specialise_in($id, $sauf=false, $types=array()) {
	$calculer = charger_fonction('calculer_specialise_in', 'inc');
	return $calculer($id, $sauf, $types);
}

?>
