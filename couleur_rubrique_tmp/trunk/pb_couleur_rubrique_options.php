<?php
//
// functions
//
function pb_couleur_rubrique($id_rubrique) {
	$pb_couleur_rubrique = lire_meta("pb_couleur_rubrique$id_rubrique");
	return $pb_couleur_rubrique;
}

function couleur_rubrique($id_rubrique) {
	return pb_couleur_rubrique($id_rubrique);
}

?>
