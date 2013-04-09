<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 * @package SPIP\GetID3\Metadatas
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de récupération des métadonnées sur les fichiers flv (surcharge du plugin medias)
 * appelée à l'insertion en base dans le plugin medias (inc/renseigner_document)
 * 
 * On utilise la version générique metadata/video
 * 
 * @param string $file
 * 		Le chemin du fichier à analyser
 * @return array $metas
 * 		Le tableau comprenant les différentes metas à mettre en base
 */
function metadata_flv($file){
	$recuperer_infos = charger_fonction('video','metadata');
	$metas = $recuperer_infos($file);
	return $metas;
}

?>