<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/headers');

/**
 * Acces aux documents joints securise
 * 
 * verifie soit que le demandeur est authentifie
 * soit que le document est publie, c'est-a-dire
 * joint a au moins 1 article, breve ou rubrique publie
 *
 * URLs de la forme :
 * docrestreint.api/id/cle/file
 *
 * @param null $arg
 */
function action_api_docrestreint_dist($arg=null) {

	// Obtenir l'argument '{id_document}/{cle_action}/{chemin_fichier.ext}'
	if (is_null($arg)) {
		$arg = _request('arg');
	}
	$arg = explode("/", $arg);

	// Supprimer et vider les buffers qui posent des problemes de memory limit
	accesrestreint_vider_buffers();

	// manque des arguments : 404
	if (count($arg) < 3) {
		accesrestreint_afficher_erreur_document(404);
		return;
	}

	// Séparer les 3 arguments
	// file ($f) exige pour eviter le scan id_document par id_document
	$id_document = intval(array_shift($arg));
	$cle         = array_shift($arg);
	$f           = implode("/", $arg);

	/**
	 * URL de test de fonctionnement
	 * @see accesrestreint_gerer_htaccess()
	 */
	if ($id_document==0 AND $cle==1 AND $f=="test/.test") {
		echo "OK";
		return;
	}

	include_spip('inc/documents');

	$file = get_spip_doc($f);
	spip_log($file, 'dbg');

	// securite : on refuse tout ../ ou url absolue
	if ((strpos($f, '../') !== false) OR (preg_match(',^\w+://,', $f))) {
		accesrestreint_afficher_erreur_document(403);
		return;
	}

	// inexistant ou illisible : 404
	if (!file_exists($file) OR !is_readable($file)) {
		accesrestreint_afficher_erreur_document(404);
		return;
	}

	$status = $doc = false;
	$dossiers_a_exclure = array('nl');

	// Si c'est dans un sous-dossier explicitement utilisé pour autre chose que les documents
	// (exemple : les newsletters)
	// et bien on ne teste pas l'accès
	if (preg_match('%^(' . join('|', $dossiers_a_exclure) . ')/%', $f)){
		$status = 200;
	}
	else {
		$where = "documents.fichier=".sql_quote(set_spip_doc($file))
		. ($id_document ? " AND documents.id_document=".intval($id_document): '');
		spip_log($where,'dbg');

		$doc = sql_fetsel("documents.id_document, documents.titre, documents.fichier, types.mime_type, types.inclus, documents.extension", "spip_documents AS documents LEFT JOIN spip_types_documents AS types ON documents.extension=types.extension",$where);
		spip_log($doc,'dbg');
		if (!$doc) {
			$status = 404;
		}
		else {

			// ETag pour gerer le status 304
			$ETag = md5($file . ': '. filemtime($file));
			if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
			  AND $_SERVER['HTTP_IF_NONE_MATCH'] == $ETag) {
				http_status(304); // Not modified
				exit;
			}
			else {
				header('ETag: '.$ETag);
			}

			//
			// Verifier les droits de lecture du document

			// en controlant la cle passee en argument si elle est dispo
			// (perf issue : toutes les urls ont en principe cette cle fournie dans la page au moment du calcul de la page)
			if ($cle){
				include_spip('inc/securiser_action');
				if (!verifier_cle_action($doc['id_document'].','.$f, $cle)) {
					spip_log("acces interdit $cle erronee");
					$status = 403;
				}
			}
			// en verifiant le droit explicitement sinon, plus lent !
			else {
				if (!function_exists("autoriser"))
					include_spip("inc/autoriser");
				if (!autoriser('voir', 'document', $doc['id_document'])) {
					$status = 403;
					spip_log("acces interdit $cle erronee");
				}
			}
		}
	}


	switch($status) {

	case 403:
		accesrestreint_afficher_erreur_document(403);
		break;

	case 404:
		accesrestreint_afficher_erreur_document(404);
		break;

	default:
		header("Content-Type: ". $doc['mime_type']);
		// pour les images ne pas passer en attachment
		// sinon, lorsqu'on pointe directement sur leur adresse,
		// le navigateur les downloade au lieu de les afficher

		if ($doc['inclus']=='non') {

			$f = basename($file);
			// ce content-type est necessaire pour eviter des corruptions de zip dans ie6
			header('Content-Type: application/octet-stream');

			header("Content-Disposition: attachment; filename=\"$f\";");
			header("Content-Transfer-Encoding: binary");

			// fix for IE catching or PHP bug issue
			header("Pragma: public");
			header("Expires: 0"); // set expiration time
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

		}
		else {
			header("Expires: 3600"); // set expiration time
		}

		if ($cl = filesize($file))
			header("Content-Length: ". $cl);

		readfile($file);
		break;
	}

}

/**
 * Supprimer et vider les buffers qui posent des problemes de memory limit
 *
 * @link http://www.php.net/manual/en/function.readfile.php#81032
 * 
 * @return void
**/
function accesrestreint_vider_buffers() {
	@ini_set("zlib.output_compression","0"); // pour permettre l'affichage au fur et a mesure
	@ini_set("output_buffering","off");
	@ini_set('implicit_flush', 1);
	@ob_implicit_flush(1);
	$level = ob_get_level();
	while ($level--){
		@ob_end_clean();
	}
}

/**
 * Affiche une page indiquant un document introuvable ou interdit
 *
 * @param string $status
 *     Numero d'erreur (403 ou 404)
 * @return void
**/
function accesrestreint_afficher_erreur_document($status = 404) {

	switch ($status)
	{
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
