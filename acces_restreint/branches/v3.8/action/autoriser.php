<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');

//  acces aux documents joints securise
//  est appelee avec arg comme parametre CGI
//  mais peu aussi etre appele avec le parametre file directement 
//  il verifie soit que le demandeur est authentifie
// soit que le fichier est joint a au moins 1 article, breve ou rubrique publie

// http://doc.spip.org/@action_autoriser_dist
function action_autoriser_dist()
{
	$arg = intval(_request('arg'));

	if (!autoriser('voir','document',$arg)
		OR !($row = sql_fetsel("fichier","spip_documents","id_document=".intval($arg)))
		OR !($file = $row['fichier'])
		OR !(file_exists($file))
		) {
    spip_log("Acces refuse (restreint) au document " . $arg . ': ' . $file);
    redirige_par_entete('./?page=404');
	}
	else  {
		if (!function_exists('mime_content_type')) {
			// http://doc.spip.org/@mime_content_type
			function mime_content_type($f) {preg_match("/\.(\w+)/",$f,$r); return $r[1];}
		}
		$ct = mime_content_type($file);
		$cl = filesize($file);
		$filename = basename($file);
		header("Content-Type: ". $ct);
		header("Content-Disposition: attachment; filename=\"". $filename ."\";");
		if ($dcc) header("Content-Description: " . $dcc);
		if ($cl) header("Content-Length: ". $cl);
		
		header("Content-Transfer-Encoding: binary");
		readfile($file);
  }
}

?>
