<?php

function outils_slam_config_dist() {

	define(_DIR_LIB_SM, _DIR_RACINE . 'lib/soundmanagerv295a-20090717/');
	$libsm = ($a = find_in_path(_DIR_LIB_SM . 'script/soundmanager2.js')) ? $a : '';
	$chanson = find_in_path('medias/http.mp3');

	add_outil(array(
		'id'          => "slam",
		'nom'         => _T("blagoulames:slam_nom"),
		'description' => _T("blagoulames:slam_description"),
		'categorie'   => 'blagoulames',
		'code:js'     => "
			jQuery(document).ready(function(){
				if ('$libsm') {
					if (typeof(soundManager) == 'undefined') {
						jQuery.getScript('$libsm', function(){
							soundManager.debugMode = false;
							soundManager.url = '". _DIR_LIB_SM ."swf/';
							soundManager.onready(function(){
								soundManager.play('sm2movie', '$chanson');
							});
						});
					} else {
						soundManager.onready(function(){
							soundManager.play('sm2movie', '$chanson');
						});
					}
				}
			});
			",
	));
	
}
?>
