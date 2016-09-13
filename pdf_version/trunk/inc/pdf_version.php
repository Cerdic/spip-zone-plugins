<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

define('_DIR_PDF_VERSION', _DIR_IMG . 'pdf_version/');

/**
 * Fonction facilitatrice pour generer le PDF d'un objet
 * 
 * @param $objet
 * @param $id_objet
 * @param $pdf_file
 * @return mixed
 */
function generer_pdf_version_objet($objet, $id_objet, $pdf_file){
	if (!$generer_pdf_version_objet = charger_fonction('generer_pdf_version_'.$objet,'inc',true)){
		$generer_pdf_version_objet = charger_fonction('generer_pdf_version_objet','inc');
	}
	
	return $generer_pdf_version_objet($objet, $id_objet, $pdf_file);
}


/**
 * on essaye de poser un htaccess rewrite global sur IMG/
 * si fonctionne on gardera des ulrs de document permanente
 * si ne fonctionne pas on se rabat sur creer_htaccess du core
 * qui pose un deny sur chaque sous repertoire de IMG/
 *
 * http://doc.spip.org/@gerer_htaccess
 *
 * @param bool $active
 * @return bool
 */
function pdf_version_gerer_htaccess($active = true) {

	if (!$active){
		spip_unlink(_DIR_PDF_VERSION . _ACCESS_FILE_NAME);
		return false;
	}
	else  {
		$rewrite = <<<rewrite
RewriteEngine On
RewriteRule ^(.*)\.pdf$     ../../spip.php?action=api_pdf_version&arg=$1 [QSA,L,skip=100]
rewrite;

		ecrire_fichier(_DIR_PDF_VERSION . _ACCESS_FILE_NAME,$rewrite);

		return true;
	}
}



/**
 * Affiche une page indiquant un document introuvable ou interdit
 *
 * @param string $status
 *     Numero d'erreur (403 ou 404)
 * @return void
**/
function pdf_version_afficher_erreur_document($status = 404) {

	switch ($status)
	{
		case 304:
			include_spip("inc/headers");
			// not modified : sortir de suite !
			http_status(304);
			exit;

		case 403:
			include_spip('inc/minipres');
			echo minipres("","","",true);
			break;

		case 404:
			http_status(404);
			include_spip('inc/minipres');
			echo minipres(_T('erreur') . ' 404', _T('medias:info_document_indisponible'), "", true);
			break;
	}
}
