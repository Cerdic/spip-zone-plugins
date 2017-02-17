<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('noizetier_fonctions');
include_spip('base/abstract_sql');

function formulaires_lister_noisettes_page_charger_dist($page, $bloc) {

	// Détermination de l'élément concerné : page ou objet
	if (is_array($page)) {
		// On est sur un objet précis connu par son type et son id
		$contexte = $page;
	}
	else {
		// On est sur une page classique ou une composition connu par son identifiant.
		$contexte = array('page' => $page);
	}

	// Ajout du bloc
	$contexte['bloc'] = $bloc;
	
	return $contexte;
}

function formulaires_lister_noisettes_page_traiter_dist($page, $bloc) {
	if (_request('cancel')) {
		return array('message_erreur' => _T('noizetier:operation_annulee'));
	}

	if (!autoriser('configurer', 'noizetier')) {
		return array('message_erreur' => _T('noizetier:probleme_droits'));
	}
	
	$ordre = _request('ordre');
	if (_request('save') && $ordre) {
		if (noizetier_trier_noisette($page, $ordre)) {
			return array('message_ok' => _T('info_modification_enregistree'));
		}
	}

	return array('message_erreur' => _T('noizetier:erreur_mise_a_jour'));
}
