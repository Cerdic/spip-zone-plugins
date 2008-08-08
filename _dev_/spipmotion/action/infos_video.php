<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');

function action_infos_video_dist(){
	global $redirect;
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(-?\d+)\D(\d+)\D(\w+)/(\w+)$,',$arg,$r)) {
		spip_log("action_infos_video_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	list(, $id_document) = $r;
	$redirect = action_infos_video_sous_action($id_document);
}

function action_infos_video_sous_action($id_document)
{
	include_spip('inc/documents');
	$document = sql_fetsel("docs.id_document, docs.id_vignette,docs.extension,docs.titre,docs.descriptif,docs.fichier,docs.largeur,docs.hauteur,docs.taille,docs.mode,docs.distant, docs.date, L.vu", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin = $document['chemin'];
	$movie = get_spip_doc($chemin);
	
	$height = $movie->getFrameHeight();
	$width = $movie->getFrameWidth();
	$duree = $movie->getDuration();
	
	spip_log("height = $height, width= $width, duree=$duree","spipmotion");
	
	if(_request("iframe") == 'iframe') {
		$redirect = parametre_url(urldecode($iframe_redirect),"show_docs",join(',',$documents_actifs),'&')."&iframe=iframe";
	}
	return $redirect;
}

?>