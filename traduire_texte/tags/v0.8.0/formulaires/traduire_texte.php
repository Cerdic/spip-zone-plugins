<?php
/**
 * Formulaire #FORMULAIRE_TRADUIRE_TEXTE
 *
 * @plugin     Traduire Texte
 * @copyright  2018
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Traduire_texte\Formulaires
 */


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
		$erreurs['source'] = _T('traduiretexte:erreur_pas_de_texte');
	}
	if (!$langue_source) {
		$erreurs['langue_source'] = _T('traduiretexte:erreur_pas_de_langue_source');
	}
	if (!$langue_traduction) {
		$erreurs['langue_traduction'] = _T('traduiretexte:erreur_pas_de_langue_cible');
	}
	if ($langue_source and $langue_source == $langue_traduction) {
		$erreurs['langue_traduction'] = _T('traduiretexte:erreur_langues_identiques');
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
			$res['message_erreur'] = _T('traduiretexte:erreur_inconnue_traduire');
			return $res;
		}
	}
	catch (Exception $e) {
		$res['message_erreur'] = _T('traduiretexte:erreur_traduire')
			. '<br />'
			. $e->getMessage();
		return $res;
	}

	$js = _AJAX ? '<script type="text/javascript">if (window.ajaxReload) ajaxReload("tt_traductions");</script>' : '';
	$res['message_ok'] = _T('traduiretexte:succes_traduction') . $js;
	set_request('traduction', $trad);
	return $res;
}