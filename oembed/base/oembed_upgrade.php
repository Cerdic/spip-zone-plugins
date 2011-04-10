<?php
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
			$providers = array(
				'http://*.youtube.com/watch*'        			=> 'http://www.youtube.com/oembed',
				'http://youtu.be/*'								=> 'http://www.youtube.com/oembed',
				'http://blip.tv/file/*'							=> 'http://blip.tv/oembed/',
				'http://*.vimeo.com/*'							=> 'http://www.vimeo.com/api/oembed.json',
				'http://vimeo.com/*'							=> 'http://www.vimeo.com/api/oembed.json',
				'http://*.dailymotion.com/*'					=> 'http://www.dailymotion.com/api/oembed',
				'http://*.flickr.com/*'							=> 'http://www.flickr.com/services/oembed/',
				'http://flickr.com/*'							=> 'http://www.flickr.com/services/oembed/'
			);
			foreach ($providers as $scheme => $endpoint) {
				sql_insertq('spip_oembed_providers',array('scheme'=>$scheme,'endpoint'=>$endpoint));
			}
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
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