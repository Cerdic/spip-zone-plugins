<?php 

// inc/spiplistes_pipeline_insert_head.php (CP-20071019)

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	SPIP-Listes pipeline
	inc/spiplistes_pipeline_insert_head.php (CP-20071019)
	
	Nota: insert_head en cache. 
		Si modif ici, vider le cache
	
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');

function spiplistes_insert_head ($flux) {
	
	$sig =
		(isset($GLOBALS['spip_header_silencieux']) && $GLOBALS['spip_header_silencieux'])
		? ""
		: "\n\n<!-- SPIP-Listes v.: ".spiplistes_real_version_get(_SPIPLISTES_PREFIX)." -->\n"
		;

	// pour le formulaire en général
	$flux .= ""
		. $sig
		. "<link rel='stylesheet' href='".find_in_path('spiplistes_formulaire.css')."' type='text/css' media='all' />\n"
		;

	// pour la page abonnement.html
	if(in_array(_request('page'), array(
		'abonnement'	// qui contient aussi le formulaire 
		)
		)
	) {
		$flux .= ""
			. "<link rel='stylesheet' href='".find_in_path('spiplistes_style.css')."' type='text/css' media='all' />\n"
			;
	}

	if(!empty($sig))
	{
		$flux .= "<!-- SPIP-Listes / -->\n";
	}
	
	return($flux);
}

?>