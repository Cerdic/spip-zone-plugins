<?php

function generer_url_document_flickr($id_document, $statut='') {
	if (intval($id_document) <= 0) 
		return '';
	$row = @spip_fetch_array(spip_query("SELECT fichier,distant	FROM spip_documents WHERE id_document = $id_document"));
	  if ($row) {
		if ($row['distant'] == 'oui') {
		  if(preg_match('#http://static.flickr.com/(.*?)/(.*?)_(.*?)(_[stmbo])\.(jpg|gif|png)#',$row['fichier'],$matches)) {
			$id = $matches[2];
			$secret = $matches[3];
			include_spip('inc/flickr_api');
			$details = flickr_photos_getInfo($id,$secret);
			if($details->urls['photopage']) return $details->urls['photopage'];
			if($details->owner_nsid) 
			  return "http://www.flickr.com/photos/".$details->owner_nsid."/$id/";
			else return $row['fichier'];
		  } else 
			return $row['fichier'];
		} else {
			if (($GLOBALS['meta']["creer_htaccess"]) != 'oui')
				return _DIR_RACINE . ($row['fichier']);
			else 	return generer_url_action('autoriser', "arg=$id_document");
		}
	}

}


if (!_DIR_RESTREINT) {
  if (!function_exists('generer_url_document')) {
	function generer_url_document($id, $stat='')
	{ return generer_url_document_flickr($id, $stat);}
  }
 }

function balise_URL_DOCUMENT($p) {
	$_id_document = '';
	if ($p->param && !$p->param[0][0]){
		$_id_document =  calculer_liste($p->param[0][1],
							$p->descr,
							$p->boucles,
							$p->id_boucle);
	}
	if (!$_id_document)
		$_id_document = champ_sql('id_document',$p);
	$p->code = "generer_url_document_flickr($_id_document)";

	$p->interdire_scripts = false;
	return $p;
}


?>