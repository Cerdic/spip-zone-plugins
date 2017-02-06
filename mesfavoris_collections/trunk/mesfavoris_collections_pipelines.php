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
