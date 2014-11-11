<?php
/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 3.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

/**
 * Remplace un article par sa nouvelle version
 * - Update la rubrique parente en depubliant/republiant l'article d'origine
 * - Intervertit les auteurs
 * - Libère les 2 article de l'édition de l'auteur courant
 */
function remplacer_article($article,$article_orig,$newstatut='poubelle'){

	include_spip('action/editer_article');
	include_spip('inc/modifier_article');
	include_spip('inc/modifier');

	//// si le plugin CIARCHIVE est actif on applique le statut 'archive'
	if (test_plugin_actif('ciarchive'))
	$newstatut='archive';
	
	//// indispensable include pour liberer les fichiers
	include_spip('inc/drapeau_edition');
	
	//// SQL pour récuperer les contenus des articles
	$champs = array('*');
	$from = 'spip_articles';
	$where = array( "id_article=".$article);
	$whereOrig = array( "id_article=".$article_orig);
	
	/// si à l'avenir on veut s'attaquer aux rubriques ..
	$type = 'article';
	
	/// On execute le SQL articles
	$infosArticle = sql_allfetsel($champs, $from, $where);
	$infosArticleOrig = sql_allfetsel($champs, $from, $whereOrig);
	
	/// SQL pour récuperer les auteurs des articles
	$champs_auteur = array('id_auteur');
	$from_auteur = 'spip_auteurs_liens';
	$where_auteur = array( "id_objet=".$article." AND objet='".$type."'");
	$whereOrig_auteur = array( "id_objet=".$article_orig." AND objet='".$type."'");
	
	/// On execute le SQL auteurs
	$infosAuteurArticle = sql_allfetsel($champs_auteur, $from_auteur, $where_auteur);
	$infosAuteurArticleOrig = sql_allfetsel($champs_auteur, $from_auteur, $whereOrig_auteur);
	
	
	// On choisi les champs que l'on veut conserver
	// Et on garde les valeurs dans deux tableaux distincts pour l'article et l'article d'origine
	// NB: On recupère aussi les statuts
	$champs_dupliques = array('surtitre','titre','soustitre','id_rubrique','descriptif','chapo','texte','ps','date','id_secteur','maj','export','statut','date_redac','accepter_forum','date_modif','lang','langue_choisie','id_trad','extra','nom_site','url_site');
	foreach ($champs_dupliques as $key => $value) {
		$infos_de_l_article[$value] = $infosArticle[0][$value];
		$infos_de_l_article_orig[$value] = $infosArticleOrig[0][$value];
	}

	// On les update les deux avec les infos de l'autre via la fontion articles_set de spip
		articles_set($article_orig,$infos_de_l_article);
		articles_set($article,$infos_de_l_article_orig);

	/// On update la rubrique parente sur ses champs maj et date en repassant article_orig à publie 
		instituer_article($article_orig, array('statut'=>'publie','id_parent'=>$infos_de_l_article['id_rubrique']) );
	
	/// On passe article à archi si le statut existe
		instituer_article($article, array('statut'=>$newstatut,'id_parent'=>$infos_de_l_article_orig['id_rubrique']) );
			
	/// On update les deux articles avec les bons auteurs 
		$maj_auteur_article_orig = sql_updateq("spip_auteurs_liens", $infosAuteurArticle[0], "id_objet=".$article_orig." AND objet='".$type."'");
		$maj_auteur_article = sql_updateq("spip_auteurs_liens", $infosAuteurArticleOrig[0], "id_objet=".$article." AND objet='".$type."'");
		
		
	
	//DEBLOQUAGE DES 2 ARTICLES pour l'auteur courant
		debloquer_edition($GLOBALS['visiteur_session']['id_auteur'], $article, 'article');
		debloquer_edition($GLOBALS['visiteur_session']['id_auteur'], $article_orig, 'article');	
	
	return $id_article;
}
