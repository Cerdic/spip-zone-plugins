<?php
/**
 * Mots documents
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Compléter ou modifier le résultat de la compilation d’un squelette donné
 *
 * On ajoute les mots-clés à la navigation des documents
 *
 * @pipeline recuperer_fond
 * @param array $flux
 * @return array
 */
function mots_documents_recuperer_fond($flux){

	if ($flux['args']['fond'] == 'prive/squelettes/inclure/mediatheque-navigation'
		and sql_countsel('spip_groupes_mots', 'tables_liees LIKE '.sql_quote('%documents%'))
	) {

		$fond_mots = recuperer_fond('prive/squelettes/inclure/mediatheque-navigation-mots', $flux['args']['contexte']);
		// On s'insère après le dernier <ul> de la barre d'onglets secondaires
		// Bon, sans parseur, c'est la galère
		$cherche = "#<ul\s+class=[\"']sanstitre[\"']>\s*(?:<li[^>]*>(?!.*<li>).*?</li>\s*)+\s*</ul>#i";
		$remplace = "$0$fond_mots";
		$flux['data']['texte'] = preg_replace($cherche, $remplace, $flux['data']['texte']);
	}
	return $flux;
}
