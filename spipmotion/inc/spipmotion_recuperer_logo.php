<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2011 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
	$document = sql_fetsel("docs.id_orig,docs.id_document,docs.fichier,docs.framecount", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
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
				$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
				if(defined('_DIR_PLUGIN_FONCTIONS_IMAGES')){
					include_spip('fonctions_images_fonctions');
					if($retour>10){
						if($document['id_orig'] == '0'){
							$versions = sql_select('id_document,id_vignette','spip_documents','id_orig='.intval($document['id_document']),'','taille DESC');
						}
						else{
							$versions = sql_select('id_document,id_vignette','spip_documents','id_orig='.intval($document['id_orig']));
						}
						while($version = sql_fetch($versions)){
							spip_log($version,'spipmotion');
							if(intval($version['id_vignette']) > 0){
								$vignette = sql_getfetsel('fichier','spip_documents','id_document='.intval($version['id_vignette']));
								$vignette = get_spip_doc($vignette);
								$x = $ajouter_documents($vignette, $vignette,
							    	$type, $id, $mode, $id_document, $actifs);
							    return $x;
							}
						}
						return false;
					}else if(!filtrer('image_monochrome',$fichier_temp)){
						imagedestroy($img_temp);
						unlink($img_finale);
						$frame = $frame+50;
						$retour++;
					}else if(file_exists($img_finale)){
						$x = $ajouter_documents($img_finale, $img_finale,
							    $type, $id, $mode, $id_document, $actifs);
						imagedestroy($img_temp);
						unlink($img_finale);
						$vignette = true;
					}else{
						return false;
					}
				}else{
					if(file_exists($img_finale)){
						$x = $ajouter_documents($img_finale, $img_finale,
							    $type, $id, $mode, $id_document, $actifs);
						imagedestroy($img_temp);
						unlink($img_finale);
						$vignette = true;
					}
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