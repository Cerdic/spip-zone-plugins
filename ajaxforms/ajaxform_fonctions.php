<?php

/**
 * Affiche la boucle éventuelle dans laquelle est placée la balise et si
 * c'est le cas, le type de boucle
 *
 * copie de balise_TYPE_BOUCLE du plugin etiquette
 *
 * @param Object $p
 * @return Object
 */
function balise_AJAXFORM_TYPE_BOUCLE_dist($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? $type : "balise_hors_boucle";
	return $p;
}

?>
