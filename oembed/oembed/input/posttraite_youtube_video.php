<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_input_posttraite_youtube_video_dist($data,$url_orig){

	$html = $data['html'];
	if ($e = extraire_balise($html,'embed')
	  AND $src = extraire_attribut($e,'src')){
		$data['url'] = $src;
	}
	// On regarde si l'attribut rel est placé dans l'URL
	// pour éviter l'affichage des vidéo liées à la fin de la vidéo
	if (strpos($url_orig,"&rel=0") OR strpos($url_orig, "?rel=0"))
		$data['html'] = str_replace('feature=oembed', 'feature=oembed&#038;rel=0', $data['html']);

	// On regarde si l'attribut wmode=opaque est placé dans l'URL
	// pour forcer l'affichage des vidéo au bon z-index
	if (strpos(strtolower($url_orig),"&wmode=opaque") OR strpos(strtolower($url_orig), "?wmode=opaque"))
		$data['html'] = str_replace('feature=oembed', 'feature=oembed&#038;wmode=Opaque', $data['html']);
	
	return $data;
}