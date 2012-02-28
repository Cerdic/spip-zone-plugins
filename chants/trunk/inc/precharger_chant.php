<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/precharger_objet');

function inc_precharger_chant_dist($id_chant, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('chant', $id_chant, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_chant_dist($id_chant, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('chant', $id_chant, $id_rubrique, $lier_trad, 'titre');
}

?>