<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function inc_generer_pdf_version_objet_dist($objet, $id_objet, $pdf_file) {

	// petit hack de debug
	//if (file_exists($f = _DIR_IMG.'_article_PDF/'.$objet.'_'.$id_objet.'.pdf')){
	//	@rename($f,$pdf_file);
	//}

	// preambule : si un pdf joint avec le credit='pdf_version' est associe a l'objet
	// on l'utilise comme version PDF manuelle
	// (si ce n'est pas un document distant)

	$pdf_join = sql_fetsel('*','spip_documents as D JOIN spip_documents_liens as L ON D.id_document=L.id_document','D.extension='.sql_quote('pdf').' AND D.credits='.sql_quote('pdf_version').' AND L.id_objet='.intval($id_objet).' AND L.objet='.sql_quote($objet),'','D.id_document DESC','0,1');
	if ($pdf_join) {
		include_spip('inc/documents');
		if ($f = get_spip_doc($pdf_join['fichier'])
      AND file_exists($f)){
			@copy($f, $pdf_file);
			return;
		}
	}

	$primary = id_table_objet($objet);
	$fond = 'pdf_version/'.$objet;

	// si on ne sait pas generer le PDF pour cet objet, on ne fait rien
	if (!trouver_fond($fond)){
		return;
	}

	$fond_header = 'pdf_version/objet-header';
	if (trouver_fond("pdf_version/{$objet}-header")){
		$fond_header = "pdf_version/{$objet}-header";
	}
	$fond_footer = 'pdf_version/objet-footer';
	if (trouver_fond("pdf_version/{$objet}-footer")){
		$fond_footer = "pdf_version/{$objet}-footer";
	}

	$contexte = array($primary=>$id_objet);
	$html = recuperer_fond($fond,$contexte);
	$header = recuperer_fond($fond_header,$contexte);
	if (stripos($header,'</body>')===false){
		$header = "<!DOCTYPE html>
		<html>
		<head></head>
		$header
		<body></body>
		</html>";
	}
	$footer = recuperer_fond($fond_footer,$contexte);
	if (stripos($header,'</body>')===false){
		$header = "<!DOCTYPE html>
		<html>
		<head></head>
		$footer
		<body style='margin: 0;padding: 0;'></body>
		</html>";
	}

	$tmp_dir = sous_repertoire(_DIR_TMP,'pdf_version');
	include_spip('inc/acces');
	$tmp_token = substr(md5(creer_uniqid()),0,8);

	$tmp_file = $tmp_dir."$objet-$id_objet-$tmp_token.html";
	$tmp_file_footer = $tmp_dir."$objet-$id_objet-$tmp_token-footer.html";
	$tmp_file_header = $tmp_dir."$objet-$id_objet-$tmp_token-header.html";

	ecrire_fichier($tmp_file, $html);
	ecrire_fichier($tmp_file_footer, $footer);
	ecrire_fichier($tmp_file_header, $header);

	$exec_wkhtmltopdf = charger_fonction('exec_wkhtmltopdf','inc');

	$args = array(
		'--margin-left' => '15mm',
		'--margin-right' => '15mm',
		'--margin-top' => '15mm',
		'--margin-bottom' => '30mm',
		'--header-html' => $tmp_file_header,
		'--header-spacing' => '5',
		'--footer-html' => $tmp_file_footer,
		'--footer-spacing' => '5',
	);
	$exec_wkhtmltopdf($tmp_file, $pdf_file, $args);

	@unlink($tmp_file);
	@unlink($tmp_file_footer);
	@unlink($tmp_file_header);
}