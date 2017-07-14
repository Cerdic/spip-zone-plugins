<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_lister_noisettes_page_charger_dist($page, $bloc) {

	// Si on est en présence d'une page, il faut convertir l'identifiant en tableau.
	// Sinon, on est en présence d'un objet précis connu par son type et son id fourni dans un
	// tableau.
	$valeurs = is_array($page) ? $page : array('page' => $page);

	// Ajout du bloc recevant les noisettes
	$valeurs['bloc'] = $bloc;
	
	return $valeurs;
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
		include_spip('noizetier_fonctions');
		if (noizetier_trier_noisette($page, $ordre)) {
			return array('message_ok' => _T('info_modification_enregistree'));
		}
	}

	return array('message_erreur' => _T('noizetier:erreur_mise_a_jour'));
}
