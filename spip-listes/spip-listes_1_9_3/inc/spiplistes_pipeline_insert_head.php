<?php 
/**
 * @package spiplistes
 */
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
		// deja charge' dans le squelette
		//. "<link rel='stylesheet' href='".find_in_path('spiplistes_formulaire.css')."' type='text/css' media='all' />\n"
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
		if(
			(($s = spiplistes_pref_lire('opt_plier_deplier_formabo')) && ($s == 'oui'))
			&& ($f = find_in_path("javascript/spiplistes_abonnement.js")))
		{
			//$flux .= "<script type='text/javascript' src='" . compacte($f) . "'></script>\n";
			$flux .= "<script type='text/javascript' src='" . $f . "'></script>\n";
		}
	}

	if(!empty($sig))
	{
		$flux .= "<!-- SPIP-Listes / -->\n";
	}
	
	return($flux);
}

?>