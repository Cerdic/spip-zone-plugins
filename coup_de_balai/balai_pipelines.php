<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Insertion dans la colonne de gauche du squelette info_balai
function balai_affiche_gauche($flux){
	include_spip('inc/presentation');
	$add = '';

	if(isset($flux['args']['exec']) and $flux['args']['exec'] == 'rubrique'
		and isset($flux['args']['id_rubrique']) and $id_rubrique = $flux['args']['id_rubrique']){
			$add = debut_cadre_relief('', true, '', 'Coup de balai') . recuperer_fond('squelettes/info_balai', array('id_rubrique'=>$id_rubrique)) . fin_cadre_relief(true);
		}

	if(isset($flux['args']['exec']) and $flux['args']['exec'] == 'article'
		and isset($flux['args']['id_article']) and $id_article = $flux['args']['id_article']
			and !sql_fetch(sql_select('id_article', 'spip_articles',  array(
		        "id_article = $id_article",
				"statut = 'poubelle'"
				)
			))){
			$add =  debut_cadre_relief('', true, '', 'Coup de balai') . recuperer_fond('squelettes/info_balai', array('id_article'=>$id_article)) . fin_cadre_relief(true);
	}

		// Insertion en tête de la colonne. On ne va pas se gêner !
	$flux['data'] = $add . $flux['data'];

	return $flux;
}

// Insertion du .css
function balai_header_prive($flux){
	$css_balai = find_in_path('css/balai.css');
	$flux .= "\n<link rel='stylesheet' href='$css_balai' type='text/css' />\n";
	return $flux;
}

?>
