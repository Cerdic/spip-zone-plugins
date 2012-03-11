<?php
/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
 * @return
 */
function relecture_autoriser() {}


/**
 * Autorisation d'ouverture d'une relecture
 *
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 * @return
 */
function autoriser_article_ouvrirrelecture_dist($faire, $type, $id, $qui, $opt) {

	// Conditions :
	// - l'auteur connecte est un des auteurs de l'article
	// - l'article n'a pas deja une relecture d'ouverte

	$les_auteurs = lister_objets_lies('auteur', 'article', $id, 'auteurs_liens');

	$from = 'spip_relectures';
	$where = array("id_article=$id", "etat=" . sql_quote('ouverte'));
	$nb_relecture_ouverte = sql_countsel($from, $where);

	return (in_array($qui, $les_auteurs) AND ($nb_relecture_ouverte==0));
}


?>
