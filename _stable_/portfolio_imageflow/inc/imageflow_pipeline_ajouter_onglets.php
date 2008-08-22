<?php 

// inc/imageflow_pipeline_ajouter_onglets.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/imageflow_api_globales');

function imageflow_ajouter_onglets ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	// seuls les admins tt rubriques ont acces au bouton
	if(
			$connect_statut 
		&& $connect_toutes_rubriques
		&& ($flux['args'] == 'configuration')
	) {
		$flux['data'][_IMAGEFLOW_PREFIX] = new Bouton( 
			_DIR_IMAGEFLOW_IMAGES."ImageFlow_configure-24.png"
			, _T("imageflow:portfolio_imageflow")
			, generer_url_ecrire(imageflow_configure)
			)
			;
	}
	return ($flux);
}

?>