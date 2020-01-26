<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_office_national_du_film_du_canada_video_dist($data) {

	$data['html'] = htmlspecialchars_decode(preg_replace(',&lt;p.*/p&gt;,', '', $data['html']));

	return $data;
}
