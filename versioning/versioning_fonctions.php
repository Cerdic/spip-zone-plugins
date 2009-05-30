<?php
/*
 * Permet de récupérer un article en base peu importe le profil 
 * de l'utilisateur connecté
 */
function article_select_tout_profil($id_article){
	$article_orig = spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article=$id_article"));
	return $article_orig;
}

/*
 * Securisation du contenu pour l'execution sql 
 */
function infos_article_propre($article_orig)
{
	foreach($article_orig as $key => $value){
		if(!is_numeric($value)) 
		{
			$article_orig[$key] = _q($value);
		}
	}
	
	return $article_orig;
}

/*
 * Permet de voir les infos concernant un article recuperé 
 * en utilisant la méthode article_select (qui renvoie un Array).
 * Cette méthode prend en paramètre un objet de type Array
 */
function showArticleInfo($article_choisi)
{
	echo "<b><u>Debut info sur l'article :</u></b> <br/><br/>" ;
	//print_r($article_choisi);
	
	foreach($article_choisi as $key => $value)
	{
		echo "<b>" . $key . " </b> =>  " . $value . "<br/>" ;
	}
	
	echo "<b><u>Fin info sur l'article</u></b> " ;
}

/*
 * Méthode qui permet de savoir si un article 
 * est une copie d'un autre, elle vérifie si le champ
 * version_of est renseigné ou non
 */
function isACopy($id_article_courant) 
{
	// Récupère l'id de l'article original	
	$id_article_orig = getIdArticleOrig($id_article_courant); 
	
	if($id_article_orig != 0)
	{
		return true; // Si l'article courant est une copie
	}
	else
	{
		return false;
	}
}

/*
 * Permet d'inverser les champs de deux articles sauf
 * les champs id_article et version_of
 */
function update_inverse_champs_articles($id_article_a_changer, $article_source)
{
	$query = " UPDATE spip_articles SET " .
			 " surtitre=" . $article_source['surtitre'] . " , " .
			 " titre=" . $article_source['titre'] . " , " .
			 " soustitre=" . $article_source['soustitre'] . " , " .
			 " id_rubrique=" . $article_source['id_rubrique'] . " , " .
			 " descriptif=" . $article_source['descriptif'] . " , " .
			 " chapo=" . $article_source['chapo'] . " , " .
		     " texte=" . $article_source['texte'] . " , " .
			 " ps=" . $article_source['ps'] . " , " .
			 " date=" . $article_source['date'] . " , " .
			 " id_secteur=" . $article_source['id_secteur'] . " , " .
			 " maj=" . $article_source['maj'] . " , " .
			 " export=" . $article_source['export'] . " , " .
			 " date_redac=" . $article_source['date_redac'] . " , " .
			 " visites=" . $article_source['visites'] . " , " .
			 " referers=" . $article_source['referers'] . " , " .
			 " popularite=" . $article_source['popularite'] . " , " .
			 " accepter_forum=" . $article_source['accepter_forum'] . " , " .
			 " date_modif=" . $article_source['date_modif'] . " , " .
			 " lang=" . $article_source['lang'] . " , " .
			 " langue_choisie=" . $article_source['langue_choisie'] . " , " .
			 " id_trad=" . $article_source['id_trad'] . " , " .
			 " extra=" . $article_source['extra'] . " , " .
			 " idx=" . $article_source['idx'] . " , " .
			 " id_version=" . $article_source['id_version'] . " , " .
			 " nom_site=" . $article_source['nom_site'] . " , " .
			 " url_site=" . $article_source['url_site'] . " , " .
			 " url_propre=" . $article_source['url_propre'] ;
	
	$query .= " WHERE id_article = $id_article_a_changer";	
	
	return $query ;
}

/*
 * Permet d'avoir l'id de l'article original ayant permis de créer la copie
 */
function getIdArticleOrig($id_article_courant)
{
	// Récupère les informations de l'article courant
	$article_courant = article_select($id_article_courant);
	$id_version_of = $article_courant['version_of']; 
	
	return $id_version_of;
}

/*
 * Permet de savoir si l'article original est publié en ligne ou non
 */
function isArticleOrigPublished($id_article_courant)
{	
	// Récupère l'id de l'article original	
	$id_article_orig = getIdArticleOrig($id_article_courant); 
	
	if($id_article_orig != 0) // On a un article original ayant des copies
	{
		$article_orig = article_select($id_article_orig);
		$statut_article_orig = $article_orig['statut'];
		
		if($statut_article_orig == 'publie')
			return true; 
	}
	
	// Si l'article original n'est pas en ligne 
	return false ;
}

/*
 * Permet de permuter les mots-clés entre 2 articles
 */
function echange_mots_cles($id_article_orig, $id_article_copy)
{
	// 1. Récupérer tt les mots clés de l'article d'origine
	$mots_orig = spip_query("SELECT * FROM spip_mots_articles WHERE id_article=$id_article_orig");	

	// 2. Récupérer tt les mots clés de l'article copié
	$mots_copy = spip_query("SELECT * FROM spip_mots_articles WHERE id_article=$id_article_copy");
	
	// 3. Supprimer les mots-clés associés aux articles (origine & copié)
	$query_delete_mots = "DELETE FROM spip_mots_articles WHERE id_article IN ($id_article_orig,$id_article_copy)" ;
	spip_query($query_delete_mots);
	
	// 4. Insérer les mots clés récupérés en 1) et 2) avec les bons ID
		// Original -> Copy
	while($list_mots_orig = spip_fetch_array($mots_orig))
	{
		$id_mot_orig = $list_mots_orig['id_mot'];
		spip_query("INSERT INTO spip_mots_articles (id_mot,id_article) VALUES($id_mot_orig,$id_article_copy)");		
	}
	
		// Copy -> Original
	while($list_mots_copy = spip_fetch_array($mots_copy))
	{
		$id_mot_copy = $list_mots_copy['id_mot'];
		spip_query("INSERT INTO spip_mots_articles (id_mot,id_article) VALUES($id_mot_copy,$id_article_orig)");		
	}	
}

/*
 * Permet de permuter les documents entre 2 articles
 */
function echange_documents($id_article_orig, $id_article_copy)
{
	// 1. Récupérer tt les documents de l'article d'origine
	$documents_orig = spip_query("SELECT * FROM spip_documents_articles WHERE id_article=$id_article_orig");	

	// 2. Récupérer tt les documents de l'article copié
	$documents_copy = spip_query("SELECT * FROM spip_documents_articles WHERE id_article=$id_article_copy");
	
	// 3. Supprimer les documents associés aux articles (origine & copié)
	$query_delete_documents = "DELETE FROM spip_documents_articles WHERE id_article IN ($id_article_orig,$id_article_copy)" ;
	spip_query($query_delete_documents);
	
	// 4. Insérer les documents récupérés en 1) et 2) avec les bons ID
		// Original -> Copy
	while($list_documents_orig = spip_fetch_array($documents_orig))
	{
		$id_document_orig = $list_documents_orig['id_document'];
		spip_query("INSERT INTO spip_documents_articles (id_document,id_article) VALUES($id_document_orig,$id_article_copy)");		
	}
	
		// Copy -> Original
	while($list_documents_copy = spip_fetch_array($documents_copy))
	{
		$id_document_copy = $list_documents_copy['id_document'];
		spip_query("INSERT INTO spip_documents_articles (id_document,id_article) VALUES($id_document_copy,$id_article_orig)");		
	}	
}

/*
 * Permet de permuter les auteurs entre 2 articles
 */
function echange_auteurs($id_article_orig, $id_article_copy)
{
	// 1. Récupérer tt les auteurs de l'article d'origine
	$auteurs_orig = spip_query("SELECT * FROM spip_auteurs_articles WHERE id_article=$id_article_orig");	

	// 2. Récupérer tt les auteurs de l'article copié
	$auteurs_copy = spip_query("SELECT * FROM spip_auteurs_articles WHERE id_article=$id_article_copy");
	
	// 3. Supprimer les auteurs associés aux articles (origine & copié)
	$query_delete_auteurs = "DELETE FROM spip_auteurs_articles WHERE id_article IN ($id_article_orig,$id_article_copy)" ;
	spip_query($query_delete_auteurs);
	
	// 4. Insérer les auteurs récupérés en 1) et 2) avec les bons ID
		// Original -> Copy
	while($list_auteurs_orig = spip_fetch_array($auteurs_orig))
	{
		$id_auteur_orig = $list_auteurs_orig['id_auteur'];
		spip_query("INSERT INTO spip_auteurs_articles (id_auteur,id_article) VALUES($id_auteur_orig,$id_article_copy)");		
	}
	
		// Copy -> Original
	while($list_auteurs_copy = spip_fetch_array($auteurs_copy))
	{
		$id_auteur_copy = $list_auteurs_copy['id_auteur'];
		spip_query("INSERT INTO spip_auteurs_articles (id_auteur,id_article) VALUES($id_auteur_copy,$id_article_orig)");		
	}	
}

?>