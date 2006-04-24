<?php

/**
 * des callbacks standards a executer pour valider une action
 * chaque fonction doit etre nommee callback_..., retourner null si tout
 * va bien, un message d'erreur sinon et avoir la valeur de l'item comme
 * parametre
 * remarque : le callback a le droit de modifer le contenu
 */

// verifier que l'utilisateur est identifie
function callback_identifie(&$content) {
	return null; // plus tard ...
}

// verifier que l'utilisateur est un admin
function callback_admin(&$content) {
	return "plus tard ...";
}

// verifier que l'utilisateur a le droit de modifier l'article
// (admin, ou admin retreint de cette rubrique, ou auteur et article pas publie)
function callback_modifArticle(&$content) {
	return "plus tard ...";
}

?>
