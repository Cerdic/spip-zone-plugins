<?php

/**
 * Préchargement de l'objet editorial pensebete
 *
 * @plugin Pensebetes
 * @copyright  2019
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package SPIP\Pensebetes\Inc
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/precharger_objet');

function inc_precharger_pensebete_dist($id_pensebete, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('pensebete', $id_pensebete, $id_rubrique, $lier_trad, 'nom');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_pensebete_dist($id_pensebete, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('pensebete', $id_pensebete, $id_rubrique, $lier_trad, 'nom');
}

