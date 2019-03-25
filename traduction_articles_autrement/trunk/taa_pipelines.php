<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Agit lors de l’édition d’un élément éditorial, lorsque l’utilisateur édite les champs ou change le statut de l’objet.
 * Il est appelé juste avant l’enregistrement des données.
 * On peut s’en servir pour contrôler ou modif
 *
 * @pipeline pre_edition
 *
 * @param array $flux
 *   Les données du pipeline
 *
 * @return array
 *   Les donées du pipeleine.
 */
function taa_pre_edition($flux) {
	$table = $flux['args']['table'];
	// Si tradrub actif, on suppose le  système de secteur par langue.
	// L'article doit donc avoir la mème langue que la rubrique parente.
	if ($table == 'spip_articles' and test_plugin_actif('tradrub')) {
		$rubrique = sql_fetsel('id_rubrique,lang', $table, 'id_article=' . $flux['args']['id_objet']);
		if ($lang = sql_getfetsel(
				'lang',
				'spip_rubriques',
				'id_rubrique=' . $rubrique['id_rubrique']) and $lang != $rubrique['lang']) {
			$flux['data']['lang'] = $lang;
		}
	}

	return $flux;
}