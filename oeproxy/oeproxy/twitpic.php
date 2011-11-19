<?php
/*
 * Plugin oEmebed The Web
 * (c) 2011 Cedric Morin
 * Distribue sous licence GPL
 *
 * http://oembed.com/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');
include_spip('inc/distant');

/**
 * Proxy twitpic :
 * utilise l'api twitpic pour les metadonnees
 * parse l'url pour la photo principale (un peu crappy mais pas trouve mieux)
 *
 * @param string $url
 * @param array $options
 * @param string $html
 * @return array|int
 */
function oeproxy_twitpic_dist($url,$options,$html=null){

	$id = end(explode('/',$url));
	$url_show = "http://api.twitpic.com/2/media/show.json?id=".$id;

	// recuperer la page qui servira pour trouver le media et son titre
	if (is_null($html)){
		$html = recuperer_page($url);
	}

	if (!$html
		OR !$show = recuperer_page($url_show)
	  OR !$show = json_decode($show,true))
		return 404;

	// OK on a les infos sur le media
	$id_long = $show['id'];
	$photo_url = "http://s1-02.twitpicproxy.com/photos/large/".$id_long.".".$show['type'];
	$photo_title = '';
	$width = $show['width'];
	$height = $show['height'];

	// recuperer le media et son titre dans la page
	$img = extraire_balises($html,"img");
	foreach($img as $i){
		if (extraire_attribut($i,'id')=='photo-display'){
			$photo_url = extraire_attribut($i,'src');
			$photo_title = extraire_attribut($i,'alt');
			break;
		}
	}

	// reduire en fonction de la taille maxi demandee
	// pour ne pas generer des trilliards de cache, on prend des dimensions fixes
	$max = 100000;
	if ($options['maxwidth'] AND $options['maxwidth']<$width)
		$max = min($options['maxwidth'],$max);
	if ($options['maxheight'] AND $options['maxheight']<$height)
		$max = min($options['maxheight'],$max);

	$img = "";
	if ($max<100000){
		if ($max<150){
			// on envoi du 75x75
			$photo_url = 'http://twitpic.com/show/mini/'.$id;
			$width = 75;
			$height = 75;
		}
		elseif ($max<300){
			$photo_url = 'http://twitpic.com/show/thumb/'.$id;
			$width = 150;
			$height = 150;
		}
		elseif ($max<600){
			include_spip('inc/filtres_images_mini');
			$img = image_reduire($photo_url,300,300);
		}
		elseif ($max<1200){
			include_spip('inc/filtres_images_mini');
			$img = image_reduire($photo_url,600,600);
		}
		else {
			include_spip('inc/filtres_images_mini');
			$img = image_reduire($photo_url,1200,1200);
		}
	}
	if ($img){
		$photo_url = url_absolue(extraire_attribut($img,'src'));
		list($height,$width) = taille_image($img);
	}


	$result = array(
		// type (required)
    // The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => 'photo',

		// version (required)
    // The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
    // A text title, describing the resource.
		'title' => $photo_title,

		// html (required)
    // The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'url' => $photo_url,

		// width (required)
    // The width in pixels required to display the HTML.
		'width' => $width,

		// height (required)
    // The height in pixels required to display the HTML.
		'height' => $height,

		// author_name (optional)
    // The name of the author/owner of the resource.
		// NIY
		'author_name' => $show['user']['username'],

		// author_url (optional)
    // A URL for the author/owner of the resource.
		// NIY
		'author_url' => "http://twitpic.com/photos/".$show['user']['username'],


		// thumbnail_url (optional)
    // A URL to a thumbnail image representing the resource. The thumbnail must respect any maxwidth and maxheight parameters. If this paramater is present, thumbnail_width and thumbnail_height must also be present.
		'thumbnail_url' => 'http://twitpic.com/show/thumb/'.$id,

		// thumbnail_width (optional)
    // The width of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_height must also be present.
		'thumbnail_width' => '150',

		// thumbnail_height (optional)
    // The height of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_width must also be present.
		'thumbnail_height' => '150',
	);

	return $result;
}