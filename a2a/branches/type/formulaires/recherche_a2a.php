<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_recherche_a2a_charger($id_article){
	$recherche = _request('recherche');
	$recherche_titre = _request('recherche_titre');
	$id_article_orig = $id_article;

	return 
		array(
			'recherche' => $recherche,
			'recherche_titre' => $recherche_titre,
			'id_article_orig' => $id_article_orig,
		);
}

function formulaires_recherche_a2a_verifier($id_article){
}

function formulaires_recherche_a2a_traiter($id_article){
	return true; // permettre d'editer encore le formulaire
}

?>