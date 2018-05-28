<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajout de contenu sous la fiche d'un objet
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function prestations_affiche_enfants($flux) {
	include_spip('inc/config');
	$objets = lire_config('prestations/objets', array());
	
	if (
		isset($flux['args']['objet'])
		and isset($flux['args']['id_objet'])
		and $objet = $flux['args']['objet']
		and $id_objet = intval($flux['args']['id_objet'])
		and in_array(table_objet_sql($objet), $objets)
	) {
		$enfants = recuperer_fond(
			'prive/objets/contenu/objet-prestations-enfants',
			array(
				'objet' => $objet,
				'id_objet' => $id_objet,
			)
		);
		
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $enfants, $p, 0);
		} else {
			$flux['data'] .= $enfants;
		}
	}
	
	return $flux;
}
