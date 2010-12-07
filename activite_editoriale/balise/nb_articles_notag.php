<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite




function balise_NB_ARTICLES_NOTAG($p)
{
	return calculer_balise_dynamique ($p, 'NB_ARTICLES_NOTAG', array( 'id_secteur',  'id_rubrique',  'max',  'date_debut',  'date_fin'));
}

function  balise_NB_ARTICLES_NOTAG_stat ($args, $filtres) {
	return $args;
}

function balise_NB_ARTICLES_NOTAG_dyn($id_secteur, $id_rubrique, $max, $date_debut, $date_fin) {
	
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
	
	
	$query = "select L1.date, L1.id_article, L1.titre, L4.id_auteur, L4.nom from spip_articles L1 left join spip_auteurs_articles L3 on L3.id_article= L1.id_article left join spip_auteurs L4 on L4.id_auteur= L3.id_auteur left join spip_mots_articles L2 on L1.id_article= L2.id_article left join spip_mots L5 on L5.id_mot = L2.id_mot
	WHERE (L1.id_secteur = ".$id_secteur.")
	AND (L1.date >= '".$date_debut."')
	AND (L1.date <= '".$date_fin."')
	AND (L1.statut = 'publie') 
	AND (L1.date < '9999-12-31') 
	AND ((L1.id_rubrique IN ($rubriques)))
	AND (((L5.id_groupe not IN ($groupe))) or L5.id_groupe IS NULL) 
	group by L5.id_groupe, L1.date, L1.id_article, L1.titre, L4.id_auteur, L4.nom 
	ORDER BY L1.date DESC";
	$result = spip_query($query);
	while($row = sql_fetch($result))
	{
		$id_article = $row['id_article'];
		$id_auteur = $row['id_auteur'];
		$titre = $row['titre'];
		$nom = $row['nom'];
		$date = affdate($row['date'], 'd/m/Y');
		
		$editer = '<a href="?exec=articles&id_article=' . $id_article . '">' . $titre . '</a>';
		$auteur = '<a href="?exec=auteur_infos&id_auteur=' . $id_auteur . '">' . $nom . '</a>';
		
		$liste[] = array('id' => $id_article, 'titre' => $editer, 'nom_auteur' => $auteur, 'date' => $date);
	}

    return array('fonds/articles_notag', 0, array('liste' => $liste));
}
?>