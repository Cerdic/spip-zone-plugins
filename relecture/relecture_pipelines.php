<?php

/**
 * Ajout de l'onglet Ajouter les plugins dont l'url depend du l'existence ou pas d'un depot
 * de plugins
 *
 * @param array $flux
 * @return array
 */
function relecture_ajouter_onglets($flux) {
    return $flux;
}


/**
 * Affichage des blocs lies aux relectures
 *
**/
function relecture_affiche_gauche($flux) {

	if ($flux['args']['exec'] == 'article'){
		$flux['data'] .= recuperer_fond('prive/squelettes/extra/relectures', array(
				'id_article' => $flux['args']['id_article']
			));
	}

	return $flux;
}
?>
