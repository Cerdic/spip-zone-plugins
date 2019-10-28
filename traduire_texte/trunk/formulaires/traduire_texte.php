<?php

function formulaires_traduire_texte_charger_dist() {
	$valeurs = array(
		'source' => '',
		'langue_source' => '',
		'langue_traduction' => '',
		'traduction' => ''
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

	include_spip('inc/traduire_texte');
	$source = _request('source');
	$langue_source = _request('langue_source');
	$langue_traduction = _request('langue_traduction');

	try {
		$trad = traduire($source, $langue_traduction, $langue_source, ['throw' => true]);
		if (!$trad) {
			$res['message_erreur'] = 'Une erreur inconnue est survenue pendant le calcul de la traduction';
			return $res;
		}
	}
	catch (Exception $e) {
		$res['message_erreur'] = 'Une erreur est survenue pour calculer la traduction :'
			. '<br />'
			. $e->getMessage();
		return $res;
	}

	$js = _AJAX ? '<script type="text/javascript">if (window.ajaxReload) ajaxReload("tt_traductions");</script>' : '';
	$res['message_ok'] = 'Traduction effectuée' . $js;
	set_request('traduction', $trad);
	return $res;
}