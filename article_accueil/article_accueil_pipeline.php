<?php
/*
 * Plugin Article Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 */


/**
 * Affichage du formulaire de selection de l'article d'accueil
 *
 * @param array $flux
 * @return array
 */
function article_accueil_affiche_milieu($flux){
	$exec = $flux['args']['exec'];
	if ($exec=='naviguer'){
		if ($id = $flux['args']['id_rubrique']) {
			if (autoriser('modifier','rubrique',$id)) {
				$ids = 'formulaire_editer_article_accueil-' . $id;
				$bouton = bouton_block_depliable(strtoupper(_T('article_accueil:article_accueil')), false, $ids);
				$flux['data'] .= debut_cadre('e', chemin('article_accueil-24.png','images/'),'',$bouton, '', '', true);
				$flux['data'] .= recuperer_fond('prive/editer/article_accueil', array_merge($_GET, array('id'=>$id)));
				$flux['data'] .= fin_cadre();
			}
		}
	}

	return $flux;
}

?>