<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function oembed_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_oembed_providers')),
		array('sql_alter',"TABLE spip_documents ADD oembed text NOT NULL DEFAULT ''"),
		array('oembed_update_new_providers'),
	);

	$maj['0.2'] = array(
		array('sql_alter',"TABLE spip_documents ADD oembed text NOT NULL DEFAULT ''"),
	);

	// toujours un update des nouveaux providers sur la version cible
	$maj[$version_cible] = array(
		array('oembed_update_new_providers'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function oembed_update_new_providers(){
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
		'http://*.wordpress.com/*'       =>   'http://public-api.wordpress.com/oembed/1.0/',
		'http://my.opera.com/*'           => 'http://my.opera.com/service/oembed',


		#'https://twitter.com/*/status/*' =>   '?action=oeproxy_twitter',
		#'http://twitter.com/*/status/*' =>   '?action=oeproxy_twitter',
		#'https://twitter.com/*/statuses/*' =>   '?action=oeproxy_twitter',
		#'http://twitter.com/*/statuses/*' =>   '?action=oeproxy_twitter',

		#'http://yfrog.ru|com.tr|it|fr|co.il|co.uk|com.pl|pl|eu|us)/*'         =>   'http://yfrog.com/api/oembed',
		#'https://gist.github.com/*' => 'http://github.com/api/oembed?format=json'
	);

	foreach($providers as $scheme=>$endpoint){
		if (!sql_countsel('spip_oembed_providers','scheme='.sql_quote($scheme)))
			sql_insertq('spip_oembed_providers',array('scheme'=>$scheme,'endpoint'=>$endpoint));
	}
}

function oembed_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_oembed_providers");
	effacer_meta($nom_meta_base_version);
}

?>