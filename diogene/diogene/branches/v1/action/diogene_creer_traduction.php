<?php 
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 *
 * © 2010-2011 - Distribue sous licence GNU/GPL
 *
 * Action de création de traduction depuis l'espace public
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_diogene_creer_traduction_dist(){
	include_spip('action/editer_article');
	$arg = _request('arg');
	$lang = _request('langue_forcee');
	
	if(!is_numeric(intval($arg)) OR !$lang){
		return;
	}
	
	$contenu_article = sql_fetsel('*','spip_articles','id_article = '.intval($arg));
	
	if($contenu_article){
		$id_trad = $contenu_article['id_article'];
		if(!($id_article = sql_getfetsel('id_article','spip_articles','id_trad='.intval($id_trad).' AND lang='.sql_quote($lang)))){
		
			$id_rubrique = $contenu_article['id_rubrique'];
			
			/**
			 * Insertion de l'article dans la rubrique idoine
			 */
			$id_article = insert_article($id_rubrique);
		
			/**
			 * Mise à jour de l'article en lui donnant l'ensemble du contenu de la version originale
			 */
			$c = $contenu_article;
			unset($c['id_article']);
			unset($c['date']);
			unset($c['statut']);
			unset($c['id_rubrique']);
			unset($c['id_parent']);
			
			$c['lang'] = $lang;
			
			include_spip('inc/modifier');
			revision_article($id_article, $c);
			
			/**
			 * On donne un statut, une date et un id_parent
			 */
			$c = array(
					'date'=> date("Y-m-d H:i:s"),
					'statut' => lire_config('diogene/statuts/article_statut_defaut','prepa'),
					'id_parent' => $id_rubrique
				);
			instituer_article($id_article, $c);
			
			/**
			 * On l'associe au bon article d'origine
			 */
			$c = array(
				'lier_trad' => _request('arg')
			);
			article_referent ($id_article, $c);
			
			include_spip('action/diogene_recup_doc_trad');
			diogene_recuperer_docs_trad($id_article,$id_trad);
			
			/**
			 * On lui associe automatiquement le ou les auteurs de l'article original
			 * L'id_auteur courant est automatiquement ajouté par insert_article()
			 */
			$auteurs = sql_select('*','spip_auteurs_articles','id_article='.intval($id_trad));
			while($auteur = sql_fetch($auteurs)){
				$auteur['id_article'] = $id_article;
				sql_insertq("spip_auteurs_articles",$auteur);
			}
		}
		
		$redirect = _request('redirect');
		if(!$redirect){
			include_spip('diogene_fonctions');
			$redirect = generer_url_publier($id_article);
		}
		
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		
		include_spip('inc/headers');
		redirige_par_entete(str_replace('&amp;','&',$redirect));
	}
}

?>