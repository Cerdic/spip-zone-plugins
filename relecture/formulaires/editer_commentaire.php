<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_editer_commentaire_charger_dist($id_commentaire='oui', $id_relecture, $index_debut, $index_fin, $redirect='') {
	// Traitement standard de chargement
	$valeurs = formulaires_editer_objet_charger('commentaire', $id_commentaire, 0, 0, $redirect, 'relectures_edit_config');

	return $valeurs;
}

function formulaires_editer_commentaire_verifier_dist($id_commentaire, $id_relecture, $index_debut, $index_fin, $redirect='') {
	$erreurs = formulaires_editer_objet_verifier('commentaire', $id_commentaire, array('texte'));
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_commentaire_traiter_dist($id_commentaire, $id_relecture, $index_debut, $index_fin, $redirect='') {

	// les autres traitements particuliers de creation de  l'objet relecture sont faits dans le
	// pipeline pre_insertion
	// Pour les modifications, aucun traitement particulier n'est necessaire
	return formulaires_editer_objet_traiter('commentaire', $id_commentaire, 0, 0, $redirect);
}

?>