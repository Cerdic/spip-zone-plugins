<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_facebook_video_dist($data, $url_orig) {
	$id_video = preg_replace(',http.*facebook.com\/.*videos\/,', '', $data['url']);
	$id_video = rtrim($id_video, '/');
	if (intval($id_video) > 0) {
		include_spip('inc/distant');
		/**
		 * Récupérer les données opengraph de la video
		 * @var string $data2
		 */
		$data2 = recuperer_page('https://graph.facebook.com/'.$id_video.'/');
		if ($data2) {
			$data2 = json_decode($data2, true);
			if (is_array($data2)) {
				if (isset($data2['picture'])) {
					$data['thumbnail_url'] = $data2['picture'];
				}
				if (isset($data2['embed_html'])) {
					$embed = $data2['embed_html'];
					$data['source'] = $data['url'];
					$data['html'] = $data2['embed_html'];
					unset($data['url']);
				}
				if (isset($data2['description'])) {
					$data['title'] = $data2['description'];
				}
			}
		}
	}
	return $data;
}
