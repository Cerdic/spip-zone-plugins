<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline styliser pour cour-circuiter les rubriques
 *
 * @param array $flux
 * @return array
 */
function courtcircuit_styliser($flux){
	if ($flux['args']['fond'] == 'rubrique' AND $id_rubrique = $flux['args']['id_rubrique']) {
		if (isset($GLOBALS['meta']['courtcircuit']))
			$config = unserialize($GLOBALS['meta']['courtcircuit']);
		else $config = array();
		$redirect_article = recuperer_fond(
			'courtcircuit_selection_article', 
			array_merge(array('id_rubrique' => $id_rubrique),$config)
			);
		if (intval($redirect_article)) {
			include_spip('inc/utils');
			include_spip('inc/headers');
			$url = generer_url_entite(intval($redirect_article), 'article', '', '', true);
			redirige_par_entete($url,'','301');
		}
	}
	return $flux;
}

?>
