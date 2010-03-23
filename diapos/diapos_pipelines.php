<?php
function diapos_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapos.js').'"></script>';
	
	find_in_path('diapos.css.html'); 
	if (find_in_path('diapos.css.html')){
		$flux .= recuperer_fond('diapos-insert-head',array());
	}
	
	//if (find_in_path('diapos.css.html')){
		//$flux .= '<link rel="stylesheet" href="'.generer_url_public("diapos.css").'" type="text/css" media="projection, screen, tv" />';
	//}
	return $flux;
}

function diapos_header_prive($flux){
	$flux .= '<script type="text/javascript" src="'.find_in_path('diapos.js').'"></script>';
	return $flux;
}
?>