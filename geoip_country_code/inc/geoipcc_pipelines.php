<?php

// inc/geoipcc_pipelines.php

/**
 * Copyright (c) 2009 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 * */

 /**
 * bouton sur la page de configuration
 * @return array
 * @param array $flux
 */
function geoipcc_ajouter_boutons ($flux)
{
	// bouton aide pour les webmaitres
	
	if (
		($GLOBALS['connect_statut'] == '0minirezo')
		&& $GLOBALS['connect_toutes_rubriques']
	) {
		global $spip_lang;

		// le bouton d'aide en ligne
		$flux['aide_index']->sousmenu['geoipcc_aide']= new Bouton(
			_DIR_GEOIPCC_IMAGES . 'geoipcc-24.png'
			, 'geoipcc:GeoIPcc'
			, generer_url_prive('geoipcc_aide') . "&amp;var_lang=$spip_lang"
			)
			;
	}
	return ($flux);
}
