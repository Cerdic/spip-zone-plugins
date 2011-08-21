<?php


if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/precharger_objet');

function inc_precharger_rubrique_dist($id, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('rubrique', $id, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_rubrique_dist($id, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('rubrique', $id, $id_rubrique, $lier_trad, 'titre');
}



?>
