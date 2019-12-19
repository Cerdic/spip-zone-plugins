<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_youtube_video_dist($data, $url_orig) {

	$html = $data['html'];
	if ($e = extraire_balise($html, 'embed')
		and $src = extraire_attribut($e, 'src')) {
		$data['url'] = $src;
	}
	// On regarde si l'attribut rel est placé dans l'URL
	// pour éviter l'affichage des vidéo liées à la fin de la vidéo
	if (strpos($url_orig, '&rel=0') or strpos($url_orig, '?rel=0')) {
		$data['html'] = str_replace('feature=oembed', 'feature=oembed&rel=0', $data['html']);
	}

	// On regarde si l'attribut wmode=opaque est placé dans l'URL
	// pour forcer l'affichage des vidéo au bon z-index
	if (strpos(strtolower($url_orig), '&wmode=opaque') or strpos(strtolower($url_orig), '?wmode=opaque')) {
		$data['html'] = str_replace('feature=oembed', 'feature=oembed&wmode=Opaque', $data['html']);
	}
	
	// Forcer l'utilisation de la version sans cookie de youtube
	$data['html'] = str_replace('https://www.youtube.com/', 'https://www.youtube-nocookie.com/', $data['html']);

	// recuperer la duree si possible, ruse de sioux
	// http://stackoverflow.com/questions/10066638/get-youtube-information-via-json-for-single-video-not-feed-in-javascript
	if (defined('_OEMBED_VIDEO_DURATION') and _OEMBED_VIDEO_DURATION){
		$v = null;
		$r = parse_url($url_orig);
		if (!empty($r['query'])){
			$v = parametre_url($url_orig, 'v');
		} elseif (!empty($r['path'])) {
			$v = ltrim($r['path'], '/');
		}
		if ($v){
			$oembed_recuperer_url = charger_fonction('oembed_recuperer_url', 'inc');
			if ($infos = $oembed_recuperer_url("https://www.youtube.com/get_video_info?html5=1&video_id=" . $v, '', '')){
				$infos = rawurldecode($infos);
				// et une deuxieme fois car on cherche dur= qui est encode dans une autre variable
				$infos = rawurldecode($infos);
				$infos = explode('dur=', $infos, 2);
				if ($duree = intval(round(floatval(end($infos))))){
					$data['duration'] = $duree;
				}
				unset($infos);
			}
		}
	}

	// un bug chez youtube ?
	$data['html'] = rtrim($data['html'], ')');
	return $data;
}
