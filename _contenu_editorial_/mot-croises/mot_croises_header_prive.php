<?php 

function mot_croises_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('mots-croises-prive.css').'" />';
	return $flux;
}
?>