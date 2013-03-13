<?php
/**
 * Plugin A2A
 * 
 * Fichier des utilisations de pipelines du plugin
 * 
 * @package SPIP\A2A\Pipelines
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 * 
 * Affiche le bloc d'association d'articles entre eux sur la page de visualisation d'article
 * dans l'espache privé
 * 
 * Ajoute la liste des articles liés et le formulaire de liaison dans $flux['data']
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @param array $flux
 * 		Le contexte du pipeline modifié si besoin
 */
function a2a_affiche_milieu($flux){	
	if (($flux['args']['exec'] == "articles" && isset($flux['args']['id_article']) && $flux['args']['id_article'] > 0) || ($flux['args']['exec'] == "article")){
		$contexte = array();
		$contexte['id_article_orig'] = $flux["args"]["id_article"];		
        $contexte['formulaire'] = _request('formulaire');
		$texte = recuperer_fond('prive/contenu/a2a_article', $contexte, array('ajax'=>true));
		
		if (($p = strpos($flux['data'],'<!--affiche_milieu-->'))!==false)
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	return $flux;
}

?>