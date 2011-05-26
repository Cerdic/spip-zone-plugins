<?php

/* Le plugin orientation ajoute le critere {portrait} , {carre} ou {paysage} pour le classement des photos */
/**
 * critere {portrait} qui selectionne
 * - les documents dont les dimensions sont connues
 * - les documents dont la hauteur est superieure a la largeur
 *
 * {!portrait} exclus ces documents
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_portrait_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$not = ($crit->not?"NOT ":"");
	$boucle->where[] = "'$not($table.largeur>0 AND $table.hauteur > $table.largeur)'";
}

/**
 * critere {paysage} qui selectionne
 * - les documents dont les dimensions sont connues
 * - les documents dont la hauteur est inferieure a la largeur
 *
 * {!paysage} exclus ces documents
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_paysage_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$not = ($crit->not?"NOT ":"");
	$boucle->where[] = "'$not($table.largeur>0 AND $table.largeur > $table.hauteur)'";
}

/**
 * critere {carre} qui selectionne
 * - les documents dont les dimensions sont connues
 * - les documents dont la hauteur est egale a la largeur
 *
 * {!carre} exclus ces documents
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_carre_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$table = $boucle->id_table;
	$not = ($crit->not?"NOT ":"");
	$boucle->where[] = "'$not($table.largeur>0 AND $table.largeur = $table.hauteur)'";
}

?>