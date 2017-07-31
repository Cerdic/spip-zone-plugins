<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



/*
 * function lazysizes_addons
 * 
 */
function lazysizes_addons() {
	$lazy_addons = array(
		'artdirect' => 'ls.artdirect',
		'aspectratio' => 'ls.aspectratio',
		'attrchange' => 'ls.attrchange',
		'bgset' => 'ls.bgset',
		'custommedia' => 'ls.custommedia',
		'fix-io-sizes' => 'fix-ios-sizes',
		'include' => 'ls.include',
		'noscript' => 'ls.noscript',
		'object-fit' => 'ls.object-fit',
		'optimumx' => 'ls.optimumx',
		'parent-fit' => 'ls.parent-fit',
		'print' => 'ls.print',
		'progressive' => 'ls.progressive',
		'respimg' => 'ls.respimg',
		'rias' => 'ls.rias',
		'static-gecko-picture' => 'ls.static-gecko-picture',
		'twitter' => 'ls.twitter',
		'unload' => 'ls.unload',
		'unveilhooks' => 'ls.unveilhooks',
		'video-embed' => 'ls.video-embed'
	);
	
	return $lazy_addons;
}

function lazysizes_insertion_js(){
	$flux = '';
	include_spip('inc/config');
	$lazy_options = lire_config('lazysizes/options');
	$js_init_options = generer_url_public('lazysizes_config.js') ;
	$flux .= "<script type='text/javascript' src='$js_init_options' ></script>\n";;
	
	// Addons
	$active_addons = lire_config('lazysizes/addons');
	$ls_addons = lazysizes_addons();
	
	if (is_array($active_addons)) {
		foreach($active_addons as $addon => $state){
			if(array_key_exists($addon, $ls_addons)){
				$file = timestamp(find_in_path('javascript/addons/'.$addon.'/'.$ls_addons[$addon].'.js'));
				$flux .= "<script type='text/javascript' src='$file' ></script>\n"; 
			}
		}
	}
			
	$lazysizes = timestamp(find_in_path('javascript/lazysizes.js'));	
	$flux .= "<script type='text/javascript' src='$lazysizes' ></script>\n";

	
	$flux .= "<script type='text/javascript'>window.lazySizes.init();</script>";
	
	return $flux;
}


/*
 * function titrer_document
 *
 * transforme un nom de fichier en chaine lisible
 * tire de la fonction ajouter_document du core
 * https://zone.spip.org/trac/spip-zone/browser/_core_/plugins/medias/action/ajouter_documents.php#L149
 *
 * @param $fichier
 * @return string
 */

function titrer_document($fichier) {
	$titre = substr($fichier, 0, strrpos($fichier, '.')); // Enlever l'extension du nom du fichier
	$titre = preg_replace(',[[:punct:][:space:]]+,u', ' ', $titre);
	return preg_replace(',\.([^.]+)$,', '', $titre);
}