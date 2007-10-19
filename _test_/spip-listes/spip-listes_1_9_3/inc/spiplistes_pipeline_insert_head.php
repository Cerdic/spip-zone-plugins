<?php 
/*
	SPIP-Listes pipeline
	inc/spiplistes_pipeline_insert_head.php (CP-20071019)
	
	Nota: insert_head en cache. 
		Si modif ici, vider le cache
	
*/


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

function spiplistes_insert_head ($flux) {

	$flux .= ""
		. "\n\n<!-- PLUGIN SPIPLISTES v.: ".__plugin_real_version_get()." -->\n"
	;

	if(in_array(_request('page'), array(
		'abonnement'	// formulaire 
		)
		)
	) {
		$flux .= ""
			. "<link rel='stylesheet' href='"._DIR_PLUGIN_SPIPLISTES."spiplistes_style.css' type='text/css' media='all' />\n"
			;
	}

	$flux .= ""
		. "\n<!-- / PLUGIN SPIPLISTES -->\n"
	;
	return ($flux);
}

?>