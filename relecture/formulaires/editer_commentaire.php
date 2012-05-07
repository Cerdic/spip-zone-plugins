<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_commentaire_charger_dist($id_commentaire='oui', $redirect='') {
	// Traitement standard de chargement
	$valeurs = formulaires_editer_objet_charger('commentaire', $id_commentaire, 0, 0, $redirect, '');

	// Ouverture d'un commentaire
	if (($id_commentaire == 'oui')
	AND ($id_relecture = intval(_request('id_relecture')))) {
		// On supprime les index 'id_relecture', 'element' du tableau des valeurs afin qu'ils soient transmis dans
		// la fonction traiter() (car ce sont des champs de l'objet relecture)
		unset($valeurs['id_relecture']);
		unset($valeurs['element']);
	}
	// Modification d'une commentaire
	else if ($id = intval($id_commentaire)) {
		// - si le commentaire est ouvert l'auteur de l'article peut le moderer.
		//   On lui renvoie le texte du commentaire et de la reponse
		$textes = sql_fetsel('texte, reponse', 'spip_commentaire', "id_commentaire=$id");
		$valeurs = array_merge($valeurs, $textes);
	}

	return $valeurs;
}

function formulaires_editer_commentaire_verifier_dist($id_commentaire, $redirect='') {
	$erreurs = formulaires_editer_objet_verifier('commentaire', $id_commentaire, array('texte'));

	// On ajoute des verifications specifiques :
	// - le texte du commentaire doit avoir plus de n caracteres.

	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_commentaire_traiter_dist($id_commentaire, $redirect='') {

	// les autres traitements particuliers de creation de  l'objet commentaire sont faits dans le
	// pipeline pre_insertion
	// Pour les modifications, aucun traitement particulier n'est necessaire
	return formulaires_editer_objet_traiter('commentaire', $id_commentaire, 0, 0, $redirect);
}

?>