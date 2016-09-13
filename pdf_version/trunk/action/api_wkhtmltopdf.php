<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/pdf_version');

/**
 * API WKHTMLTOPDF :
 * http://www.example.org/wkhtmltopdf.api/abcdef0123456789?url=http://blog.nursit.net
 *
 * toutes les options de wkhtmltopdf peuvent etre passees en GET ou POST
 * l'ordre des options de l'appel sera respecte
 * le html source peut etre une url fournie par url=
 * ou du HTML brut fourni dans html=
 * dans ce dernier cas on preferera appeler l'API en POST pour ne pas etre limite par la longueur du GET
 *
 */
function action_api_wkhtmltopdf_dist(){

	// verifier que la fonction API wkhtmltopdf a bien ete activee
	if (lire_config('pdf_version/methode','exec')!=='exec'
	  or !lire_config('pdf_version/api_wkhtmltopdf_actif','1')) {
		pdf_version_afficher_erreur_document(403);
		exit;
	}
	// recuperer le token et le verifier
	$arg = _request('arg');
	$ok = false;
	$cles = lire_config('pdf_version/api_keys','');
	if ($cles) {
		$cles = explode("\n",$cles);
		$cles = array_filter($cles);
		foreach($cles as $cle){
			$cle = explode(';',$cle);
			$cle = trim(reset($cle));
			if ($arg === $cle) {
				$ok = true;
				break;
			}
		}
	}
	if (!$ok) {
		pdf_version_afficher_erreur_document(403);
		exit;
	}


	$args = array();

	// recuperer les arguments : on utilise $_REQUEST car l'API peut etre appelee en POST ou GET
	foreach($_REQUEST as $k=>$v){
		if (strncmp($k,'--',2)==0
			and preg_match(',^--[\w-]+$,i',$k)){
			$args[$k] = $v;
		}
	}

	$tmpdir = sous_repertoire(_DIR_TMP,'wkhtmltopdf');
	include_spip('inc/acces');

	$tmpfile = $tmpdir . substr(md5(creer_uniqid()),0,16);
	$pdf_file = $tmpfile . '.pdf';
	$tmpfile_header = $tmpfile_footer = '';

	// les header/footer HTML dans un fichier temporaire
	if (isset($args['--header-html'])){
		$tmpfile_header = $tmpfile . '-header.html';
		ecrire_fichier($tmpfile_header,$args['--header-html']);
		$args['--header-html'] = $tmpfile_header;
	}

	if (isset($args['--footer-html'])){
		$tmpfile_footer = $tmpfile . '-footer.html';
		ecrire_fichier($tmpfile_footer,$args['--footer-html']);
		$args['--footer-html'] = $tmpfile_footer;
	}
	$tmpfile .= '.html';

	// le HTML source principal : une URL ou du HTML
	$html_file = '';
	if (_request('url')){
		$html_file = _request('url');
	}
	elseif(_request('html')) {
		ecrire_fichier($tmpfile, _request('html'));
		$html_file = $tmpfile;
	}

	if (!$html_file) {
		$exec_wkhtmltopdf = charger_fonction('exec_wkhtmltopdf','inc');
		$exec_wkhtmltopdf($html_file, $pdf_file, $args);
	}


	if (file_exists($pdf_file)) {

		// delivrer le PDF
		$embed = true;

		// toujours envoyer un content type
		header("Content-Type: application/pdf");
		// pour les images ne pas passer en attachment
		// sinon, lorsqu'on pointe directement sur leur adresse,
		// le navigateur les downloade au lieu de les afficher
		if (!$embed) {
			$f = basename($pdf_file);
			header("Content-Disposition: attachment; filename=\"$f\";");
			header("Content-Transfer-Encoding: binary");

			// fix for IE catching or PHP bug issue
			header("Pragma: public");
			header("Expires: 0"); // set expiration time
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		} else {
			header("Expires: 3600"); // set expiration time
		}

		if ($size = filesize($pdf_file)) {
			header("Content-Length: ". $size);
		}

		readfile($pdf_file);
		@unlink($pdf_file);

	}
	else {
		pdf_version_afficher_erreur_document(404);
	}

	if ($tmpfile and file_exists($tmpfile)) {
		@unlink($tmpfile);
	}
	if ($tmpfile_header and file_exists($tmpfile_header)) {
		@unlink($tmpfile_header);
	}
	if ($tmpfile_footer and file_exists($tmpfile_footer)) {
		@unlink($tmpfile_footer);
	}

}
