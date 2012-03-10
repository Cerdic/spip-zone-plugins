<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function relecture_autoriser() {}

/**
 * Autorisation d'iconification d'un depot
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_xxxx_dist($faire, $type, $id, $qui, $opt) {
	return true;
}


/**
 * Ajout de l'onglet Ajouter les plugins dont l'url depend du l'existence ou pas d'un depot
 * de plugins
 *
 * @param array $flux
 * @return array
 */
function relecture_ajouter_onglets($flux) {
    return $flux;
}


/**
 * Affichage du formulaire de choix Contact/Organisation
 * dans la colonne de vue d'un auteur
 * et
 * Affichage du formulaire de recherche et de sélection d'Organisations
 * dans la colonne de vue d'une rubrique
**/
function relecture_affiche_gauche($flux) {
	return $flux;
}
?>
