<?php
function gmaps_v3_affiche_milieu($vars) {
	
	include_spip('inc/abstract_sql');
	include_spip('inc/autoriser');
		
	// Si c'est un article, on bosse
	if($vars["args"]["exec"] == 'articles' && $vars["args"]["id_article"] != ''){
		$type_object = 'article';
		$id_object   = $vars["args"]["id_article"];
	}
	// Si c'est une rubrique, on ne fait rien
	// On pourrait afficher la MAP quand même, suffirait de décommenter
	elseif($vars["args"]["exec"] == 'naviguer' && $vars["args"]["id_rubrique"] != ''){
		// $type_object = 'rubrique';
		// $id_object   = $vars["args"]["id_rubrique"];
		return $vars;
	}
	// Sinon, et bien on ne fait rien non plus
	else{
		return $vars;
	}
	
	$id_article = $vars["args"]["id_article"];
	
	$fond = 'prive/contenu/gmaps_v3_affiche_milieu';
	$ret = recuperer_fond($fond,array(
		'id_article'=>$id_article
		));
		
	$vars["data"] .= $ret;
	
	return $vars;
}

