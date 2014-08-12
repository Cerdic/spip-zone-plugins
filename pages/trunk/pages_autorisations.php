<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function pages_autoriser() {}


/* ----------------------- AUTORISATIONS DE L'OBJET PAGE UNIQUE ----------------------- */

/**
 * Autorisation de créer un page unique.
 *
 * Cette page unique peut être créée soit à partir de rien
 * soit en convertissant un article existant.
 * Par défaut seuls les administrateurs complets sont autorisés.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_page_creer_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - l'auteur connecté est un administrateur complet
	$autoriser = pages_autorisation_defaut_dist($qui);

	return $autoriser;
}

/**
 * Autorisation de modifier une page unique existante.
 *
 * Cette page peut être modifiée soit au travers du formulaire d'édition
 * soit en convertissant une page en article éditorial.
 * Par défaut seuls les administrateurs complets sont autorisés.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_page_modifier_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - l'auteur connecté est un administrateur complet
	if ($id_article = intval($id)) {
		$autoriser = pages_autorisation_defaut_dist($qui);
	}

	return $autoriser;
}


/**
 * Autorisation d'afficher une page unique.
 *
 * Par défaut seuls les administrateurs complets sont autorisés.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_page_voir_dist($faire, $type, $id, $qui, $opt) {

	$autoriser = false;

	// Conditions :
	// - l'auteur connecté est un administrateur complet
	if ($id_article = intval($id)) {
		$autoriser = pages_autorisation_defaut_dist($qui);
	}

	return $autoriser;
}


/**
 * Autorisation d'afficher la liste des pages uniques.
 *
 * Par défaut seuls les administrateurs complets sont autorisés.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_pages_voir_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - l'auteur connecté est un administrateur complet
	$autoriser = pages_autorisation_defaut_dist($qui);

	return $autoriser;
}


/**
 * Autorisation d'accéder à la liste des pages uniques.
 *
 * Cette autorisation coîncide avec l'autorisation pages_voir.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_pages_menu_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - l'auteur connecté doit posséder l'autorisation pages_voir
	$autoriser = autoriser('voir', '_pages', $id, $qui, $opt);

	return $autoriser;
}

/**
 * Autorisation d'afficher le bouton créer une page unique inclus
 * dans la barre des outils rapides .
 *
 * Cette autorisation coîncide avec l'autorisation page_creer.
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 *
*@return
 */
function autoriser_pagecreer_menu_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - l'auteur connecté est un administrateur complet
	$autoriser = autoriser('creer', 'page', $id, $qui, $opt);

	return $autoriser;
}


function pages_autorisation_defaut_dist($qui) {
	return (($qui['statut'] == '0minirezo')
			AND !$qui['restreint']);
}

?>
