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
		// Option 1 : une nouvelle barre d'onglets (mais ça fait beaucoup quand même)
		//$navigation_mots = recuperer_fond('prive/squelettes/inclure/mediatheque-navigation-mots', $flux['args']['contexte'])
		//$cherche = "/(<\/div>)(\s+<div class=[\"']nettoyeur[\"']>)/is"; // FIXME : à améliorer, très spécifique au markup quand même
		//$remplace = "$1$navigation_mots$2";
		//$flux['data']['texte'] = preg_replace($cherche, $remplace, $flux['data']['texte']);

		// Option 2 : un simple select ajouté dans les onglets existants
		$select_mots = recuperer_fond('prive/squelettes/inclure/mediatheque-navigation-mots-select', $flux['args']['contexte']);
		$cherche = "/(<\/ul>\s*)(<\/div>)/is"; // FIXME hum hum... sans marqueur où parseur HTML c'est compliqué de s'insérer où on veut
		$remplace = "$1$select_mots$2";
		$flux['data']['texte'] = preg_replace($cherche, $remplace, $flux['data']['texte']);
	}
	return $flux;
}
