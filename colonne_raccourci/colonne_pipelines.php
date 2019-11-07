<?php

if (!defined('_ECRIRE_INC_VERSION')){
	return;
}

/*
// paquet.xml

	<pipeline nom="scss_insert_head" inclure="colonne_pipelines.php" />
	<pipeline nom="insert_head" inclure="colonne_pipelines.php" />	
	
*/
/* insertion de scripts scss calculés, mmm?? */
function colonne_scss_insert_head($flux) {
	//$flux .='<link rel="stylesheet" href="'.scss_css(find_in_path('css/spip_colonne.css')).'">';
	return $flux;
}

/* insertion du js */
function colonne_insert_head($flux) {
	//$flux .='<script src="'.find_in_path('javascript/spip_colonne.js').'"></script>';
	return $flux;
}