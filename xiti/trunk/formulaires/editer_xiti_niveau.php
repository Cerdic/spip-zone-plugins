<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_xiti_niveau_charger_dist($id_xiti_niveau = 'new', $id_rubrique = 0, $retour = '') {
	$valeurs = formulaires_editer_objet_charger('xiti_niveau', $id_xiti_niveau, $id_rubrique, '', $retour, '');
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_xiti_niveau_identifier_dist($id_xiti_niveau = 'new', $id_rubrique = 0, $retour = '') {
	return serialize(array(intval($id_xiti_niveau), ''));
}

function formulaires_editer_xiti_niveau_verifier_dist($id_xiti_niveau = 'new', $id_rubrique = 0, $retour = '') {
	// on ne demande pas le titre obligatoire : il sera rempli a la volee dans editer_article si vide
	$erreurs = formulaires_editer_objet_verifier('xiti_niveau', $id_xiti_niveau);
	foreach (array('titre', 'niveau', 'xtsite') as $obli) {
		if (!_request($obli)) {
			$erreurs[$obli] = _T('info_obligatoire');
		}
	}
	return $erreurs;
}

// https://code.spip.net/@inc_editer_article_dist
function formulaires_editer_xiti_niveau_traiter_dist($id_xiti_niveau = 'new', $id_rubrique = 0, $retour = '') {
	return formulaires_editer_objet_traiter('xiti_niveau', $id_xiti_niveau, $id_rubrique, '', $retour);
}
