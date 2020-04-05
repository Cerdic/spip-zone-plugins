<?php

function diapo_insert_head($flux){
	$flux .= '<script type="text/javascript" src="'.generer_url_public('diapo.js').'"></script>';
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('diapo.css').'" type="text/css" media="all" />';
	$flux .= '<link rel="stylesheet" href="' . direction_css(find_in_path('diapo.css')) . '" type="text/css" media="all" />';
	return $flux;
}
function diapo_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'. direction_css(find_in_path('diapo.css')) .'" type="text/css" media="all" />';
	return $flux;
}
function diapo_ieconfig_metas($table){
	$table['diapo']['titre'] = Diapo;
	$table['diapo']['icone'] = 'images/diapo16.png';
	$table['diapo']['metas_brutes'] = 'diapo,diapo_base_version';
	return $table;
}
?>