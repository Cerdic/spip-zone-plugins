<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'export PDF
 *
 * @param mixed $squelette Le squelette à transformer en PDF
 * @param mixed $contexte L'éventuel contexte'
 * @param string $filename Le nom du fichier
 * @param string $paper Le format du papier (letter, legal, A4, voir $PAPER_SIZES)
 * @param string $orientation (portrait ou landscape)
 * @access public
 */
function inc_exporter_pdf_dist($squelette, $contexte, $filename = 'sortie.pdf', $paper = 'A4', $orientation = 'portrait') {

  // On inclut la configuration DOMPDF
  include_spip('lib/dompdf/dompdf_config.inc');

  // On charge DOMPDF
  $dompdf = new DOMPDF();

  // On récupère le html du squelette.
  $html = recuperer_fond($squelette, $contexte);

  // On lance DOMPDF pour crée le PDF et le renvoyer au navigateur.
  $dompdf->load_html($html);
  $dompdf->set_paper($paper, $orientation);
  $dompdf->render();

  $dompdf->stream($filename, array("Attachment" => false));
}