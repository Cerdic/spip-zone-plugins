<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public

function formulaires_noizetier_valider_tri_traiter_dist($retour) {
	$res = array('redirect' => $retour);
	if (_request('cancel')) {
		$res['message_ok'] = _T('noizetier:operation_annulee');
	}

	if (!autoriser('configurer', 'noizetier')) {
		$res['message_erreur'] = _T('noizetier:probleme_droits');
	}

	$ordre = _request('ordre');

	if (_request('save') && $ordre) {
		include_spip('noizetier_fonctions');
		if (noizetier_trier_noisette('', $ordre)) {
			$res['message_ok'] = _T('info_modification_enregistree');
		}
	}

	return $res;
}
