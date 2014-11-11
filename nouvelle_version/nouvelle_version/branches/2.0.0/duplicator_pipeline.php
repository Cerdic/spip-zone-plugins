<?php

/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 2.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/

function duplicator_boite_infos($flux){
	$type = $flux['args']['type'];
	$statut = $flux['args']['row']['statut'];
	/// Existe t il une version de l'article déjà en cours d'édition
	$row = sql_fetsel("id_article,statut", "spip_articles", "version_of=".$flux['args']['id']." AND statut!='archi' AND  statut!='poubelle'");
	/// Si oui, on ne propose pas d'en recréer une mais de se rendre sur cette version:
	if($idversion = intval($row['id_article']))
	{
	$flux['data'] .= icone_horizontale(_T('duplicator:se_rendre_sur_la_version'), generer_url_ecrire('articles',"id_article=$idversion"), "",_DIR_PLUGIN_DUPLICATOR."images/voir_new_version-24.png", false);
	}
	else
	{
	///// Sinon si il n'existe pas d'autre version on propose de la créer uniquement si l'article est publié
	if (($id = intval($flux['args']['id'])) && ($type=='article') )
		if(!($idorig = $flux['args']['row']['version_of']) && ($statut=='publie')){
		$flux['data'] .= icone_horizontale(_T('duplicator:icone_dupliquer_article'), generer_url_ecrire('duplicator_article',"id_objet=$id&type=$type"), "",_DIR_PLUGIN_DUPLICATOR."images/article_new_version-24.png", false);
		}
		else if(($idorig = $flux['args']['row']['version_of']) && ($statut!='poubelle') && ($statut!='archi'))
		{
			///// Sinon enfin, si on est sur une nouvelle version on propose de la publier
			//// on verifie les autorisations de publications sur l'article d'origine
			if(autoriser('modifier','article',$flux['args']['row']['version_of'])){
			$flux['data'] .= icone_horizontale(_T('duplicator:icone_remplacer_article'), generer_url_ecrire('remplacer_article',"id_objet=$id&id_orig=$idorig&type=$type"), "",_DIR_PLUGIN_DUPLICATOR."images/publier_new_version-24.png", false);
			$flux['data'] .= icone_horizontale(_T('duplicator:se_rendre_sur_l_original'), generer_url_ecrire('articles',"id_article=$idorig"), "",_DIR_PLUGIN_DUPLICATOR."images/voir_old_version-24.png", false);
			$flux['data'] .= "<script>
			$('.publie').hide();
			</script>
			";
			}
		}
	}
	return $flux;
}
?>

