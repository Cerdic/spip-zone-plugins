<?php
function lm2_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path(_DIR_LIB_SM.'script/soundmanager2.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_playlist_jquery.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_inlineplayer.js').'"></script>'."\n";
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('lm2_player.css').'" type="text/css" media="all" />'."\n";
	$flux .= '<script type="text/javascript">soundManager.debugMode = false;</script>';
	return $flux;
}

?>