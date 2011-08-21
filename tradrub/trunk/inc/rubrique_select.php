<?php


if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/editer_select');

function inc_rubrique_select_dist($id, $id_rubrique=0, $lier_trad=0) {
	return select_objet('rubrique', $id, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_rubrique_select_trad_dist($id, $id_rubrique=0, $lier_trad=0) {
	return select_objet_trad('rubrique', $id, $id_rubrique, $lier_trad, 'titre');
}



?>
