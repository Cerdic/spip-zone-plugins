<?php
/**
 * Plugin Simple trad
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2013 - Distribue sous licence GNU/GPL
 *
 * Utilisation des pipelines par Simple trad
 *
 * @package SPIP\Simple_trad\Pipelines
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * Si on est dans la création d'une nouvelle traduction, on vérifie s'il n'y a pas déjà une traduction pour 
 * chaque langue, si c'est le cas, on ne permet pas d'éditer le formulaire et on propose
 * un lien vers les autres traductions.
 * 
 * @param array $flux 
 * 		Le contexte d'environnement du pipeline
 * @return array $flux 
 * 		Le contexte d'environnement modifié
 */
function simple_trad_formulaire_charger($flux){
	if(isset($flux['args']['form']) && $flux['args']['form'] == 'editer_article'
		&& ($id_trad = _request('lier_trad')) || (isset($flux['args'][3]) && $id_trad = $flux['args'][3])){
		$langues_dispos = explode(',',$GLOBALS['meta']['langues_multilingue']);
		$article_origine = sql_fetsel('id_article,id_trad','spip_articles','id_article='.intval($id_trad));
		if(is_array($article_origine)){
			if(intval($article_origine['id_trad']) > 0)
				$article = $article_origine['id_trad'];
			else
				$article = $article_origine['id_article'];
		}

		$compte_trad = sql_countsel('spip_articles','id_trad='.intval($article));
		if($compte_trad == count($langues_dispos)){
			$articles = sql_select('id_article,lang','spip_articles','id_trad='.intval($article));
			$urls = '';
			$compte_article = 0;
			while($article = sql_fetch($articles)){
				$compte_article++;
				$urls .= '<a href="'.generer_url_entite($article['id_article'],'article').'">'.traduire_nom_langue($article['lang']).'</a>';
				if($compte_article != $compte_trad)
					$urls .= ', ';
			}
			$flux['message_erreur'] = _T('simpletrad:erreur_traduction_langues_completes',array('urls'=>$urls));
			$flux['editable'] = false;
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * 
 * Insère le champ "lang" si on a un id_trad dans l'environnement
 *
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function simple_trad_editer_contenu_objet($flux){
	/**
	 * Uniquement si on est sur un article (pour l'instant) et que l'on a id_trad dans le contexte
	 */
	if (($flux['args']['type'] == "article") && isset($flux['args']['contexte']['id_trad']) && intval($flux['args']['contexte']['id_trad']) > 0){
		$langues_dispos = explode(',',$GLOBALS['meta']['langues_multilingue']);
		/**
		 * On ajoute le formulaire de langue sur les articles
		 */
		if(count($langues_dispos)>1){
			$saisie_langue = recuperer_fond('formulaires/selecteur_langue_simple',array('langues_dispos'=>$langues_dispos,'id_article'=>$flux['args']['contexte']['id_article'],'id_trad'=>$flux['args']['contexte']['id_trad']));
			$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer editer_titre.*<\/li>),Uims","\\1".$saisie_langue,$flux['data'],1);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_insertion (SPIP)
 * 
 * A la création d'un article on vérifie si on nous envoie la langue
 * si oui on la met correctement dès l'insertion
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux 
 * 		Le contexte modifié 
 */
function simple_trad_pre_insertion($flux){
	/**
	 * Dans le public, on utilise changer_lang plutôt que lang
	 */
	if(($flux['args']['table'] == 'spip_articles') && _request('changer_lang')){
		$flux['data']['lang'] = _request('changer_lang');
		$flux['data']['langue_choisie'] = 'oui';
	}
	/**
	 * Restaurer l'auteur original
	 */
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	if(($flux['args']['table'] == 'spip_articles') && _request('lier_trad') && intval(_request('lier_trad')) > 0 && lire_config('simple_trad/recuperer_auteur','on') == 'on'){
		$id_auteur = sql_getfetsel('auteur.id_auteur','spip_auteurs as auteur LEFT JOIN spip_auteurs_liens as lien ON auteur.id_auteur=lien.id_auteur LEFT JOIN spip_articles as art ON lien.objet="article" AND lien.id_objet=art.id_article','art.id_article='.intval( _request('lier_trad')));
		if($id_auteur)
			set_request('id_auteur',$id_auteur);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition (SPIP)
 * 
 * A la modification d'un article on vérifie si on nous envoie la langue
 * si elle est différente de celle de l'article on la change
 *
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux 
 * 		Le contexte modifié 
 */
function simple_trad_pre_edition($flux){
	if(($flux['args']['spip_table_objet'] == 'spip_articles') && _request('changer_lang') && _request('changer_lang') != 'herit'){
		$flux['data']['lang'] = _request('changer_lang');
		$flux['data']['langue_choisie'] = 'oui';
	}
	
	return $flux;
}
	