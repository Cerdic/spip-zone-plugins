<?php
/**
 * Plugin Annonces services
 * (c) 2012 Collectif SPIP - Montpellier
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/precharger_objet');

function inc_precharger_ad_service_dist($id_ad_service, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('ad_service', $id_ad_service, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_ad_service_dist($id_ad_service, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('ad_service', $id_ad_service, $id_rubrique, $lier_trad, 'titre');
}


?>