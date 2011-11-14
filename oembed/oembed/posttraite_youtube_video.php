<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_posttraite_youtube_video_dist($data){

	$html = $data['html'];
	if ($e = extraire_balise($html,'embed')
	  AND $src = extraire_attribut($e,'src')){
		$data['url'] = $src;
	}
	
	return $data;
}