<?php
function balise_NB_RUBRIQUES_BRANCHE($p)
{
	return calculer_balise_dynamique ($p, 'NB_RUBRIQUES_BRANCHE', array( 'id_rubrique'));
}

function  balise_NB_RUBRIQUES_BRANCHE_stat ($args, $filtres) {
	return $args;
}

function balise_NB_RUBRIQUES_BRANCHE_dyn($id_rubrique) {
	
	$rubriques = calcul_branche_in($id_rubrique);

	
	return count(explode(',', $rubriques));
}

?>