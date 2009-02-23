<?php
function horloge_insert_head($flux){
	// $clockjs = find_in_path('horloge.js');
	$flux .= '<script type="text/javascript" src="'.generer_url_public('horloge.js').'"></script>';
	return $flux;
}
?>
