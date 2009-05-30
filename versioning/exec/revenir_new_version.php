<?php

/***************************************************************************\
 * 						Gestion du versioning 							   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/articles_edit');
include_spip('inc/article_select');
include_spip('inc/actions');
include_spip('base/abstract_sql'); // pr utiliser la m�thode spip_abstract_insert
include_spip('versioning_fonctions');

$GLOBALS['mysql_debug'] = true;

/*	R�cup�re les infos de l'article � �diter 
 *  puis appelle explicitement la m�thode revenir_new_version
 */
function exec_revenir_new_version_dist()
{	
	revenir_new_version(_request('id_article'));	
}

/*
 * Permet de revenir � la version archiv�e 
 */
function revenir_new_version($id_article_archi)
{	 
	// 1. On r�cup�re l'article archiv�e (la copie)	 
	$article_archi = infos_article_propre(article_select_tout_profil($id_article_archi));
	
	// 2. On r�cup�re l'article original dans un article temporaire
	// grace au champ version_of de la copie
	$id_article_orig = $article_archi['version_of'] ;
	$article_temp = infos_article_propre(article_select_tout_profil($id_article_orig)); 

	// 3. On met les champs de la copie dans l'original
	// sauf les champs id_article et version_of 
	$query_archi_to_orig = update_inverse_champs_articles($id_article_orig,$article_archi) ;
		
	//echo "<br/><b>Affichage de la requete de mis a jour de l'article original :</b><br/> " . $query_archi_to_orig ;
	
	spip_query($query_archi_to_orig); // Mis � jour de l'article original
	
	// 4. On met les champs de l'article temporaire dans la copie
	// sauf les champs id_article et version_of
	$query_temp_to_archi = update_inverse_champs_articles($id_article_archi,$article_temp) ;
	
	//echo "<br/><b>Affichage de la requete de mis a jour de la copie :</b><br/> " . $query_temp_to_archi ;
	
	spip_query($query_temp_to_archi); // Mis � jour de la copie
	
	// 5. On met le statut de la copie � "archiv�"
	$query_statut_archi = " UPDATE spip_articles SET statut='prepa' WHERE id_article=$id_article_archi" ;
	
	spip_query($query_statut_archi); // Mis � jour de la copie au statut en cours de r�daction
	
	// 6. On permute les mots-cl�s
	echange_mots_cles($id_article_orig,$id_article_archi);
	
	// 7. On permute les documents
	echange_documents($id_article_orig,$id_article_archi);
	
	// 8. On permute les auteurs
	echange_auteurs($id_article_orig,$id_article_archi);
	
	// On redirige vers l'article en ligne 
	header("Location: " . $GLOBALS['meta']['adresse_site'] . "/ecrire/?exec=articles&id_article=$id_article_orig");
}

?>