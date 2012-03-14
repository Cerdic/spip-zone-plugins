<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function a2a_affiche_milieu($flux){
	include_spip('public/assembler');
        include_spip('inc/layer');
        include_spip('inc/presentation');
	
	$contexte = array();
	
	if (($flux['args']['exec'] == "articles") || ($flux['args']['exec'] == "article")){
		$contexte['id_article_orig'] = $flux["args"]["id_article"];
		$flux['data'] .= "<div id='pave_a2a'>";
		$bouton = bouton_block_depliable(_T('a2a:articles_lies'), "replie", "pave_a2a_depliable");
		$flux['data'] .= debut_cadre_enfonce(find_in_path('images/a2a-22.png'), true, "", $bouton);
		$flux['data'] .= recuperer_fond('prive/contenu/a2a_article', $contexte, array('ajax'=>true));
		$flux['data'] .= fin_cadre_enfonce(true);
		$flux['data'] .= "</div>";
	}
	return $flux;
	
}

?>
