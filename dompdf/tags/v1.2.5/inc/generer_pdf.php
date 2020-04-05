<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

use Dompdf\Dompdf;

/**
 * Fonction de génération de PDF. Contrairement à exporter_pdf, generer_pdf
 * renvoie le PDF à PHP ce qui permet de l'utiliser à d'autre fin.
 *
 * @param mixed $squelette Le squelette à transformer en PDF
 * @param mixed $contexte L'éventuel contexte'
 * @param string $filename Le nom du fichier
 * @param string $paper Le format du papier (letter, legal, A4, voir
 * $PAPER_SIZES)
 * @param string $orientation (portrait ou landscape)
 * @access public
 */
function inc_generer_pdf_dist($squelette, $contexte = array(), $filename = 'sortie.pdf', $paper = 'A4', $orientation = 'portrait') {

	// On inclut la configuration DOMPDF
	include_spip('lib/dompdf/autoload.inc');
	include_spip('dompdf_fonctions');

	// On charge DOMPDF
	$dompdf = new DOMPDF();

	// On récupère le html du squelette.
	$html = dompdf_trouver_html($squelette, $contexte);

	// On lance DOMPDF pour crée le PDF et le renvoyer au navigateur.
	$dompdf->load_html($html);
	$dompdf->set_paper($paper, $orientation);
	$dompdf = spip_dompdf($dompdf);
	$dompdf->render();

	return $dompdf->output();
}
