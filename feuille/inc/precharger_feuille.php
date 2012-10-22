<?php
/**
 * Plugin Feuille
 * (c) 2012 chankalan
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/precharger_objet');

function inc_precharger_feuille_dist($id_feuille, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('feuille', $id_feuille, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_feuille_dist($id_feuille, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('feuille', $id_feuille, $id_rubrique, $lier_trad, 'titre');
}


?>