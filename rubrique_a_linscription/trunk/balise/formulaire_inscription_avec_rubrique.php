<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');
include_spip('inc/filtres');

// la création d'une rubrique dépend du formulaire de configuration :
// * elle peut se faire sur #FORMULAIRE_INSCRIPTION
// * mais on peut souhaiter qu'elle ne se fasse que sur #FORMULAIRE_INSCRIPTION_AVEC_RUBRIQUE
function balise_FORMULAIRE_INSCRIPTION_AVEC_RUBRIQUE ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION_AVEC_RUBRIQUE', array());
}
function balise_FORMULAIRE_INSCRIPTION_AVEC_RUBRIQUE_stat($args, $context_compil) {
	include_spip("balise/formulaire_inscription");
	return balise_FORMULAIRE_INSCRIPTION_stat($args, $context_compil);
}

?>