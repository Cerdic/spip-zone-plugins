<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Modifier le squelette des formulaires
 *
 * - Changer le label du bouton pour retirer un favori dans le contexte d'une collection
 *
 * @param  array $flux
 * @return array
 */
function mesfavoris_collections_formulaire_fond($flux) {
	
	if (isset($flux['args']['form'])
		and $flux['args']['form'] == 'favori'
		and $id_favoris_collection = intval(_request('id_favoris_collection'))
	){
		include_spip('inc/filtres');
		$recherche = array(attribut_html(_T('mesfavoris:remove')),attribut_html(_T('mesfavoris:remove_from')));
		$remplace = attribut_html(_T('favoris_collection:retirer_id_favoris_collection_label'));
		$flux['data'] = str_replace($recherche, $remplace, $flux['data']);
	}
	
	return $flux;
}


/**
 * Compléter la vérification des données soumises d’un formulaire CVT
 *
 * - Retirer un favori d'une collection :
 *   si la constante _MESFAVORIS_COLLECTIONS_CONSERVER_ORPHELINS est définie, 
 *   et s'il n'est que dans une seule collection, on le sort de celle-ci au lieu de le supprimer.
 *
 * @note
 * Le traiter du formulaire des favoris est surchargé dans mesfavoris_collections_options.php.
 * On pourrait mettre le code dedans, mais c'est mieux ici si jamais la surcharge disparait un jour.
 *
 * @pipeline formulaire_verifier
 *
 * @param array $flux
 * @return array
 */
function mesfavoris_collections_formulaire_verifier($flux) {
	
	if (defined('_MESFAVORIS_COLLECTIONS_CONSERVER_ORPHELINS')
		and _MESFAVORIS_COLLECTIONS_CONSERVER_ORPHELINS === true
		and isset($flux['args']['form'])
		and $flux['args']['form'] == 'favori'
		and isset($flux['args']['args'][0])
		and isset($flux['args']['args'][1])
		and intval(_request('id_favoris_collection'))
		and !is_null(_request('retirer'))
	) {
		include_spip('base/abstract_sql'); // au cas où

		$objet     = $flux['args']['args'][0];
		$id_objet  = $flux['args']['args'][1];
		$categorie = isset($flux['args']['args'][2]) ? $flux['args']['args'][2] : '';

		// trouver les favoris de l'objet qui se trouvent dans des collections
		$favoris = sql_allfetsel(
			'id_favori',
			'spip_favoris',
			array(
				'objet = ' . sql_quote($objet),
				'id_objet = ' . intval($id_objet),
				'id_favoris_collection > 0',
				//'categorie = ' . sql_quote($categorie), // mmmmmh, utile ?
			)
		);
		// S'il n'est présent que dans *une* collection,
		// on le sort de celle-ci et on annule sa suppression.
		if (count($favoris) === 1){
			$favoris = array_shift($favoris);
			sql_updateq(
				'spip_favoris',
				array(
					'id_favoris_collection' => 0,
				),
				array(
					'id_favori = ' . intval($favoris['id_favori']),
				)
			);
			set_request('retirer', null);
		}
	}
	
	return $flux;
}