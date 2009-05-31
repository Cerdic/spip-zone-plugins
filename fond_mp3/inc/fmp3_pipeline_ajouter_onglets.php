<?php 

// inc/fmp3_pipeline_ajouter_onglets.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/fmp3_api_globales');

function fmp3_ajouter_onglets ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	// seuls les admins tt rubriques ont acces au bouton
	if(
			$connect_statut 
		&& $connect_toutes_rubriques
		&& ($flux['args'] == 'configuration')
	) {
		$flux['data'][_FMP3_PREFIX] = new Bouton( 
			_DIR_FMP3_IMAGES."fmp3-24.png"
			, _T("fmp3:portfolio_fmp3")
			, generer_url_ecrire(fmp3_configure)
			)
			;
	}
	return ($flux);
}

?>