<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
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
function metadata_audio($file){
	$metas = array();
	$recuperer_infos = charger_fonction('getid3_recuperer_infos','inc');
	$metas = $recuperer_infos($file);
	return $metas;
}

?>