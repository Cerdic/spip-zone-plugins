<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function fond_espace_prive_insert_head_css($flux) {
	if ( !$fond = find_in_path(_DIR_IMG."fond_espace_prive.jpg")) $fond = find_in_path("fond_espace_prive.jpg");

	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("fond_espace_login.css")."'>\n";
	$flux .= "<style>#minipres{background-image:url(".$fond.")}</style>";
	return $flux;
}


function fond_espace_prive_header_prive($flux) {
	//if ( !$fond = find_in_path(_DIR_IMG."fond_espace_prive.jpg")) $fond = find_in_path("fond_espace_prive.jpg");

	//$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("fond_espace_prive.css")."'>\n";
	//$flux .= "<style>body{background-image:url(".$fond.")}</style>";
	return $flux;
}

