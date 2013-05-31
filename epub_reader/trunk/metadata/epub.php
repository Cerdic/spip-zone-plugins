<?php
/**
 * Plugin Epub reader
 * © 2011-2013 - kent1
 * Licence GPL v3
 * 
 * Fonction de récupération automatique de métadonnées à l'upload de document
 * appelée par le plugin medias
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de récupération des métadonnées sur les fichiers epub
 * appelée à l'insertion en base dans le plugin medias (inc/renseigner_document)
 * 
 * @param string $file 
 * 		Le chemin du fichier à analyser
 * @return array $metas 
 * 		Le tableau comprenant les différentes metas à mettre en base
 */
function metadata_epub_dist($file){
	include_spip('inc/epubreader_creerjs');
	$metas = epubreader_recuperer_metas(false,$file);
	return is_array($metas) ? $metas : array();
}

?>