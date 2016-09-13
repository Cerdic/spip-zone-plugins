<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/pdf_version');

function action_api_pdf_version_dist($arg=null, $embed=true) {

	if (is_null($arg)) {
		$arg = _request('arg');
	}

	// verifier que arg est bien forme
	if (!$arg) {
		pdf_version_afficher_erreur_document(403);
		exit;
	}

	$arg = basename($arg,'.pdf');
	list($objet,$id_objet) = explode('-',$arg);
	if (preg_match(',\W,', $objet)
	  or !is_numeric($id_objet)
	  or !$id_objet = intval($id_objet)){
		pdf_version_afficher_erreur_document(403);
		exit;
	}

	include_spip('inc/autoriser');

	// verifier le droit de voir cette version PDF
	if (!autoriser('voirpdfversion',$objet,$id_objet)) {
		pdf_version_afficher_erreur_document(403);
		exit;
	}

	// si le fichier n'existe pas, le generer a la volee
	// via la fonction adhoc ou generique
	$pdf_file = _DIR_PDF_VERSION . $arg . '.pdf';
	if (!file_exists($pdf_file) OR _request('var_mode')=='recalcul') {

		generer_pdf_version_objet($objet, $id_objet, $pdf_file);
		
		// securite
		if (!file_exists($pdf_file)) {
			spip_log("Echec generation PDF $objet $id_objet","pdf_version");
			pdf_version_afficher_erreur_document(404);
			exit;
		}
	}

	// delivrer le PDF

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
}

