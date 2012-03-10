<?php
/*
 * Plugin Article Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Affichage du formulaire de selection de l'article d'accueil
 *
 * @param array $flux
 * @return array
 */
function article_accueil_affiche_milieu($flux){
	$exec = $flux['args']['exec'];
	if (($exec=='naviguer') || ($exec == 'rubrique')){
		if ($id = $flux['args']['id_rubrique']) {
			if (autoriser('modifier','rubrique',$id)) {
				$ids = 'formulaire_editer_article_accueil-' . $id;
				$texte = recuperer_fond(
					'prive/editer/article_accueil',
					array(
						'type'=>$type,
						'id_rubrique'=>$id,
					)
				);
				if (($p = strpos($flux['data'],'<!--affiche_milieu-->'))!==false)
					$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
				else
					$flux['data'] .= $texte;
			}
		}
	}

	return $flux;
}

?>
