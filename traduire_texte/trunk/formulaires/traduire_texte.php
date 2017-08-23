<?php

function formulaires_traduire_texte_charger_dist() {
	$valeurs = array(
		'source' => '',
		'langue_source' => '',
		'langue_traduction' => '',
		'traduction' => '',
		'hash' => ''
	);
	if (isset($GLOBALS['meta']['langues_multilingue'])) {
		$langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
		if (in_array($GLOBALS['spip_lang'], $langues)) {
			$valeurs['langue_source'] = $GLOBALS['spip_lang'];
			foreach ($langues as $l) {
				if ($l !== $GLOBALS['spip_lang']) {
					$valeurs['langue_traduction'] = $l;
					break;
				}
			}
		}
	}
	return $valeurs;
}


function formulaires_traduire_texte_verifier_dist() {
	$source = _request('source');
	$langue_source = _request('langue_source');
	$langue_traduction = _request('langue_traduction');
	$erreurs = array();

	if (!trim($source)) {
		$erreurs['source'] = 'Aucun texte à traduire';
	}
	if (!$langue_source) {
		$erreurs['langue_source'] = 'Langue source à définir';
	}
	if (!$langue_traduction) {
		$erreurs['langue_traduction'] = 'Langue de traduction à définir';
	}
	if ($langue_source and $langue_source == $langue_traduction) {
		$erreurs['langue_traduction'] = 'Langue de traduction identique à la langue source';
	}

	return $erreurs;
}


function formulaires_traduire_texte_traiter_dist() {
	$res = array('editable' => true);

	if (_request('supprimer') and $hash = _request('hash')) {
		if (autoriser('supprimer', 'traduction', $hash)) {
			sql_delete('spip_traductions', array('hash=' . sql_quote($hash)));
			set_request('hash', null);
			set_request('source', null);
			$res['message_ok'] = 'La traduction est supprimée';
			return $res;
		} else {
			$res['message_erreur'] = 'Impossible de supprimer la traduction';
			return $res;
		}
	}

	include_spip('inc/traduire_texte');
	$source = _request('source');
	$langue_source = _request('langue_source');
	$langue_traduction = _request('langue_traduction');
	list($trad, $hash) = traduire($source, $langue_traduction, $langue_source, array('raw' => true));

	if (!$trad) {
		$res['message_erreur'] = 'Une erreur est survenue pour calculer la traduction';
		return $res;
	}

	$res['message_ok'] = 'Traduction effectuée';
	set_request('traduction', $trad);
	set_request('hash', $hash);
	return $res;
}