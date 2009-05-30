<?php

/***************************************************************************\
 * 						Gestion du versioning 							   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/articles_edit');
include_spip('inc/article_select');
include_spip('inc/actions');
include_spip('base/abstract_sql'); // pr utiliser la méthode spip_abstract_insert
include_spip('versioning_fonctions');

$GLOBALS['mysql_debug'] = true;

/*	Récupère les infos de l'article à éditer 
 *  puis appelle explicitement la méthode publier_new_version
 */
function exec_publier_new_version_dist()
{	
	publier_new_version(_request('id_article'));	
}

/*
 * Permet de publier la copie de l'article original
 * et de passé ce dernier au statut archivé
 */
function publier_new_version($id_article_copy)
{	 
	// 1. On récupère l'article courant (la copie)	 
	$article_copy = infos_article_propre(article_select_tout_profil($id_article_copy));
	
	// 2. On récupère l'article original dans un article temporaire
	// grace au champ version_of de la copie
	$id_article_orig = $article_copy['version_of'] ;
	$article_temp = infos_article_propre(article_select_tout_profil($id_article_orig)); 

	// 3. On met les champs de la copie dans l'original
	// sauf les champs id_article et version_of 
	$query_copy_to_orig = update_inverse_champs_articles($id_article_orig,$article_copy) ;
		
	//echo "<br/><b>Affichage de la requete de mis a jour de l'article original :</b><br/> " . $query_copy_to_orig ;
	
	spip_query($query_copy_to_orig); // Mis à jour de l'article original
	
	// 4. On met les champs de l'article temporaire dans la copie
	// sauf les champs id_article et version_of
	$query_temp_to_copy = update_inverse_champs_articles($id_article_copy,$article_temp) ;
	
	//echo "<br/><b>Affichage de la requete de mis a jour de la copie :</b><br/> " . $query_temp_to_copy ;
	
	spip_query($query_temp_to_copy); // Mis à jour de la copie
	
	// 5. On met le statut de la copie à "archivé"
	$query_statut_copy = " UPDATE spip_articles SET statut='archi' WHERE id_article=$id_article_copy" ;
	
	spip_query($query_statut_copy); // Mis à jour de la copie au statut archivé
	
	// 6. On permute les mots-clés
	echange_mots_cles($id_article_orig,$id_article_copy);
	
	// 7. On permute les documents
	echange_documents($id_article_orig,$id_article_copy);
	
	// 8. On permute les auteurs
	echange_auteurs($id_article_orig,$id_article_copy);
	
	// On redirige vers l'article en ligne 
	header("Location: " . $GLOBALS['meta']['adresse_site'] . "/ecrire/?exec=articles&id_article=$id_article_orig");
}

?>