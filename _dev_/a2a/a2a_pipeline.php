<?php

function a2a_affiche_milieu($flux){
	include_spip('public/assembler');
	
	$contexte = array();
	
	if ($flux['args']['exec'] == "articles"){
		$contexte['id_article_orig'] = $flux["args"]["id_article"];
		$flux['data'] .= "<div id='pave_a2a'>";
		$flux['data'] .= debut_cadre_enfonce("", true, "", _T('a2a:articles_lies'));
		$flux['data'] .= recuperer_fond('fonds/a2a_interface', $contexte);
		$flux['data'] .= fin_cadre_enfonce(true);
		$flux['data'] .= "</div>";
	}
	return $flux;
	
}

function a2a_header_prive($flux){
	include_spip('inc/utils');
	
	$fichier_js = find_in_path("javascript/a2a.js");
	$flux .= "<script type=\"text/javascript\" src=\"".generer_url_public('a2a.js')."\" ></script>";
	return $flux;
}

?>
