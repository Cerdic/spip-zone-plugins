<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2012 kent1
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/precharger_objet');

function inc_precharger_collection_dist($id_collection, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('collection', $id_collection, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_collection_dist($id_collection, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('collection', $id_collection, $id_rubrique, $lier_trad, 'titre');
}


?>