<?php
/**
 * Déclaration des filtres et balises
 *
 * @plugin     Pages
 * @copyright  2013
 * @author     RastaPopoulos
 * @licence    GNU/GPL
 * @package    SPIP\Pages\Pipelines
 * @link       http://contrib.spip.net/Pages-uniques
 */

if (!defined("_ECRIRE_INC_VERSION")) return;



// http://doc.spip.org/@balise_URL_ARTICLE_dist
function balise_URL_PAGE_UNIQUE_dist($p) {

	$_id = interprete_argument_balise(1,$p);
	if (!$_id) {
		$msg = array('zbug_balise_sans_argument', array('balise' => ' URL_PAGE_UNIQUE'));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = false;
		return $p;
	}

	if (!function_exists("generer_generer_url_arg"))
		include_spip("balise/url_");

	$_id = "sql_getfetsel('id_article','spip_articles','page='.sql_quote($_id))";
	$p->code = generer_generer_url_arg('article', $p, $_id);
	if (!$p->etoile)
		$p->code = "vider_url($p->code)";
	$p->interdire_scripts = false;
	return $p;
}
