<?php

// copie joyeusement sur le plugin etiquette (balise_TYPE_BOUCLE)
// bete de copier plusieurs fois cette fonction du coup.
// a mettre dans le core ?
function balise_AJAXFORM_TYPE_BOUCLE_dist($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? $type : "balise_hors_boucle";
	return $p;   
}

?>
