<?php
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function taa_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/taa_styles.css') . '" type="text/css" media="all" />';
	return $flux;
}

/* Ajoute la langue de traduction dans le chargement du formulaire edition_article */
function taa_formulaire_charger($flux) {
	$form = $flux['args']['form'];
	if ($form == 'editer_article') {
		$id_article = $flux['data']['id_article'];
		if (!$id_rubrique = $flux['data']['id_parent'])
			$id_rubrique = (_request('id_rubrique') ? _request('id_rubrique') : (intval($id_article) ? sql_getfetsel('id_rubrique', 'spip_articles', 'id_article=' . $id_article) : ''));
		$lang = _request('lang_dest');

		if (!$lang and intval($id_rubrique))
			$lang = sql_getfetsel('lang', 'spip_rubriques', 'id_rubrique=' . $id_rubrique);
		$flux['data']['lang_dest'] = $lang;

		if ($flux['data']['lang_dest']) {
			$flux['data']['_hidden'] .= '<input type="hidden" name="lang_dest" value="' . $lang . '"/>';
			$flux['data']['_hidden'] .= '<input type="hidden" name="changer_lang" value="' . $lang . '"/>';
		}
	}

	return $flux;
}

/* Prise en compte de la langue de traduction dans le traitement du formulaire edition_article */
function taa_pre_insertion($flux) {
	if ($flux['args']['table'] == 'spip_articles') {
		if ($lang = _request('lang_dest')) {
			$flux['data']['lang'] = $lang;
		}
		elseif (test_plugin_actif('tradrub')) {
			$id_rubrique = _request('id_parent') ? _request('id_parent') : _request('id_rubrique');
			$lang = sql_getfetsel('lang', 'spip_rubriques', 'id_rubrique=' . $id_rubrique);
			$flux['data']['lang'] = $lang;
		}
	}
	return $flux;
}

/*function taa_recuperer_fond($flux) {
	// Insertion des onglets de langue
	if ($flux['args']['fond'] == 'prive/squelettes/contenu/article') {
		include_spip('inc/config');
		$id_article = $flux['args']['contexte']['id_article'];

		// Vérifier si il y des secteurs à exclure
		$id_secteur = sql_getfetsel('id_secteur', 'spip_articles', 'id_article=' . $id_article);
		$limiter_secteur = lire_config('taa/limiter_secteur') ? lire_config('taa/limiter_secteur') : array();

		if (!in_array($id_secteur, $limiter_secteur)) {
			$barre = charger_fonction('barre_langues', 'inc');
			$barre_langue = $barre($id_article);

			$flux['data']['texte'] = str_replace('</h1>', '</h1>' . $barre_langue, $flux['data']['texte']);
		}
	}

	// Liste compacte des articles
	if ($flux['args']['fond'] == 'prive/objets/liste/articles' and _request('exec') != 'article' and !lire_config('taa/liste_compacte_desactive')) {

		$flux['texte'] = recuperer_fond('prive/objets/liste/articles_compacte', $flux['args']['contexte']);
	}

	return $flux;
}*/

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