<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_recherche_a2a_charger($id_article){
	$recherche = _request('recherche');
	$recherche_titre = _request('recherche_titre');
	// par defaut chercher seulement dans le titre:
	if (is_null($recherche_titre) && strlen($recherche) == 0) $recherche_titre = 'oui';
	$id_article_orig = $id_article;

	return 
		array(
			'recherche' => $recherche,
			'recherche_titre' => $recherche_titre,
			'id_article_orig' => $id_article_orig
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
