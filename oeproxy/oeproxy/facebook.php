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
 * Proxy tublr :
 * utilise l'api tublr pour le post
 * parse l'url pour la photo principale (un peu crappy mais pas trouve mieux)
 *
 * @param string $url
 * @param array $options
 * @param string $html
 * @return array|int
 */
function oeproxy_facebook_dist($url,$options,$html=null){


/*
   new $.fn.oembed.OEmbedProvider("facebook", "rich", [], "https://graph.facebook.com/$2$3/?callback=?"
    ,{templateRegex:,
      templateData : function(data){ if(!data.id)return false;
          var out = '<div class="facebook1"><div class="facebook2"><a href="http://www.facebook.com/">facebook</a> <a href="'+data.link+'">'+data.name+'</a></div><div class="facebookBody"><div>';
          if(data.picture) out += '<img src="'+data.picture+'" align="left"></div><div>';
          if(data.category) out += 'Category <strong>'+data.category+'</strong><br/>';
          if(data.website) out += 'Website <strong>'+data.website+'</strong><br/>';
          if(data.gender) out += 'Gender <strong>'+data.gender+'</strong><br/>';
          out += '</div></div></div>';
          return out;
        },
      }),
*/

	if (!$url
		OR !preg_match(',^https?://.*facebook.com/(people/[^/]+/(\d+).*|([^/]+$)),i',$url,$m))
		return 404;

	$api = "http://graph.facebook.com/".$m[2].$m[3]."/";
	$string = recuperer_page_cache($api);
	$contexte = json_decode($string,true);

	$contexte['picture'] = "http://graph.facebook.com/".$contexte['id']."/picture";

	$result = array(
		// type (required)
    // The resource type. Valid values, along with value-specific parameters, are described below.
		'type' => 'rich',

		// version (required)
    // The oEmbed version number. This must be 1.0.
		'version' => '1.0',

		// title (optional)
    // A text title, describing the resource.
		'title' => $contexte['name'],

		// html (required)
    // The HTML required to display the resource. The HTML should have no padding or margins. Consumers may wish to load the HTML in an off-domain iframe to avoid XSS vulnerabilities. The markup should be valid XHTML 1.0 Basic.
		'html' => recuperer_fond('modeles/oeproxy/facebook',$contexte),

		// width (required)
    // The width in pixels required to display the HTML.
		'width' => ($options['width']?$options['width']:'300'),

		// height (required)
    // The height in pixels required to display the HTML.
		'height' => ($options['height']?$options['height']:'100'),

		// author_name (optional)
    // The name of the author/owner of the resource.
		'author_name' => $contexte['name'],

		// author_url (optional)
    // A URL for the author/owner of the resource.
		'author_url' => $contexte['link'],


		// thumbnail_url (optional)
    // A URL to a thumbnail image representing the resource. The thumbnail must respect any maxwidth and maxheight parameters. If this paramater is present, thumbnail_width and thumbnail_height must also be present.
		'thumbnail_url' => $contexte['picture'],

		// thumbnail_width (optional)
    // The width of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_height must also be present.
		'thumbnail_width' => 50,

		// thumbnail_height (optional)
    // The height of the optional thumbnail. If this paramater is present, thumbnail_url and thumbnail_width must also be present.
		'thumbnail_height' => 50,

	);

	return $result;
}
