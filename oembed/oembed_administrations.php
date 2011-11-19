<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

function oembed_upgrade($nom_meta_base_version,$version_cible){
	$current_version = "0.0";
	if (	(!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/oembed');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// voir
			// http://oembed.com/
			// http://code.google.com/p/oohembed/source/browse/app/provider/endpoints.json
			// https://github.com/starfishmod/jquery-oembed-all/blob/master/jquery.oembed.js
			$providers = array(
				'http://*.youtube.com/watch*'    =>   'http://www.youtube.com/oembed',
				'http://youtu.be/*'              =>   'http://www.youtube.com/oembed',
				'http://blip.tv/file/*'          =>   'http://blip.tv/oembed/',
				'http://*.vimeo.com/*'           =>   'http://www.vimeo.com/api/oembed.json',
				'http://vimeo.com/*'             =>   'http://www.vimeo.com/api/oembed.json',
				'http://*.dailymotion.com/*'     =>   'http://www.dailymotion.com/api/oembed',
				'http://*.flickr.com/*'          =>   'http://www.flickr.com/services/oembed/',
				'http://flickr.com/*'            =>   'http://www.flickr.com/services/oembed/',
				'http://soundcloud.com/*'        =>   'http://soundcloud.com/oembed',
			  'http://*.soundcloud.com/*'      =>   'http://soundcloud.com/oembed',
			  'http://slideshare.net/*/*'      =>   'http://www.slideshare.net/api/oembed/2',
				'http://www.slideshare.net/*/*'  =>   'http://www.slideshare.net/api/oembed/2',
				'http://yfrog.com/*'         =>   'http://yfrog.com/api/oembed',
				'http://yfrog.*/*'         =>   'http://yfrog.com/api/oembed',
				'http://instagr.am/*'            =>   'http://api.instagram.com/oembed',
				'http://instagram.com/*'         =>   'http://api.instagram.com/oembed',
				'http://rd.io/*'                 =>   'http://www.rdio.com/api/oembed/',
				'http://rdio.com/*'              =>   'http://www.rdio.com/api/oembed/',
				'http://huffduffer.com/*/*'      =>   'http://huffduffer.com/oembed',
				'http://nfb.ca/film/*'           =>   'http://www.nfb.ca/remote/services/oembed/',
				'http://dotsub.com/view/*'       =>   'http://dotsub.com/services/oembed',
				'http://clikthrough.com/theater/video/*'=>'http://clikthrough.com/services/oembed',
				'http://kinomap.com/*'           =>   'http://www.kinomap.com/oembed',
				'http://photobucket.com/albums/*'=>   'http://photobucket.com/oembed/',
				'http://photobucket.com/groups/*'=>   'http://photobucket.com/oembed/',
				'http://smugmug.com/*/*'         =>   'http://api.smugmug.com/services/oembed/',
				'http://meetup.com/*'            =>   'http://api.meetup.com/oembed',
				'http://meetup.ps/*'             =>   'http://api.meetup.com/oembed',


				#'https://twitter.com/*/status/*' =>   '?action=oeproxy_twitter',
				#'http://twitter.com/*/status/*' =>   '?action=oeproxy_twitter',
				#'https://twitter.com/*/statuses/*' =>   '?action=oeproxy_twitter',
				#'http://twitter.com/*/statuses/*' =>   '?action=oeproxy_twitter',

				#'http://yfrog.ru|com.tr|it|fr|co.il|co.uk|com.pl|pl|eu|us)/*'         =>   'http://yfrog.com/api/oembed',
				#'https://gist.github.com/*' => 'http://github.com/api/oembed?format=json'
			);
			foreach ($providers as $scheme => $endpoint) {
				sql_insertq('spip_oembed_providers',array('scheme'=>$scheme,'endpoint'=>$endpoint));
			}
			ecrire_meta($nom_meta_base_version,$current_version=0.1,'non');
		}
		if (version_compare($current_version,"0.2","<")){
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_documents ADD oembed text NOT NULL DEFAULT ''");
			ecrire_meta($nom_meta_base_version,$current_version=0.2,'non');
		}
	}
}


function oembed_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_oembed_providers");
	effacer_meta($nom_meta_base_version);
}

?>