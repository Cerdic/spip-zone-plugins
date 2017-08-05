<?php
/**
 * Fonctions utiles au plugin DOMPDF
 *
 * @plugin     DOMPDF
 * @copyright  2014
 * @author     vertige
 * @licence    GNU/GPL
 * @package    SPIP\Dompdf\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction qui va déterminer si c'est un squelette ou du html qui est passé à DOMPDF
 *
 * @param mixed $squelette
 * @access public
 * @return mixed
 */
function dompdf_trouver_html($squelette, $contexte = array()) {
	// Si on a passé un squelette
	if (trouver_fond($squelette)) {
		// On récupère le html du squelette.
		return recuperer_fond($squelette, $contexte);
	} else {
		// Sinon, on déduit que c'est du html directement passé à la fonction
		return $squelette;
	}
}

/**
 * Simplifier la création de cadre avec l'icône PDF dans l'espace privé
 *
 * @param mixed $url_action
 * @param mixed $titre
 * @param mixed $titre_export
 * @access public
 * @return mixed
 */
function dompdf_cadre($url_action, $titre = null, $titre_export = null) {

	include_spip('inc/presentation');

	return
		debut_cadre_relief('', true, '', $titre).
		icone_horizontale(
			$titre_export,
			$url_action,
			'pdf-24.png',
			'export',
			false).
		fin_cadre_relief(true);
}

/**
 * Option pour DOMPDF et SPIP
 *
 * @param obj $dompdf
 * @access public
 * @return obj $dompdf
 */
function spip_dompdf($dompdf) {

	// Utiliser le dossier de cache de SPIP pour stocker les caches de fonts
	include_spip('inc/flock');
	$dompdf_spip_option = array();
	$dompdf_spip_option['fontCache'] = sous_repertoire(_DIR_CACHE, 'dompdf_fontCache');

	$dompdf->set_option('fontCache', $dompdf_spip_option['fontCache']);
	$dompdf->set_option('isHtml5ParserEnabled', true);

	return $dompdf;
}
