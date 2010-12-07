<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_NB_ARTICLES_BRANCHE($p)
{
	return calculer_balise_dynamique ($p, 'NB_ARTICLES_BRANCHE', array( 'id_rubrique'));
}

function  balise_NB_ARTICLES_BRANCHE_stat ($args, $filtres) {
	return $args;
}

function balise_NB_ARTICLES_BRANCHE_dyn($id_rubrique) {
	$rubriques = calcul_branche_in($id_rubrique);
	// Calcul branche
	$query = "SELECT COUNT(id_article) AS nb_articles FROM spip_articles AS articles  WHERE  (articles.statut = 'publie') AND (articles.date < '9999-12-31') AND ((articles.id_rubrique IN ($rubriques)))";

	$result = spip_query($query);
	$row = sql_fetch($result);
	return $row[nb_articles];
}

?>