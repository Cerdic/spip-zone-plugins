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
 * On utilise la même fonction que pour les vidéos
 * 
 * @param string $file 
 * 		Le chemin du fichier à analyser
 * @return array $metas 
 * 		Le tableau comprenant les différentes metas à mettre en base
 */
function metadata_flv_dist($file){
	$metas = array();
	$videos_metas = charger_fonction('video','metadata');
	$metas = $videos_metas($file);
	return $metas;
}

?>