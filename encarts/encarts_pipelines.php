<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Avec l'aide de Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */


function encarts_affiche_milieu($flux) {

	$exec =  $flux['args']['exec'];
	
	// si on est sur la page ?exec=articles on affiche le bouton et/ou le bloc encarts
	if ($exec=='articles' AND $id_article = $flux['args']['id_article']) {
	
		$id_article = $flux['args']['id_article'];
		if (!$id_rubrique = $flux['args']['id_rubrique']) {
			$id_rubrique = sql_getfetsel('id_rubrique', 'spip_articles', 'id_article='. $id_article);
		}

		// a corriger $_GET... trop permissif
		$contexte = $_GET;
		$flux['data'] .= recuperer_fond('prive/boite/encarts_article', $contexte, array('ajax'=>true));

	}

	return $flux;
}



?>