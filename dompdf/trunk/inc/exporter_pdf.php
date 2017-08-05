<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

use Dompdf\Dompdf;

/**
 * Fonction d'export PDF
 *
 * @param mixed $squelette Le squelette ou le html à transformer en PDF
 * @param mixed $contexte L'éventuel contexte'
 * @param string $filename Le nom du fichier
 * @param string $paper Le format du papier (letter, legal, A4, voir
 * $PAPER_SIZES)
 * @param string $orientation (portrait ou landscape)
 * @access public
 */
function inc_exporter_pdf_dist($squelette, $contexte = array(), $filename = 'sortie.pdf', $paper = 'A4', $orientation = 'portrait') {

	// Charger DOMPDF
	include_spip('lib/dompdf/autoload.inc');
	include_spip('dompdf_fonctions');

	// On charge DOMPDF
	$dompdf = new DOMPDF();

	$html = dompdf_trouver_html($squelette, $contexte);

	// On lance DOMPDF pour créer le PDF et le renvoyer au navigateur.
	$dompdf->load_html($html);
	$dompdf->set_paper($paper, $orientation);
	// Autoriser les images absolues «distantes» (http://mon_site.tld/IMG/jpg/truc.jpg)
	// (Dompdf considère distantes les urls même si c'est le même domaine !)
	$dompdf->set_option('enable_remote', true);
	$dompdf = spip_dompdf($dompdf);
	$dompdf->render();

	$dompdf->stream($filename, array('Attachment' => false));
}
