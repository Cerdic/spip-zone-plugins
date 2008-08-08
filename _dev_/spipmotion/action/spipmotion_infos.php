<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');

function action_spipmotion_infos_dist(){
	global $redirect;
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_infos_video_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	else{
		action_spipmotion_infos_post($r);
		spip_log("action spipmotion_infos","spipmotion");
	}
}

function action_spipmotion_infos_post($r){
	list(, $sign, $id_objet, $objet, $id_document, $suite) = $r;
	include_spip('inc/documents');
	$document = sql_fetsel("docs.id_document,docs.extension,docs.fichier,docs.taille,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin = $document['fichier'];
	$movie = get_spip_doc($chemin);
	spip_log("on travail sur $movie","spipmotion");	
	
	$movie = @new ffmpeg_movie($movie, 0);
	$height = $movie->getFrameHeight();
	$width = $movie->getFrameWidth();
	$duree = $movie->getDuration();
	
	sql_updateq('spip_documents',array('hauteur'=> $height, 'largeur'=>$width),'id_document='.sql_quote($id_document));
	if(_request("iframe") == 'iframe') {
		$redirect = parametre_url(urldecode($iframe_redirect),"show_docs",join(',',$documents_actifs),'&')."&iframe=iframe";
	}
	return $redirect;
}

?>