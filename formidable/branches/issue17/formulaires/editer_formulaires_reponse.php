<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_editer_formulaires_reponse_charger($id_formulaires_reponse, $retour) {
	include_spip('inc/editer');
	// Est-ce qu'on a le droit ?
	if (!autoriser('modifier', 'formulaires_reponse', $id_formulaires_reponse)) {
		$contexte = array();
		$contexte['editable'] = false;
		$contexte['message_erreur'] = _T('formidable:erreur_autorisation');
	} else {
		$contexte = formulaires_editer_objet_charger('formulaires_reponse', $id_formulaires_reponse, 0, 0, $retour, '');
	}
	return $contexte;
}

function formulaires_editer_formulaires_reponse_verifier($id_formulaires_reponse, $retour) {
	include_spip('inc/editer');
	return formulaires_editer_objet_verifier('formulaires_reponse', $id_formulaires_reponse);
}

function formulaires_editer_formulaires_reponse_traiter($id_formulaires_reponse, $retour) {
	include_spip('inc/editer');
	return formulaires_editer_objet_traiter('formulaires_reponse', $id_formulaires_reponse, 0, 0, $retour, '');
}
