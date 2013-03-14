<?php
/**
 * Plugin Article Accueil
 * (c) 2011 Cedric Morin, Joseph
 * Distribue sous licence GPL
 *
 * @package SPIP\Article_accueil\Pipelines 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 * 
 * Affichage du formulaire de selection de l'article d'accueil
 * dans la partie centrale de la page
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le flux modifié
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
						//'type'=>$type, # Non défini
						'id_rubrique'=>$id
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