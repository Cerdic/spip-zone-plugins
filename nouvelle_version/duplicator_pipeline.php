<?php

/***************************************************************************\
 * Plugin Nouvelle Version pour Spip 2.0
 * Licence GPL (c) 2011
 * Modération de la nouvelle version d'un article
 *
\***************************************************************************/


function duplicator_boite_infos($flux){
	$type = $flux['args']['type'];

	if (($id = intval($flux['args']['id'])) && ($type=='article'))
		if(!($idorig = $flux['args']['row']['version_of'])){
		$flux['data'] .= icone_horizontale(_T('duplicator:icone_dupliquer_article'), generer_url_ecrire('duplicator_article',"id_objet=$id&type=$type"), "",_DIR_PLUGIN_DUPLICATOR."images/article_new_version-24.png", false);
		}
		else
		{
			//// on verifie les autorisations de publications sur l'article d'origine
			if(autoriser('modifier','article',$flux['args']['row']['version_of'])){
			$flux['data'] .= icone_horizontale(_T('duplicator:icone_remplacer_article'), generer_url_ecrire('remplacer_article',"id_objet=$id&id_orig=$idorig&type=$type"), "",_DIR_PLUGIN_DUPLICATOR."images/article_new_version-24.png", false);
			}
		}
	//}
	return $flux;
}
?>
