<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');

function action_spipmotion_logo_dist(){
	global $redirect;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_logo_video_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	} else action_infos_video_post($r);
	spip_log("action spipmotion_logo","spipmotion");
}

function action_infos_video_post($r){
	list(, $sign, $id, $type, $id_document, $suite) = $r;
	include_spip('inc/documents');
	$mode= 'vignette';
	spip_log("id_document=$id_document");
	spip_log("type=$type");
	$document = sql_fetsel("docs.id_document,docs.fichier", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin_court = $document['fichier'];
	$chemin = get_spip_doc($chemin_court);
	spip_log("on travail sur $chemin","spipmotion");

	$movie = new ffmpeg_movie($chemin,0);
	$string_temp = "$id-$type-$id_document";
	$query = md5($string_temp);
	$dossier_temp = _DIR_VAR;
	$fichier_temp = "$dossier_temp$query.jpg";
	spip_log("fichier temporaire = $fichier_temp","spipmotion");

	$frame1 = $movie->getFrame(100);
	if($frame1){
		spip_log('frame1 existe');
		$img_temp = $frame1->toGDImage();
		imagejpeg($img_temp, $fichier_temp);
		$img_finale = $fichier_temp;
		$mode = 'vignette';

		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');

		spip_log("on ajoute $img","spipmotion");
		// verifier l'extension du fichier en fonction de son type mime
		list($extension,$arg) = fixer_extension_document($arg);
		$x = $ajouter_documents($img_finale, $img_finale,
				    $type, $id, $mode, $id_document, $actifs);

		imagedestroy($img_temp);
		unlink($img_finale);
	}

	// un invalideur a la hussarde qui doit marcher au moins pour article, breve, rubrique
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_$type/$id'");
	return $x;
}

?>