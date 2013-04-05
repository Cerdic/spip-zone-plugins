<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2013 - Distribué sous licence GNU/GPL
 * 
 * Fonction de récupération automatique de métadonnées à l'upload de document
 * appelée par le plugin medias
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de récupération des métadonnées sur les fichiers vidéos
 * appelée à l'insertion en base dans le plugin medias (inc/renseigner_document)
 * 
 * @param string $file
 * 		Le chemin du fichier à analyser
 * @return array $metas
 * 		Le tableau comprenant les différentes metas à mettre en base
 */
function metadata_video_dist($file){
	$metas = array();
	$spipmotion_recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
	$metas = $spipmotion_recuperer_infos(false,$file,true,true);
	/**
	 * Si on ne sait pas récupérer la durée de la vidéo
	 * On vérifie que l'on a -encoded dans le nom du fichier
	 * On essaie de trouve le fichier original car c'est une conversion de spipmotion
	 * On récupère si possible la durée de l'original et on relance la récupération du logo
	 */
	if(!$metas['duree'] && preg_match('/-encoded/',$file)){
		$fichier = basename($file);
		$fichier = str_replace('-encoded','',substr($fichier,0, strrpos($fichier, ".")));
		$duree_originale = sql_getfetsel('duree','spip_documents','mode != "conversion" AND fichier LIKE "%'.$fichier.'.%"');
		if($duree_originale > 0){
			$metas['duree'] = $duree_originale;
			$recuperer_logo = charger_fonction("spipmotion_recuperer_logo","inc");
			$id_vignette = $recuperer_logo($id_document,1,$file,$metas,true);
			if(intval($id_vignette))
				$metas['id_vignette'] = $id_vignette;
		}
	}
	return $metas;
}

?>