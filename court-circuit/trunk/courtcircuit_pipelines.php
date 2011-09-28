<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline styliser pour court-circuiter les rubriques
 *
 * @param array $flux
 * @return array
 */
function courtcircuit_styliser($flux){
	if ($flux['args']['fond'] == 'rubrique' AND $id_rubrique = $flux['args']['id_rubrique']) {
		include_spip('inc/courtcircuit');
		$url_redirect = courtcircuit_url_redirection($id_rubrique);
		if ($url_redirect!='')
			redirige_par_entete($url_redirect,'','301');
	}
	return $flux;
}

?>
