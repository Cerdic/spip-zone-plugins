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
 *  puis appelle explicitement la méthode articles_new_version
 */
function exec_articles_new_version_dist()
{
	createIfNotExistColumnVersionOf();
	articles_new_version(_request('id_article'));	
}

/*
 * Cree une copie d'un article avec le statut en cours d'edition
 * associée à l'auteur connecté 
 */
function articles_new_version($id_article)
{
	/* 
	 * On récupère l'article originale.
	 * Quelque soit le profil, car un meme 
	 * un rédacteur peut créer une nouvelle 
	 * version d'un article publié. 
	 */
	$article_orig = infos_article_propre(article_select_tout_profil($id_article));
	
	/* On récupère l'id de l'utilisateur connecté */
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'] ;

	$surtitre = $article_orig['surtitre'];
	$titre = $article_orig['titre'];
	$soustitre = $article_orig['soustitre'];
	$id_rubrique = $article_orig['id_rubrique'];
	$descriptif = $article_orig['descriptif'];
	$chapo = $article_orig['chapo'];
	$texte = $article_orig['texte'];
	$ps = $article_orig['ps'];
	$date = $article_orig['date'];	
	$id_secteur = $article_orig['id_secteur'];
	$maj = $article_orig['maj'];
	$export = $article_orig['export'];
	$date_redac = $article_orig['date_redac'];
	$visites = $article_orig['visites'];
	$referers = $article_orig['referers'];
	$popularite = $article_orig['popularite'];
	$accepter_forum = $article_orig['accepter_forum'];
	$date_modif = $article_orig['date_modif'];
	$lang = $article_orig['lang'];
	$langue_choisie = $article_orig['langue_choisie'];
	$id_trad = $article_orig['id_trad'];
	$extra = $article_orig['extra'];
	$idx = $article_orig['idx'];
	$id_version = $article_orig['id_version'];
	$nom_site = $article_orig['nom_site'];
	$url_site = $article_orig['url_site'];
	$url_propre = $article_orig['url_propre'];
	$version_of = $id_article;
	
	$statut = "prepa"; // Le nouvel article aura le statut "en cours d'edition"
	
	
	// Copie les données de l'article en cours dans un nouvel article  
	// Insère la copie en base de données
	$new_id_article = spip_abstract_insert('spip_articles', // nom de la table 
										   "(surtitre,titre,soustitre,id_rubrique,descriptif,chapo,texte,ps,date,
										    statut,id_secteur,maj,export,date_redac,visites,referers,popularite,
										    accepter_forum,date_modif,lang,langue_choisie,id_trad,extra,idx,id_version,
										    nom_site,url_site,url_propre,version_of)" , // Champs de la table
										   "($surtitre,$titre,$soustitre,$id_rubrique,$descriptif,$chapo,$texte,$ps,$date,
										    '$statut',$id_secteur,$maj,$export,$date_redac,$visites,$referers,$popularite,
										    $accepter_forum,$date_modif,$lang,$langue_choisie,$id_trad,$extra,$idx,$id_version,
										    $nom_site,$url_site,$url_propre,$version_of)" // Valeurs à insérer	
										  )	;		
	
	if($new_id_article != 0) // Si l'insertion s'est bien produite
	{			
		// Associer l'auteur (ici utilisateur connecté) avec la nouvelle copie 
		//spip_query("INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES ($connect_id_auteur, $new_id_article)");
		
		spip_abstract_insert('spip_auteurs_articles',
							"(id_auteur, id_article)",
							"($connect_id_auteur,$new_id_article)"
							);					
		
		// Associer les mots clefs et documents
		getDocEtMotCle($version_of,$new_id_article);
	
		// Redirige vers la page d'édition avec l'id du nouvel article (copie) 			

		header("Location: " . $GLOBALS['meta']['adresse_site'] . "/ecrire/?exec=articles_edit&id_article=$new_id_article");
	}
	else
	{
		echo "Echec lors de la creation du nouvel article avec pour id " . $new_id_article ;
	}
	/*
	//showArticleInfo($article_orig);
	*/
}


/*
 * Permet de récupérer les mots clés et documents
 * d'un article original pour les associer à la nouvelle copie
 * de ce dernier
 */
function getDocEtMotCle($id_origin,$id_new)
{
	// Récupération des documents de l'article original
	$docs = spip_query("SELECT * FROM spip_documents_articles WHERE id_article=$id_origin");
	
	while($list_docs = spip_fetch_array($docs))
	{
		$id_document = $list_docs['id_document'];
		spip_query("INSERT INTO spip_documents_articles (id_document,id_article) VALUES($id_document,$id_new)");
		//echo "Document numero : " . $id_document . " \n";
	}

	// Récupération des mots cles de l'article original
	$mots_cles = spip_query("SELECT * FROM spip_mots_articles WHERE id_article=$id_origin");
	
	while($list_mots = spip_fetch_array($mots_cles))
	{
		$id_mot = $list_mots['id_mot'];
		spip_query("INSERT INTO spip_mots_articles (id_mot,id_article) VALUES($id_mot,$id_new)");
		//echo "Mot numero : " . $id_mot ." \n";
	}
}

/*
 * Permet de créer la colonne version_of si elle n'existe pas
 * dans la table spip_articles
 */
function createIfNotExistColumnVersionOf()
{
	$x = spip_query("SHOW columns FROM spip_articles");	  
	
	$champ_trouve = false;
	
	// Vérification de l'existence de la colonne version_of	
	while($r = spip_fetch_array($x))
	{	
		$champ_versionOf = $r['Field'] ;
		
		if(strtolower($champ_versionOf) == 'version_of')
		{
			$champ_trouve = true;
			break;
		}
	}	

	// Action à effectuer si le champ version_of n'existe pas 
	if(!$champ_trouve)
	{
		spip_query("ALTER TABLE spip_articles ADD version_of BIGINT( 21 ) NULL");
	}	
}

?>