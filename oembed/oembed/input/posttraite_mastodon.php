<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function oembed_input_posttraite_mastodon_dist($data) {

	if ($iframe = extraire_balise($data['html'], 'iframe')) {
		$iframe_cor = vider_attribut($iframe, 'height');
		$iframe_cor = vider_attribut($iframe_cor, 'scrolling');
		$iframe_cor = vider_attribut($iframe_cor, 'style');

		$data['html'] = str_replace($data['html'], $iframe, $iframe_cor);
		$src = extraire_attribut($iframe_cor, 'src');

		// si on sait mieux faire en faisant un extract du contenu de l'iframe on y go, car les iframe c'est moche
		if ($html = recuperer_page($src)) {
			$texte = $date_link = "";
			if ($p = strpos($html, 'status__content')
			  and $p1 = strpos($html, '<p', $p)
			  and $p2 = strpos($html, '</p>', $p1)
			) {
				$texte = substr($html, $p1, $p2 - $p1 +4);
			}
			if ($p = strpos($html, 'dt-published')
			  and $p1 = strpos($html, '<a', $p)
			  and $p2 = strpos($html, '</a>', $p1)
			) {
				$date_link = substr($html, $p1, $p2 - $p1 +4);
			}

			if ($texte and $date_link) {
				$html = "<blockquote class=\"twitter-tweet\">".$texte."\n&mdash; ".$data['author_name'];
				$author_account = explode('/users/', $data['author_url']);
				$a = "@".end($author_account)."@".trim(protocole_implicite(reset($author_account)),'/');
				$html .= " ($a) $date_link\n</blockquote>";

				$data['html'] = $html;
				$data['height'] = null;
			}
		}


	}
	$data['provider_name'] = 'Mastodon';

	return $data;
}
