<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite




function balise_NB_ARTICLES_PAR_AUTEUR($p)
{
	return calculer_balise_dynamique ($p, 'NB_ARTICLES_PAR_AUTEUR', array( 'id_secteur',  'id_rubrique',  'max',  'date_debut',  'date_fin'));
}

function  balise_NB_ARTICLES_PAR_AUTEUR_stat ($args, $filtres) {
	return $args;
}

function balise_NB_ARTICLES_PAR_AUTEUR_dyn($id_secteur, $id_rubrique, $max, $date_debut, $date_fin) {
	
	$rubriques = calcul_branche_in($id_rubrique);
	
	$query = "SELECT auteurs.id_auteur, auteurs.nom, COUNT(L2.id_article)  AS nb_articles, auteurs.nom
FROM spip_auteurs AS `auteurs`  
INNER JOIN spip_auteurs_articles AS L1 ON ( L1.id_auteur = auteurs.id_auteur ) 
INNER JOIN spip_articles AS L2 ON ( L2.id_article = L1.id_article )
WHERE (auteurs.statut != '5poubelle')
	AND (L2.id_secteur = ".$id_secteur.")
	AND (L2.date >= '".$date_debut."')
	AND (L2.date <= '".$date_fin."')
GROUP BY auteurs.id_auteur
ORDER BY nb_articles DESC";
	$result = spip_query($query);
	while($row = sql_fetch($result))
	{
		$nb_articles = $row['nb_articles'];
		$id_auteur = $row['id_auteur'];
		
		//calcul secteur
		$query2 = "SELECT COUNT(L1.id_article) AS total FROM spip_auteurs AS `auteurs`  LEFT JOIN spip_auteurs_articles AS L1 ON ( L1.id_auteur = auteurs.id_auteur ) WHERE (auteurs.statut != '5poubelle') AND (L1.id_auteur = $id_auteur)AND (L1.id_auteur = $id_auteur)";
		$result2 = spip_query($query2);
		$row2 = sql_fetch($result2);
		
		// Calcul branche
		$query3 = "SELECT COUNT(L1.id_article) AS nb_articles FROM spip_auteurs AS `auteurs`  LEFT JOIN spip_auteurs_articles AS L1 ON ( L1.id_auteur = auteurs.id_auteur )LEFT JOIN spip_articles AS articles ON ( L1.id_article = articles.id_article ) WHERE (auteurs.statut != '5poubelle') AND (L1.id_auteur = $id_auteur)AND (L1.id_auteur = $id_auteur) AND (articles.statut = 'publie') AND (articles.date < '9999-12-31') AND ((articles.id_rubrique IN ($rubriques)))";

		$result3 = spip_query($query3);
		$row3 = sql_fetch($result3);
		
		$liste[] = array('nom' => $row[nom], 'nb_articles_secteur' => $row[nb_articles], 'nb_articles_branche' => $row3[nb_articles],'nb_articles_total' => $row2[total]);
		
	}

    return array('fonds/stat_auteur', 0, array('liste' => $liste));
}
?>