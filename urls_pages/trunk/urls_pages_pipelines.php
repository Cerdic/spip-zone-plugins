<?php
/**
 * Pipelines utilisees par le plugin URLs Pages Personnalisées
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Urls_pages_personnalisees\pipelines
 */


/**
 * Ajouter du contenu dans le conteneur central
 *
 * - Page « controler_urls » : ajout du menu pour basculer entre les URLs normales et celles des pages
 *
 * @pipeline affiche_gauche
 */
function urls_pages_affiche_milieu($flux){

	if (isset($flux['args']['exec'])
		and $flux['args']['exec'] == 'controler_urls'
	){
		$cherche  = '/(<h1[^>]{0,}>)([^>]+)(<\/h1>)/';
		$titre = _T('urls_pages:menu_urls_objets');
		$menu = recuperer_fond('prive/squelettes/inclure/menu_urls_pages');
		$flux['data'] = preg_replace($cherche, "$menu$1$titre$3", $flux['data']);
	}

	return $flux;
}


/**
 * Modifier les requêtes SQL servant à générer les boucles
 *
 * Boucles URLS : exclure des résultats les URLs des pages en absence du critère {tout}
 *
 * @pipeline pre_boucle
 *
 * @param  object $boucle
 * @return objetc $boucle
**/
function urls_pages_pre_boucle($boucle){

	if ($boucle->type_requete == 'spip_urls') {
		$id_table = $boucle->id_table;
		$page     = $id_table .'.page';
		$id_objet = $id_table .'.id_objet';
		$type     = $id_table .'.type';
		// Restreindre aux URLs sans page
		if (!isset($boucle->modificateur['tout'])) {
				$boucle->where[]= array("'='", "'$page'", "'\"\"'");
		}
	}

	return $boucle;
}
