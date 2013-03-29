<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Insertions dans les pipelines de SPIP et d'autres plugins
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_gauche
 * (Changer le type des articles)
 *
 * @param array $flux L'environnement passé par le pipeline
 * @return array $flux L'environnement complêté
 */

function emballe_medias_affiche_gauche($flux){
	if($flux['args']['exec'] == 'articles'){
		if((lire_config('emballe_medias/types/gerer_types') == 'on') && (lire_config('emballe_medias/types/gerer_modifs_types') == 'on')){
			$flux['data'] .= recuperer_fond('prive/emballe_media_affiche_gauche',$flux['args']);
		}
	}
	return $flux;
}

function emballe_medias_em_types($array){
	$array['IMAGE'] = lire_config('emballe_medias/fichiers/fichiers_images',array('jpg','gif','png'));
	$array['VIDEO'] = lire_config('emballe_medias/fichiers/fichiers_videos',array('flv'));
	$array['SON'] = lire_config('emballe_medias/fichiers/fichiers_audios',array('mp3'));
	$array['TEXTE'] = lire_config('emballe_medias/fichiers/fichiers_textes',array('doc','pdf','odt'));

	return $array;
}

function emballe_medias_diogene_traiter($flux){
	if(isset($flux['args']['valeurs']['objet_type'])){
		$flux['data']['redirect'] = parametre_url($flux['data']['redirect'],'em_type',$flux['args']['valeurs']['objet_type']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_champs_sup (plugin Diogene)
 * 
 * On ajoute les champs qui peuvent être pris en compte pour emballe_medias
 * Pour ce faire on reproduit le même tableau que pour les articles
 * Attention : il est nécessaire que le plugin emballe_medias soit appelé après ceux qui remplissent 
 * le tableau pour les articles => on utilise la balise <utilise> dans le plugin.xml
 *
 * @param array $flux Un tableau des champs déjà ajouté
 * @return array $flux Le tableau modifié
 */
function emballe_medias_diogene_champs_sup($flux){
	$flux['emballe_media'] = $flux['article'];
	$flux['emballe_media']['diogene_max'] = 1;
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * Utilisé pour le formulaire #FORMULAIRE_EDITER_DIOGENE
 * afin de limiter l'existence d'un diogène "emballe_media" pour un site
 *
 * @param $flux
 */
function emballe_medias_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if ($form == 'editer_diogene'){
		$diogene_orig = _request('id_diogene');
		if(($flux['data']['objet']== 'emballe_media') && ($diogene = sql_getfetsel('id_diogene','spip_diogenes',"objet = 'emballe_media' AND id_diogene != ".intval($diogene_orig)))){
			$flux['data']['editable'] = false;
			$flux['data']['message_erreur'] = _T('emballe_medias:erreur_diogene_multiple');
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * Utilisé pour le formulaire #FORMULAIRE_EDITER_DIOGENE
 * afin de limiter l'existence d'un diogène sur un secteur à 1 par article ou emballe_media
 *
 * @param $flux
 */
function emballe_medias_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	if ($form == 'editer_diogene'){
		/**
		 * On vérifie juste qu'un autre diogene pour article
		 * ne porte pas sur ce secteur
		 */
		$id_secteur = _request('id_secteur');
		$diogene_orig = _request('id_diogene');
		if(!$flux['data']['id_secteur'] && in_array($flux['args']['args'][1],array('article','emballe_media')) && ($diogene = sql_getfetsel('id_diogene','spip_diogenes',"objet IN ('article','emballe_media') AND id_secteur=".intval($id_secteur)." AND id_diogene != ".intval($diogene_orig)))){
			$flux['data']['id_secteur'] = _T('emballe_medias:erreur_conflit_secteur');
		}
	}
	return $flux;
}
/**
 * Insertion dans le pipeline insert_head (SPIP)
 *
 * @param array $flux 
 * @return array $flux 
 */
function emballe_medias_insert_head($flux){
	$flux .= "<script type='text/javascript'><!--";
	$flux .= "\nvar ajax_image_searching = \n'<img src=\'".url_absolue(chemin_image("searching.gif"))."\' alt=\'\' />';";
	$flux .= "//--></script>";
	return $flux;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * Ajout de scripts javascripts dans le head
 * 
 * On ajoute jquery.cookie pour les tabs (cf jqueryUI)
 *
 * @param array $plugins Un tableau des scripts déjà demandés
 * @return array $plugins Le tableau modifié avec les scripts que l'on souhaite 
 */
function emballe_medias_jquery_plugins($plugins){
	$plugins[] = "javascript/jquery.cookie.js";
	return $plugins;
}

/**
 * Insertion dans le pipeline jqueryui_forcer (plugin jQueryUI)
 * 
 * On ajoute le chargement des js pour les tabs
 * @param array $plugins Un tableau des scripts déjà demandé au chargement
 * @retune array $plugins Le tableau complété avec les scripts que l'on souhaite 
 */
function emballe_medias_jqueryui_forcer($plugins){
	$plugins[] = "jquery.ui.core";
	$plugins[] = "jquery.ui.widget";
	$plugins[] = "jquery.ui.tabs";
	return $plugins;
}

/**
 * Insertion dans le pipeline post-edition (SPIP)
 * 
 * Lorsqu'un document est modifié on vérifie s'il est attaché à un article du secteur d'emballe medias
 * Si oui :
 * - On modifie la date de mise à jour des articles auxquels il est lié si l'article a été mis à jour il y a plus de 10 minutes
 */
function emballe_medias_post_edition($flux){
	if(($flux['args']['table'] == 'spip_documents') && ($flux['args']['action'] == 'modifier')){
		$diogenes = sql_select('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));
		if($diogenes){
			while($diogene = sql_fetch($diogenes)){
				$secteurs[] = $diogene['id_secteur'];
			}
			if(is_array($secteurs)){
				$articles_lies = sql_select('*','spip_articles AS art left join spip_documents_liens AS doc_liens ON art.id_article = doc_liens.id_objet AND doc_liens.objet="article"','id_document='.intval($flux['args']['id_objet']).' AND '.sql_in('art.id_secteur',$secteurs));
				include_spip('inc/modifier');
				while($article_lie = sql_fetch($articles_lies)){
					if(strtotime($article_lie['maj']) < (time() - 600)){
						$c['date_modif'] = date('Y-m-d H:i:s');
						revision_article($article_lie['id_article'],$c);
					}
				}
			}
		}
	}
	return $flux;
}
?>