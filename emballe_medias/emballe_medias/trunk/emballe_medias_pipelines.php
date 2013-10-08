<?php
/**
 * Plugin Emballe Medias / Wrap medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2013 - Distribue sous licence GNU/GPL
 *
 * Insertions dans les pipelines de SPIP et d'autres plugins
 *
 * @package SPIP\Emballe_medias\Pipelines
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
		include_spip('inc/config');
		if((lire_config('emballe_medias/types/gerer_types') == 'on') && (lire_config('emballe_medias/types/gerer_modifs_types') == 'on')){
			$flux['data'] .= recuperer_fond('prive/emballe_media_affiche_gauche',$flux['args']);
		}
	}
	return $flux;
}

function emballe_medias_em_types($array){
	include_spip('inc/config');
	$array['IMAGE'] = lire_config('emballe_medias/fichiers/fichiers_images',array('jpg','gif','png'));
	$array['VIDEO'] = lire_config('emballe_medias/fichiers/fichiers_videos',array('flv'));
	$array['SON'] = lire_config('emballe_medias/fichiers/fichiers_audios',array('mp3'));
	$array['TEXTE'] = lire_config('emballe_medias/fichiers/fichiers_textes',array('doc','docx','pdf','odt'));

	return $array;
}

function emballe_medias_diogene_traiter($flux){
	if(isset($flux['args']['valeurs']['objet_type'])){
		$flux['data']['redirect'] = parametre_url($flux['data']['redirect'],'em_type',$flux['args']['valeurs']['objet_type']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets (plugin Diogene)
 * 
 * On ajoute les champs qui peuvent être pris en compte pour emballe_medias
 * Pour ce faire on reproduit le même tableau que pour les articles
 * Attention : il est nécessaire que le plugin emballe_medias soit appelé après ceux qui remplissent 
 * le tableau pour les articles => on utilise la balise <utilise> dans le plugin.xml
 *
 * @param array $flux Un tableau des champs déjà ajouté
 * @return array $flux Le tableau modifié
 */
function emballe_medias_diogene_objets($flux){
	if(!is_array($flux['emballe_media']))
		$flux['emballe_media'] = $flux['article'];
	else
		$flux['emballe_media'] = array_merge($flux['emballe_media'],$flux['article']);
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
	if(isset($flux['args']['form'])){
		$form = $flux['args']['form'];
		if ($form == 'editer_diogene'){
			$diogene_orig = _request('id_diogene');
			if(($flux['data']['objet']== 'emballe_media') && ($diogene = sql_getfetsel('id_diogene','spip_diogenes',"objet = 'emballe_media' AND id_diogene != ".intval($diogene_orig)))){
				$flux['data']['editable'] = false;
				$flux['data']['message_erreur'] = _T('emballe_medias:erreur_diogene_multiple');
			}
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
	if(isset($flux['args']['form'])){
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
		if ($form == 'configurer_emballe_medias_fichiers'){
			/**
			 * On vérifie le formulaire de configuration des fichiers
			 */
			$max_size = str_replace('M','',@ini_get('upload_max_filesize'));
			$max_post = str_replace('M','',@ini_get('post_max_size'));
			if(is_numeric($max_size) && ($max_size > 0) && is_numeric($max_post) && ($max_post > 0)){
				if(_request('file_size_limit') && ((_request('file_size_limit') > $max_size) OR (_request('file_size_limit') > $max_post))){
					$flux['data']['file_size_limit'] = _T('emballe_medias:erreur_filesize_limit',array('taille_max' => $max_size));
				}
			}
			if(count($flux['data']) > 0){
				$flux['data']['message_erreur'] = _T('emballe_medias:verifier_formulaire');
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * On invalide le cache lors des configurations
 *
 * @param $flux
 */
function emballe_medias_formulaire_traiter($flux){
	if(isset($flux['args']['form'])){
		$form = $flux['args']['form'];
		if ($form == 'configurer_emballe_medias_fichiers'){
			include_spip('inc/invalideur');
			suivre_invalideur('1');
			if((_request('gerer_types') == 'on') && !is_array(lire_config('emballe_medias/types/types_dispos'))){
				$config = lire_config('emballe_medias/types',array());
				$config['types_dispos'] = array('audio','image','texte','video');
				include_spip('inc/metas');
				ecrire_config('emballe_medias/types', $config);
			}
			/**
			 * On redirige le formulaire pour rafraichir la page
			 */
			$flux['data']['redirect'] = self();
		}
		/**
		 * Sur les diogene emballe medias, si on a le statut poubelle, on met editable à false et redirect à false
		 * 
		 */
		else if (($form == 'editer_article')
			&& (_request('type_diogene') == 'emballe_media')){
			$statut = sql_getfetsel('statut','spip_articles','id_article='.intval($flux['data']['id_article']));
			if($statut == 'poubelle'){
				$flux['data']['editable'] = false;
				$flux['data']['redirect'] = false;
			}
			$flux['data']['message_ok'] .= '<script type="text/javascript">if (window.jQuery) jQuery(".description_'.$objet.',.diogene_'._request('id_diogene').'").ajaxReload();</script>';
		}
		else if($form == 'em_supprimer_media'){
			$flux['data']['editable'] = false;
			$flux['data']['redirect'] = false;
			$diogene_media = sql_getfetsel('id_diogene','spip_diogenes','objet="emballe_media"');
			$flux['data']['message_ok'] .= '<script type="text/javascript">if (window.jQuery) jQuery(".description_emballe_media,.diogene_'.$diogene_media.'").ajaxReload();</script>';
		}
	}
	return $flux;
}
/**
 * Insertion dans le pipeline insert_head (SPIP)
 *
 * @param string $flux 
 * 		Le contenu html de la balise #INSERT_HEAD
 * @return string $flux
 * 		Le contenu html de la balise #INSERT_HEAD modifié
 */
function emballe_medias_insert_head($flux){
	if(!defined('_DIR_PLUGIN_DOCUMENTATION'))
		$flux .= "<script type='text/javascript'><!--
var ajax_image_searching = \n'<img src=\'".url_absolue(chemin_image("searching.gif"))."\' alt=\'\' />';
//--></script>";
	return $flux;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * Ajout de scripts javascripts dans le head
 * 
 * On ajoute jquery.cookie pour les tabs (cf jqueryUI)
 *
 * @param array $plugins 
 * 		Un tableau des scripts déjà demandés
 * @return array $plugins 
 * 		Le tableau modifié avec les scripts que l'on souhaite 
 */
function emballe_medias_jquery_plugins($plugins){
	if(!test_espace_prive())
		$plugins[] = "javascript/jquery.cookie.js";
	return $plugins;
}

/**
 * Insertion dans le pipeline jqueryui_forcer (plugin jQueryUI)
 * 
 * On ajoute le chargement des js pour les tabs
 * 
 * @param array $plugins Un tableau des scripts déjà demandé au chargement
 * @return array $plugins Le tableau complété avec les scripts que l'on souhaite 
 */
function emballe_medias_jqueryui_plugins($plugins){
	if(!test_espace_prive()){
		$plugins[] = "jquery.ui.tabs";
	}
	return $plugins;
}

/**
 * Insertion dans le pipeline post-edition (SPIP)
 * 
 * Lorsqu'un document est modifié on vérifie s'il est attaché à un article du secteur d'emballe medias
 * Si oui :
 * - On modifie la date de mise à jour des articles auxquels il est lié si l'article a été mis à jour il y a plus de 10 minutes
 * 
 * Lorsqu'un article est mis à la poubelle, on regard s'il est dans le secteur du diogène d'emballe medias,
 * si oui, on supprime les documents qui sont liés à cet article
 * 
 * @param $flux array : le contexte du pipeline
 * @return $flux array : le contexte du pipeline modifié s'il y a lieu
 */
function emballe_medias_post_edition($flux){
	if(($flux['args']['table'] == 'spip_documents') && ($flux['args']['action'] == 'modifier')){
		$diogenes = sql_select('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));
		if($diogenes > 0){
			while($diogene = sql_fetch($diogenes)){
				$secteurs[] = $diogene['id_secteur'];
			}
			if(is_array($secteurs)){
				$articles_lies = sql_select('*','spip_articles AS art left join spip_documents_liens AS doc_liens ON art.id_article = doc_liens.id_objet AND doc_liens.objet="article"','id_document='.intval($flux['args']['id_objet']).' AND '.sql_in('art.id_secteur',$secteurs));
				include_spip('action/editer_article');
				while($article_lie = sql_fetch($articles_lies)){
					if(strtotime($article_lie['maj']) < (time() - 600)){
						$c['date_modif'] = date('Y-m-d H:i:s');
						article_modifier($article_lie['id_article'],$c);
					}
				}
			}
		}
	}
	else if(($flux['args']['table'] == 'spip_articles') && ($flux['args']['action'] == 'instituer')){
		if(isset($flux['data']['statut']) && $flux['data']['statut'] == 'poubelle'){
			$id_secteur = sql_getfetsel('id_secteur','spip_articles','id_article='.intval($flux['args']['id_objet']));
			$id_diogene = sql_getfetsel('id_diogene','spip_diogenes','objet = "emballe_media" AND id_secteur='.intval($id_secteur));
			if($id_diogene){
				include_spip('action/dissocier_document');
				$docs_lies = sql_select('id_document','spip_documents_liens','objet="article" AND id_objet='.intval($flux['args']['id_objet']));
				while($document = sql_fetch($docs_lies)){
					supprimer_lien_document($document['id_document'], 'article', $flux['args']['id_objet'],true);
				}
			}
		}
	}

	return $flux;
}
?>
