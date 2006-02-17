<?php

/***************************************************************************\
*  SPIP, Systeme de publication pour l'internet                           *
*                                                                         *
*  Copyright (c) 2001-2006                                                *
*  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
*                                                                         *
*  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
*  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once('inc_table_externe.php');
include_ecrire("exec_articles_edit"); // la version native de spip



function affiche_articles_edit($flag_editable, $id_article, $id_rubrique, $titre, $soustitre, $surtitre, $descriptif, $url, $chapo, $texte, $ps, $new, $nom_site, $url_site, $extra, $id_secteur, $date, $onfocus, $lier_trad, $champs_article)
{
	
	/*
	global $champs_extra;
	debut_page(_T('titre_page_articles_edit', array('titre' => $titre)), "documents", "articles", "hauteurTextarea();");

	debut_grand_cadre();

	afficher_hierarchie($id_rubrique);

	fin_grand_cadre();

	debut_gauche();

	//
	// Pave "documents associes a l'article"
	//

	if ($new != 'oui'){
		# modifs de la description d'un des docs joints
		if ($flag_editable) maj_documents($id_article, 'article');

		# affichage
		afficher_documents_colonne($id_article, 'article', $flag_editable);
	}
	$GLOBALS['id_article_bloque'] = $id_article;	// globale dans debut_droite
	debut_droite();
	debut_cadre_formulaire();

	formulaire_articles_edit($id_article, $id_rubrique, $titre, $soustitre, $surtitre, $descriptif, $url, $chapo, $texte, $ps, $new, $nom_site, $url_site, $extra, $id_secteur, $date, $onfocus, $lier_trad,
	$champs_article);
*/
	formulaire_articles_edit_externe($id_article);
	
	fin_cadre_formulaire();

	fin_page();
}

?>