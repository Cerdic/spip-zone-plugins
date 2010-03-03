<?php

function outils_la_meuh_config_dist() {
	$chemin_js_transform = find_in_path("javascript/jquery-css-transform.js");
	$chemin_js_rotate    = find_in_path("javascript/jquery-animate-css-rotate-scale.js");

	define(_DIR_LIB_SM, _DIR_RACINE . 'lib/soundmanagerv295a-20090717/');
	$libsm = ($a = find_in_path(_DIR_LIB_SM . 'script/soundmanager2.js')) ? $a : '';
	$chanson = find_in_path('medias/meuh.mp3');
		
	add_outil(array(
		'id'          => "la_meuh",
		'nom'         => _T("blagoulames:la_meuh_nom"),
		'description' => _T("blagoulames:la_meuh_description"),
		'categorie'   => _T('blagoulames:categorie'),
		'code:js'     => "
			jQuery.getScript('$chemin_js_transform');
			jQuery.getScript('$chemin_js_rotate');
			",
		'code:jq'     => "
			jQuery('img').mouseover(function(){
				if ('$libsm') {
					if (typeof(soundManager) == 'undefined') {
						jQuery.getScript('$libsm', function(){
							soundManager.debugMode = false;
							soundManager.url = '". _DIR_LIB_SM ."swf/';
							soundManager.onready(function(){
								soundManager.play('meuh', '$chanson');
							});
						});
					} else {
						soundManager.onready(function(){
							soundManager.play('meuh', '$chanson');
						});
					}
				}
				jQuery(this).animate({rotate: '+=180deg'}, 1000);
			});
			",
	));
	
}

?>
