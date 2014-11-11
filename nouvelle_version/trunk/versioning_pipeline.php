<?php

/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 3.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

function versioning_boite_infos($flux){
	/*********************************************
	Pour la création des icônes horizontales, 
	on convoque inc/presentation 
	**********************************************/
	include_spip('inc/presentation');
	
	/**************************************************************
	On récupère les infos du flux. 
	Ai dû rajouter version_of et statut dans les paramètres d'appel 
	de la pipeline dans squelette de l'espace privé   
	***************************************************************/
	
	$id_cur = $flux['args']['id'];
	$version_of = $flux['args']['version_of'];
	$type = $flux['args']['type'];
	$statut = $flux['args']['statut'];

	/// Existe t il une version de l'article déjà en cours d'édition
	$row = sql_fetsel("id_article,statut", "spip_articles", "version_of=".$flux['args']['id']." AND statut!='archi' AND  statut!='poubelle'");
	
	/// Si oui, on ne propose pas d'en recréer une mais de se rendre sur cette version:
	if($idversion = intval($row['id_article']))
	{
		$flux['data'] .= icone_horizontale(_T('versioning:se_rendre_sur_la_version'), generer_url_ecrire('article',"id_article=$idversion"), "",_DIR_PLUGIN_VERSIONING."images/voir_new_version-24.png", false);
	}
		else
	{
	///// Sinon si il n'existe pas d'autre version on propose de la créer uniquement si l'article est publié
		if (($id = intval($id_cur)) && ($type=='article') )
			if(!($idorig = $version_of) && ($statut=='publie')){
				$flux['data'] .= icone_horizontale(
				_T('versioning:icone_dupliquer_article'), 
				generer_url_ecrire('versioning_article',"id_objet=$id&type=$type"), 
				"",
				_DIR_PLUGIN_VERSIONING."images/article_new_version-24.png", 
				false);
			}
			else if(($idorig = $version_of) && ($statut!='poubelle') && ($statut!='archi'))
			{
				///// Sinon enfin, si on est sur une nouvelle version on propose de la publier
				//// on verifie les autorisations de publications sur l'article d'origine
				if(autoriser('modifier','article',$version_of)){
				$flux['data'] .= icone_horizontale(
					_T('versioning:icone_remplacer_article'), 
					generer_url_ecrire('remplacer_article',"id_objet=$id&id_orig=$idorig&type=$type"), 
					"",
					_DIR_PLUGIN_VERSIONING."images/publier_new_version-24.png", 
					false
				);
				$flux['data'] .= icone_horizontale(
					_T('versioning:se_rendre_sur_l_original'), 
					generer_url_ecrire('article',"id_article=$idorig"), 
					"",
					_DIR_PLUGIN_VERSIONING."images/voir_old_version-24.png", 
					false
				);
				$flux['data'] .= "<script>$(\"select.statut option[value='publie']\").remove();</script>";
			}
		}
	}
	return $flux;
}
?>

