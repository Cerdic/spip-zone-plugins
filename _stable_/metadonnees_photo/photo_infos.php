<?php


function photo_infos_header($flux) {

	$flux .= "<script src='".url_absolue(find_in_path('javascript/header_prive.js'))."' type=\"text/javascript\"></script>\n";
	return $flux;

}



?>