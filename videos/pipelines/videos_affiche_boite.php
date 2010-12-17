<?php
function videos_affiche_gauche($flux) {
	
	include_spip('inc/autoriser');
			
	// Si c'est un article en édition ou un article dans le privé, on propose le formulaire, si l'article n'existe pas encore, on ne fait rien
	if(($flux["args"]["exec"] == 'articles_edit' || $flux["args"]["exec"] == 'articles') && $flux["args"]["id_article"] != ''){
		$type_objet = 'article';
		$id_type_objet = 'id_article';
		$id_objet   = $flux["args"]["id_article"];
	}
	// Si c'est une rubrique, on ne fait rien
	elseif($flux["args"]["exec"] == 'naviguer' && $flux["args"]["id_rubrique"] != ''){
		$type_objet = 'rubrique';
		$id_type_objet = 'id_rubrique';
		$id_objet   = $flux["args"]["id_rubrique"];
	}
	// Sinon, et bien on ne fait rien non plus
	else{
		return $flux;
	}
	
	
	$fond = 'prive/contenu/videos_affiche_boite';
	$flux["data"] .= recuperer_fond($fond,array(
		'id_objet' => $id_objet,
		'objet' => $type_objet
		));
	
	return $flux;
}

