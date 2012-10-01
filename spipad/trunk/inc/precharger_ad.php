<?php
/**
 * Plugin Annonces
 * (c) 2012 apéro spip
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/precharger_objet');

function inc_precharger_ad_dist($id_ad, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('ad', $id_ad, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_ad_dist($id_ad, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('ad', $id_ad, $id_rubrique, $lier_trad, 'titre');
}


?>