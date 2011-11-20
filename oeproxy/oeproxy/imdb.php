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
 * Proxy imdb :
 * utilise l'api imdb pour les infos sur un film
 *
 * @param string $url
 * @param array $options
 * @param string $html
 * @return array|int
 */
function oeproxy_imdb_dist($url,$options,$html=null){


	/*
	new $.fn.oembed.OEmbedProvider("imdb", "rich", ["imdb.com/title/.+"], "http://www.imdbapi.com/?i=$1&callback=?",
				{templateRegex:/.*\/title\/([^\/]+).* /,
				templateData : function(data){if(!data.Title)return false;
						return '<div id="content"><h3><a class="nav-link" href="http://imdb.com/title/'+data.ID+'/">'+data.Title+'</a> ('+data.Year+')</h3><p>Starring: '+data.Actors+'</p><div id="photo-wrap" style="margin: auto;width:600px;height:450px;"><img class="photo" id="photo-display" src="'+data.Poster+'" alt="'+data.Title+'"></div> <div id="view-photo-caption">'+data.Plot+'</div></div>';
					},
				}),
	*/

	if (!$url
		OR !preg_match(',^http://(?:www.)?imdb.com/title/([^/]+),i',$url,$m))
		return 404;

	$api = "http://www.imdbapi.com/?i=".$m[1];
	$string = recuperer_page_cache($api);
	$contexte = json_decode($string,true);
	/*
	 * {"Title":"The Fairy",
	 * "Year":"2011",
	 * "Rated":"7",
	 * "Released":"14 Sep 2011",
	 * "Genre":"Comedy, Drama",
	 * "Director":"Dominique Abel, Fiona Gordon",
	 * "Writer":"Dominique Abel, Fiona Gordon",
	 * "Actors":"Dominique Abel, Fiona Gordon, Philippe Martz, Bruno Romy",
	 * "Plot":"Dom works the night shift in a small hotel near the industrial sea port of Le Havre. One night, a woman arrives with no luggage and no shoes...",
	 * "Poster":"N/A",
	 * "Runtime":"1 hr 33 mins",
	 * "Rating":"7.0",
	 * "Votes":"69",
	 * "ID":"tt1922645",
	 * "Response":"True"}
	 */

	$contexte['Url'] = $url;
	if ($contexte['Poster']=="N/A")
		$contexte['Poster']="";

	$result = array(
		// type (required)
    // The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => 'rich',

		// version (required)
    // The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
    // A text title, describing the resource.
		'title' => $contexte['Title'],

		// html (required)
    // The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'html' => recuperer_fond('modeles/oeproxy/imdb',$contexte),

		// width (required)
    // The width in pixels required to display the HTML.
		'width' => ($options['width']?$options['width']:'300'),

		// height (required)
    // The height in pixels required to display the HTML.
		'height' => ($options['height']?$options['height']:'100'),

		// author_name (optional)
    // The name of the author/owner of the resource.
		//'author_name' => $contexte['name'],

		// author_url (optional)
    // A URL for the author/owner of the resource.
		//'author_url' => $contexte['link'],


		// thumbnail_url (optional)
    // A URL to a thumbnail image representing the resource. The thumbnail must respect any maxwidth and maxheight parameters. If this paramater is present, thumbnail_width and thumbnail_height must also be present.
		//'thumbnail_url' => $contexte['picture'],

		// thumbnail_width (optional)
    // The width of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_height must also be present.
		//'thumbnail_width' => 50,

		// thumbnail_height (optional)
    // The height of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_width must also be present.
		//'thumbnail_height' => 50,

	);

	return $result;
}