<?php
/**
 * Plugin Lecteur Multimedia V2
 *
 * Auteurs :
 * kent1 et BoOz
 *
 * © 2009/2010 - Distribue sous licence GNU/GPL
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
function action_forcer_telecharger_dist() {
	include_spip('inc/documents');

	// $file exige pour eviter le scan id_document par id_document
	$f = rawurldecode(_request('file'));
	$f = str_replace('IMG/','',$f);
	$file = get_spip_doc($f);
	$arg = rawurldecode(_request('arg'));
	
	if(strpos($f,_DIR_SITE)!== false){
		$f_loc = str_replace(_DIR_SITE,'',$f);
		$file = get_spip_doc($f_loc);
	}

	$status = $dcc = false;
	if (strpos($f,'../') !== false
	OR (preg_match(',^\w+://,', $f) && strpos($f,$GLOBALS['meta']['adresse_site']))) {
		$status = 403;
	}
	else {
		$where = "documents.fichier=".sql_quote(set_spip_doc($file))
		. ($arg ? " AND documents.id_document=".intval($arg): '');

		$doc = sql_fetsel("documents.id_document,documents.distant, types.mime_type, documents.extension", "spip_documents AS documents LEFT JOIN spip_types_documents AS types ON documents.extension=types.extension",$where);

		if (!$doc){
			$status = 404;
		}else if (($doc['distant'] == 'non') && (!file_exists($file)
			OR !is_readable($file))) {
				$status = 404;
		} else if($doc['distant'] == 'non'){

			// ETag pour gerer le status 304
			$ETag = md5($file . ': '. filemtime($file));
			if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
			AND $_SERVER['HTTP_IF_NONE_MATCH'] == $ETag) {
				http_status(304); // Not modified
				exit;
			} else {
				header('ETag: '.$ETag);
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
		header("Content-Type: ".$doc['mime_type'].";\n");
		$f = basename($file);

		header("Content-Transfer-Encoding: binary");

		// fix for IE catching or PHP bug issue
		// header("Pragma: public");
		// header("Expires: 0"); // set expiration time
		// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

		if (($doc['distant'] == 'non') && ($cl = filesize($file))){
			header("Content-Length: $cl;\n");
		}
		
		header("Content-Disposition: attachment; filename=\"$f\";\n\n");

		readfile($file);
	}
}

?>