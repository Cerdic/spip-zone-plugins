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