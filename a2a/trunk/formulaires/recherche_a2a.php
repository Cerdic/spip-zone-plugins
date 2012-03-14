<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_recherche_a2a_charger($id_article){
	$recherche = _request('recherche');
	$recherche_titre = _request('recherche_titre');
	$type_liaison = _request('type_liaison');
	$id_article_orig = $id_article;

	return 
		array(
			'recherche' => $recherche,
			'recherche_titre' => $recherche_titre,
			'id_article_orig' => $id_article_orig,
			'type_liaison' => $type_liaison,
		);
}

function formulaires_recherche_a2a_verifier($id_article){
	$erreurs['message_erreur'] = _T('a2a:pas_de_resultat');
	return $erreurs;
}

function formulaires_recherche_a2a_traiter($id_article){
	return true; // permettre d'editer encore le formulaire
}

?>
