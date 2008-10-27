<?php

function bigbrother_affiche_droite($flux){
	/*if (in_array($flux['args']['exec'],array('articles_edit','breves_edit','rubriques_edit','mots_edit'))){
	include_spip('exec/inc_boites_infos');	
	$flux['data'] .= boite_info_jeux_edit();
	}*/
	
	if ($flux['args']['exec'] == 'auteur_infos'){
	
		$boite = debut_boite_info(true)
			. icone_horizontale(
				_T('bigbrother:voir_statistiques_auteur'),
				generer_url_ecrire('bigbrother_visites_articles_auteurs','id_auteur='.$flux['args']['id_auteur']),
				find_in_path('bigbrother-24.png', 'images/', false),
				'',
				false
			)
			. fin_boite_info(true);		
		
		$flux['data'] .= $boite;  
	
	}
	elseif ($flux['args']['exec'] == 'articles'){
	
		$boite = debut_boite_info(true)
			. icone_horizontale(
				_T('bigbrother:voir_statistiques_article'),
				generer_url_ecrire('bigbrother_visites_articles_auteurs','id_article='.$flux['args']['id_article']),
				find_in_path('bigbrother-24.png', 'images/', false),
				'',
				false
			)
			. fin_boite_info(true);		
		
		$flux['data'] .= $boite;  
	
	}
	
	return $flux;
}


function bigbrother_insert_head($flux){

	$flux .= '<link rel="stylesheet" media="all" type="text/css" href="'.find_in_path('bigbrother.css', 'css/', false).'" />';
	return $flux;

}

function bigbrother_header_prive($flux){

	$flux .= '<link rel="stylesheet" madia="all" type="text/css" href="'.find_in_path('bigbrother.css', 'css/', false).'" />';
	return $flux;

}

?>
