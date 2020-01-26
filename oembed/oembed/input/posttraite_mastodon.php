<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/charsets');

function emojify_names($name) {
	$name = ':'.str_replace(' ','_',strtolower($name)).':';
	return $name;
}
function emojify($texte, &$need_emoji) {
	if (
		(strpos($texte, ':')!==false and preg_match(',:\w+:,',$texte))
		or is_utf8($texte)) {
		if (!function_exists('emoji_convert')) {
			include_spip('lib/php-emoji/emoji');
			$GLOBALS['emoji_maps']['names_to_unified'] = array_flip(array_map('emojify_names',$GLOBALS['emoji_maps']['names']));
		}
		// convertir les emoji nommes type :satellite: en utf
		$texte = emoji_convert($texte, 'names_to_unified');
		// convertir les emoji utf en html
		$texte = emoji_unified_to_html($texte);
		$need_emoji = (strpos($texte, 'emoji-sizer') !== false);
	}
	return $texte;
}

function oembed_input_posttraite_mastodon_dist($data) {

	if ($iframe = extraire_balise($data['html'], 'iframe')) {
		$iframe_cor = vider_attribut($iframe, 'height');
		$iframe_cor = vider_attribut($iframe_cor, 'scrolling');
		$iframe_cor = vider_attribut($iframe_cor, 'style');
		$data['html'] = str_replace($data['html'], $iframe, $iframe_cor);

		$oembed_recuperer_url = charger_fonction('oembed_recuperer_url', 'inc');
		$url = parametre_url($data['oembed_url'],'url');
		$src_atom = $url.'.atom';
		if ($xml = $oembed_recuperer_url($src_atom, $src_atom, 'xml')) {

			$need_emoji = false;

			$name = "@".strip_tags(extraire_balise($xml, 'email'));
			$content = strip_tags(extraire_balise($xml, 'content'));
			$content = emojify(filtrer_entites($content), $need_emoji);
			$date = strip_tags(extraire_balise($xml, 'published'));
			$date = date('Y-m-d H:i:s',strtotime($date));

			$screen_name = emojify($data['author_name'], $need_emoji);

			$contexte = array(
				'url' => $data['oembed_url_source'],
				'width' => $data['width'],
				'height' => $data['height'],
				'author_screen_name' => $screen_name,
				'author_name' => $name,
				'author_url' => $data['author_url'],
				'author_thumbnail' => '',
				'author_thumbnail_width' => '',
				'author_thumbnail_height' => '',
				'content' => $content,
				'published' => $date,
				'need_emoji' => ($need_emoji?' ':''),
				'enclosure' => '',
				'enclosure_type' => '',
			);

			$links = extraire_balises($xml, 'link');
			foreach ($links as $link) {
				$rel = extraire_attribut($link, 'rel');
				if ($rel === 'avatar') {
					$contexte['author_thumbnail'] = extraire_attribut($link, 'href');
					$contexte['author_thumbnail_width'] = extraire_attribut($link, 'media:width');
					$contexte['author_thumbnail_height'] = extraire_attribut($link, 'media:height');
				}
				if ($rel === "enclosure" and !$contexte['enclosure']) {
					$contexte['enclosure'] = extraire_attribut($link, 'href');
					$contexte['enclosure_type'] = extraire_attribut($link, 'type');
				}
			}

			$data['html'] = recuperer_fond('modeles/toot', $contexte);
		}

	}
	$data['provider_name'] = 'Mastodon';

	return $data;
}
