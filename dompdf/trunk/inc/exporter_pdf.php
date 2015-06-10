<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'export PDF
 *
 * @param mixed $squelette Le squelette ou le html à transformer en PDF
 * @param mixed $contexte L'éventuel contexte'
 * @param string $filename Le nom du fichier
 * @param string $paper Le format du papier (letter, legal, A4, voir $PAPER_SIZES)
 * @param string $orientation (portrait ou landscape)
 * @access public
 */
function inc_exporter_pdf_dist($squelette, $contexte = array(), $filename = 'sortie.pdf', $paper = 'A4', $orientation = 'portrait') {

  // On inclut la configuration DOMPDF
  include_spip('lib/dompdf/dompdf_config.inc');
  include_spip('dompdf_fonctions');

  // On charge DOMPDF
  $dompdf = new DOMPDF();

  $html = dompdf_trouver_html($squelette);

  // On lance DOMPDF pour crée le PDF et le renvoyer au navigateur.
  $dompdf->load_html($html);
  $dompdf->set_paper($paper, $orientation);
  $dompdf->render();

  $dompdf->stream($filename, array("Attachment" => false));
}