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
 * @param int $id_document L'id numérique du document
 * @param int $frame la frame à capturer
 */
function inc_spipmotion_recuperer_logo($id_document,$frame=50){
	spip_log("SPIPMOTION : recuperation d un logo du document $id_document","spipmotion");
	if(!intval($id_document) OR !class_exists('ffmpeg_movie')){
		return;
	}

	include_spip('inc/documents');
	include_spip('inc/filtres_images_mini');
	$retour = 0;
	$document = sql_fetsel("docs.id_document,docs.fichier,docs.framecount", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin_court = $document['fichier'];
	$chemin = get_spip_doc($chemin_court);
	$movie = new ffmpeg_movie($chemin,0);
	if($movie->hasVideo()){
		$vignette = false;
		while(!$vignette && ($frame < $document['framecount'])){
			$frame1 = $movie->getFrame($frame);
			if($frame1){
				$string_temp = "$id-$type-$id_document";
				$query = md5($string_temp);
				$dossier_temp = _DIR_VAR;
				$fichier_temp = "$dossier_temp$query.jpg";
	
				$img_temp = $frame1->toGDImage();
				imagejpeg($img_temp, $fichier_temp);
				$img_finale = $fichier_temp;
				$mode = 'vignette';
				
				if(defined('_DIR_PLUGIN_FONCTIONS_IMAGES')){
					include_spip('fonctions_images_fonctions');
					if(!filtrer('image_monochrome',$fichier_temp)){
						imagedestroy($img_temp);
						unlink($img_finale);
						$frame = $frame+50;
						$retour++;
					}else{
						$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
						$x = $ajouter_documents($img_finale, $img_finale,
								    $type, $id, $mode, $id_document, $actifs);
						imagedestroy($img_temp);
						unlink($img_finale);
						$vignette = true;
					}
				}else{
					$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
					$x = $ajouter_documents($img_finale, $img_finale,
							    $type, $id, $mode, $id_document, $actifs);
					imagedestroy($img_temp);
					unlink($img_finale);
					$vignette = true;
				}
			}
			else{
				return false;
			}
		}
	}
	return $x;
}
?>