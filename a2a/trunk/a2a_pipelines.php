<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function a2a_affiche_milieu($flux){	
	$contexte = array();

	if (($flux['args']['exec'] == "articles") || ($flux['args']['exec'] == "article")){
	
		$contexte['id_article_orig'] = $flux["args"]["id_article"];		
        $contexte['formulaire']=_request('formulaire');
		$texte = recuperer_fond('prive/contenu/a2a_article', $contexte, array('ajax'=>true));
		
		if (($p = strpos($flux['data'],'<!--affiche_milieu-->'))!==false)
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	return $flux;
	
}

?>