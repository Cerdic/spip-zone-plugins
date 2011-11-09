<?php

include_spip("inc/fraisdon_filtres");

//
// <BOUCLE(FRAISDONS)>
//
function boucle_FRAISDONS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_fraisdons";

	return calculer_boucle($id_boucle, $boucles); 
}



?>
