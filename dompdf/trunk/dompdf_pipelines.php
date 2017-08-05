<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter le cadre DOMPDF dans formidable
 *
 * @access public
 */
function dompdf_affiche_gauche($flux) {

	if ($flux['args']['exec'] == 'formulaires_reponse') {
		$url_action = generer_action_auteur('exporter_reponse_formidable', intval($flux['args']['id_formulaires_reponse']));
		$flux['data'] .= dompdf_cadre($url_action, _T('dompdf:pdf'), _T('dompdf:export'));
	}

	if ($flux['args']['exec'] == 'formulaires_reponses') {
		$url_action = generer_action_auteur('exporter_reponses_formidable', intval($flux['args']['id_formulaire']));
		$flux['data'] .= dompdf_cadre($url_action, _T('dompdf:pdf'), _T('dompdf:export_reponses'));
	}

	return $flux;
}
