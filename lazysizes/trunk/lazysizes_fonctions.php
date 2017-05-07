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