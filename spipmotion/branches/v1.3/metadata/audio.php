<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de récupération des métadonnées sur les fichiers audios
 * appelée à l'insertion en base dans le plugin medias (inc/renseigner_document)
 * 
 * @param string $file : le chemin du fichier à analyser
 * @return array $metas : le tableau comprenant les différentes metas à mettre en base
 */
function metadata_audio_dist($file){
	$metas = array();
	$spipmotion_recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
	$metas = $spipmotion_recuperer_infos(false,$file);
	return $metas;
}

?>