<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2010 - Distribue sous licence GNU/GPL
 *
 * Action qui force le téléchargement d'un fichier
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/headers');

/**
 * Forcer le téléchargement de documents
 * verifie soit que le demandeur est authentifie
 * soit que le document est publie, c'est-a-dire
 * joint a au moins 1 article, breve ou rubrique publie
 *
 * Action fortement pompée sur acceder_document du core
 * http://doc.spip.org/@action_acceder_document_dist
 *
 * @return
 */
function action_emballe_medias_forcer_telecharger_dist() {
	include_spip('inc/documents');

	// $file exige pour eviter le scan id_document par id_document
	$f = rawurldecode(_request('file'));
	$file = get_spip_doc($f);
	$arg = rawurldecode(_request('arg'));

	
	if(defined('_DIR_SITE') && strpos($f,_DIR_SITE)!== false){
		$f_loc = str_replace(_DIR_SITE,'',$f);
		$file = get_spip_doc($f_loc);
	}

	$status = $dcc = false;
	if (strpos($f,'../') !== false
	OR preg_match(',^\w+://,', $f)) {
		$status = 403;
	}
	else if (!file_exists($file) OR !is_readable($file)) {
		$status = 404;
	} else {
		$where = "documents.fichier=".sql_quote(set_spip_doc($file))
		. ($arg ? " AND documents.id_document=".intval($arg): '');

		$doc = sql_fetsel("documents.id_document, documents.titre, documents.fichier, types.mime_type, types.inclus, documents.extension", "spip_documents AS documents LEFT JOIN spip_types_documents AS types ON documents.extension=types.extension",$where);
		if (!$doc) {
			$status = 404;
		} else {

			// ETag pour gerer le status 304
			$ETag = md5($file . ': '. filemtime($file));
			if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
			AND $_SERVER['HTTP_IF_NONE_MATCH'] == $ETag) {
				http_status(304); // Not modified
				exit;
			} else {
				header('ETag: '.$ETag);
			}

			//
			// Verifier les droits de lecture du document
			// en controlant la cle passee en argument
			//
			include_spip('inc/securiser_action');
			$cle = _request('cle');
			if (!verifier_cle_action($doc['id_document'].','.$f, $cle)) {
				spip_log("acces interdit $cle erronee");
				$status = 403;
			}
		}
	}

	switch($status) {

	case 403:
		include_spip('inc/minipres');
		echo minipres();
		break;

	case 404:
		http_status(404);
		include_spip('inc/minipres');
		echo minipres(_T('erreur').' 404',
			_T('info_document_indisponible'));
		break;

	default:

		header("Content-Type: ". $doc['mime_type']);
		$f = basename($file);
		// ce content-type est necessaire pour eviter des corruptions de zip dans ie6
		header('Content-Type: application/octet-stream');

		header("Content-Disposition: attachment; filename=\"$f\";");
		header("Content-Transfer-Encoding: binary");

		// fix for IE catching or PHP bug issue
		header("Pragma: public");
		header("Expires: 0"); // set expiration time
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

		if ($cl = filesize($file))
			header("Content-Length: ". $cl);
		readfile($file);
		break;
	}
}

?>