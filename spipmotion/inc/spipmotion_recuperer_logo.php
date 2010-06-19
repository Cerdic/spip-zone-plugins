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


/**
 * Fonction de récupération de vignette depuis un document video
 * @param int $id_document
 */
function inc_spipmotion_recuperer_logo($id_document){
	if(!intval($id_document) OR !class_exists('ffmpeg_movie')){
		return;
	}

	include_spip('inc/documents');

	$document = sql_fetsel("docs.id_document,docs.fichier", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin_court = $document['fichier'];
	$chemin = get_spip_doc($chemin_court);
	$movie = new ffmpeg_movie($chemin,0);
	if($movie->hasVideo()){
		$frame1 = $movie->getFrame(100);
		if($frame1){
			$string_temp = "$id-$type-$id_document";
			$query = md5($string_temp);
			$dossier_temp = _DIR_VAR;
			$fichier_temp = "$dossier_temp$query.jpg";

			$img_temp = $frame1->toGDImage();
			imagejpeg($img_temp, $fichier_temp);
			$img_finale = $fichier_temp;
			$mode = 'vignette';

			$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
			$x = $ajouter_documents($img_finale, $img_finale,
					    $type, $id, $mode, $id_document, $actifs);

			imagedestroy($img_temp);
			unlink($img_finale);
		}
	}
	return $x;
}
?>