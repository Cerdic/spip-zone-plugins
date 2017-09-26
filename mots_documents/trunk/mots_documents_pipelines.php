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
		and $navigation_mots = recuperer_fond('prive/squelettes/inclure/mediatheque-navigation-mots', $flux['args']['contexte'])
	) {
		// On le place avant la recherche
		// FIXME : à améliorer, très spécifique au markup quand même
		$cherche = "/(<\/div>)(\s+<div class=[\"']nettoyeur[\"']>)/is";
		$remplace = "$1$navigation_mots$2";
		$flux['data']['texte'] = preg_replace($cherche, $remplace, $flux['data']['texte']);
	}
	return $flux;
}