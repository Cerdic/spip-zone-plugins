<?php
/**
 * D�claration des filtres et balises
 *
 * @plugin     Pages
 * @copyright  2013
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Pipelines
 * @link       https://contrib.spip.net/Pages-uniques
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



// https://code.spip.net/@balise_URL_ARTICLE_dist
function balise_URL_PAGE_UNIQUE_dist($p) {

	$_id = interprete_argument_balise(1, $p);
	if (!$_id) {
		$msg = array('zbug_balise_sans_argument', array('balise' => ' URL_PAGE_UNIQUE'));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = false;
		return $p;
	}

	if (!function_exists('generer_generer_url_arg')) {
		include_spip('balise/url_');
	}

	$_id = "sql_getfetsel('id_article','spip_articles','page='.sql_quote($_id))";
	$p->code = generer_generer_url_arg('article', $p, $_id);
	if (!$p->etoile) {
		$p->code = "vider_url($p->code)";
	}
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Lister les pages uniques utiles qui ne sont pas encore créées
 *
 * Cette liste est complétée par les plugins tiers au moyen du pipeline pages_uniques_utiles.
 *
 * @uses pages_uniques_utiles()
 * @return array
 *     Tableau associatif : page => titre
 */
function pages_uniques_utiles() {

	if (
		$pages_utiles = pipeline('pages_uniques_utiles', array())
		and is_array($pages_utiles)
	) {
		include_spip('base/abstract_sql');
		foreach ($pages_utiles as $page => $titre) {
			// Si la page existe déjà, on la vire
			if (sql_countsel('spip_articles', 'page = '.sql_quote($page))) {
				unset($pages_utiles[$page]);
			}
		}
	}

	return $pages_utiles;
}