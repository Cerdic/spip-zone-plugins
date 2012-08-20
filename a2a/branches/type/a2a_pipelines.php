<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function a2a_affiche_milieu($flux){	
	$contexte = array();

	if (($flux['args']['exec'] == "articles") || ($flux['args']['exec'] == "article")){
	
		$contexte['id_article_orig'] = $flux["args"]["id_article"];		

		$texte = recuperer_fond('prive/contenu/a2a_article', $contexte, array('ajax'=>true));
		
		if (($p = strpos($flux['data'],'<!--affiche_milieu-->'))!==false)
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	return $flux;
	
}

function a2a_header_prive_css($css){
	$css.= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('a2a_prive.css').'" id="csspriveea2a" />';
	return $css;
}
?>
