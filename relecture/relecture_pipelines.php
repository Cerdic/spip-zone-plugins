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
 * Affichage du bloc lie aux relectures de l'article en cours d'affichage
 *
 * @param array $flux
 * @return array
 *
**/
function relecture_affiche_droite($flux) {

	if (($type = $flux['args']['exec'])=='article'){
		$id = $flux['args']['id_article'];
		$table = table_objet($type);
		$id_table_objet = id_table_objet($type);

		$flux['data'] .= recuperer_fond('prive/squelettes/extra/article_relectures', array($id_table_objet => $id));
	}

	return $flux;
}

?>
