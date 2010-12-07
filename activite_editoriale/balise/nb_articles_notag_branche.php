<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_NB_ARTICLES_NOTAG_BRANCHE($p)
{
	return calculer_balise_dynamique ($p, 'NB_ARTICLES_NOTAG_BRANCHE', array( 'id_rubrique'));
}

function  balise_NB_ARTICLES_NOTAG_BRANCHE_stat ($args, $filtres) {
	return $args;
}

function balise_NB_ARTICLES_NOTAG_BRANCHE_dyn($id_rubrique) {
	$rubriques = calcul_branche_in($id_rubrique);
	
	$query = "select id_groupe, titre from spip_groupes_mots";
	$result = spip_query($query);
	while($row = sql_fetch($result))
	{
		if(lire_config('activite_editoriale/groupe_' . $row[id_groupe], false))
		{
			$groupe[] = $row[id_groupe];
		}
	}
	$groupe = implode(', ', $groupe);

	$query = "select count(*) nb_articles from 
	(
		select L1.id_article from spip_articles L1 left join spip_auteurs_articles L3 on L3.id_article= L1.id_article  left join spip_mots_articles L2 on L1.id_article= L2.id_article left join spip_mots L5 on L5.id_mot = L2.id_mot
		WHERE  (L1.statut = 'publie') 
		AND (L1.date < '9999-12-31') 
		AND ((L1.id_rubrique IN ($rubriques))) 
		AND (((L5.id_groupe not IN ($groupe))) or L5.id_groupe IS NULL) 
		group by L1.id_article
	) articles";
	$result = spip_query($query);
	$row = sql_fetch($result);
	$total = $row[nb_articles];
	

	return $total;
}

?>