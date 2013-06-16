<?php
/**
 * Plugin Diogene
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010-2012 - Distribue sous licence GNU/GPL
 *
 * Utilisation des pipelines par Diogene
 *
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline editer_contenu_objet (SPIP)
 * Insère ou enlève les champs dans le formulaire
 * que l'on souhaite ajouter ou supprimer
 *
 * @param array $flux
 * @return array
 */
function diogene_editer_contenu_objet($flux){
	$args = $flux['args'];
	$type = $args['type'];
	$langues_dispos = explode(',',$GLOBALS['meta']['langues_multilingue']);
	$pipeline = pipeline('diogene_objets', array());

	if (in_array($type,array_keys($pipeline))){
		$id_secteur = $args['contexte']['id_secteur'] ?
			$args['contexte']['id_secteur'] :
			sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.intval($args['contexte']['id_parent']));

		/**
		 * Cas des pages uniques
		 */
		if(!$id_secteur && (($args['contexte']['id_parent'] == 0) OR ($args['contexte']['id_parent'] == '-1') OR (!$args['contexte']['id_parent']  && !$args['contexte']['parents'])) && ($type=='article')){
			$id_secteur='0';
			$type = 'page';
		}
		if($type == 'article'){
			if($id_diogene = intval(_request('id_diogene'))){
				$where = "id_diogene = $id_diogene AND id_secteur=".intval($id_secteur)." AND objet IN ('article','emballe_media')";
			}else{
				$where = "id_secteur=".intval($id_secteur)." AND objet IN ('article','emballe_media')";
			}
		}
		
		if(!$where){
			if((!$id_secteur) && $pipeline[$type]['diogene_max'] == 1){
				$where = "objet=".sql_quote($type);
			}else{
				$where = "id_secteur=".intval($id_secteur)." AND objet=".sql_quote($type);
			}
		}
		if($diogene = sql_fetsel('*','spip_diogenes',$where)){
			/*
			 * On ajoute dans l'environnement les champs ajoutés par diogènes et ses sous plugins
			 */
			if(unserialize($diogene['champs_ajoutes']) == 'false'){
				$args['diogene_ajouts'] = array();
			}else{
				$args['diogene_ajouts'] = unserialize($diogene['champs_ajoutes']);
			}
			/*
			 * On ajoute dans l'environnement les options des complements
			 */
			if(unserialize($diogene['options_complements']) == 'false'){
				$args['options_complements'] = array();
			}else{
				$args['options_complements'] = unserialize($diogene['options_complements']);
			}

			/**
			 * On vire les champs que l'on ne souhaite pas
			 */
			if($diogene['objet'] == 'page'){
				$diogene['champs_caches']['id_parent'];
			}

			if(is_array($champs_caches = unserialize($diogene['champs_caches']))){
				foreach($champs_caches as $champ){
					if($champ == 'urlref'){
						$champ = 'liens_sites';
					}
					if (($champ == 'liens_sites') && preg_match(",<li [^>]*class=[\"']editer editer_($champ).*<fieldset>.*<\/fieldset>.*<\/li>,Uims",$flux['data'],$regs)){
						$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer (editer_$champ).*<fieldset>.*<\/fieldset>.*<\/li>),Uims","",$flux['data'],1);
					}
					else if (($champ != 'liens_site') && preg_match(",<li [^>]*class=[\"']editer editer_($champ).*<\/li>,Uims",$flux['data'],$regs)){
						$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer (editer_$champ).*<\/li>),Uims","",$flux['data'],1);
					}
				}
			}
			/**
			 * On ajoute ce que l'on souhaite ajouter avant le formulaire
			 */
			if($type=='page'){
				$type='article';
				$args['type'] = 'article';
				$old_type = 'page';
			}
			if (preg_match(",<div [^>]*class=[\"'][^>]*formulaire_editer_($type),Uims",$flux['data'],$regs)){
				$args['champs_ajoutes'] = $diogene['champs_ajoutes'];
				$ajouts = pipeline('diogene_avant_formulaire',array('args'=>$args,'data'=>''));
				$flux['data'] = preg_replace(",(<div [^>]*class=[\"'][^>]*formulaire_editer_$type),Uims",$ajouts."\\1",$flux['data'],1);
			}
			/**
			 * On ajoute le formulaire de langue sur les articles
			 */
			if($old_type){
				$type=$old_type;
				$args['type'] = $old_type;
			}
			if(in_array($type,array('article','page')) && (count($langues_dispos)>1)){
				$saisie_langue = recuperer_fond('formulaires/selecteur_langue',array('langues_dispos'=>$langues_dispos,'id_article'=>$args['contexte']['id_article']));
				$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer editer_titre.*<\/li>),Uims","\\1".$saisie_langue,$flux['data'],1);
			}
			/**
			 * On remplace le selecteur de rubrique par le notre dans le public
			 * On fait attention au fait qu'il y ait ou pas polyhiérarchie
			 */
			if (!test_espace_prive() && preg_match(",<li [^>]*class=[\"']editer editer_parent,Uims",$flux['data'],$regs) && (!preg_match(",<li [^>]*class=[\"']editer editer_parents,Uims",$flux['data'],$regs2) OR ($args['options_complements']['polyhier_desactiver'] == 'on'))){
				$contexte_selecteur = array(
					'id_rubrique_limite'=>$id_secteur,
					'type' => $type,
					'id_parent'=>$args['contexte']['id_parent'],
					'rubrique_principale' => $rubrique_principale);
				if($type == 'rubrique'){
					$contexte_selecteur['id_rubrique'] = $args['contexte']['id_rubrique'];
				}
				if(count($regs2) > 0){
					$class = "editer editer_parents";
					$contexte_selecteur['selecteur_type'] = "polyhier";
					$contexte_selecteur['parents'] = $args['contexte']['parents'];
				}else{
					$class = "editer editer_parent";
					$contexte_selecteur['selecteur_type'] = "normal";
				}
				$contexte_selecteur['rubrique_principale'] = 'oui';
				if($diogene['objet'] == 'emballe_media'){
					$contexte_selecteur['rubrique_principale'] = 'non';
				}
				$saisie_rubrique = recuperer_fond('formulaires/selecteur_rubrique',$contexte_selecteur);
				
				if($args['contexte']['id_parent'] > 0){
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']$class.*)(<li [^>]*class=[\"']editer.*),Uims",$saisie_rubrique."\\2",$flux['data'],1);
				}else{
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']$class.*)(<li [^>]*class=[\"']editer.*),Uims","\\2",$flux['data'],1);
				}
				if(($class == 'editer editer_parents') && ($args['options_complements']['polyhier_desactiver'] == 'on')){
					$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer editer_parent.*)(<li [^>]*class=[\"']editer.*),Uims",''."\\2",$flux['data'],1);
				}
			}else if(!test_espace_prive() && ($type != 'page') && preg_match(",<li [^>]*class=[\"']editer editer_parents,Uims",$flux['data'],$regs)){
				$contexte = $args['contexte'];
				$contexte['id_rubrique'] = $diogene['id_secteur'];
				$contexte['limite_branche'] = $diogene['id_secteur'];
				$saisie_rubrique = recuperer_fond("formulaires/inc-selecteur-parents_diogene",$contexte);
				$flux['data'] = preg_replace(",(<li [^>]*class=[\"']editer editer_parents.*)(<li [^>]*class=[\"']editer.*),Uims",$saisie_rubrique."\\2",$flux['data'],1);
			}
			
			/**
			 * On ajoute en fin de formulaire les blocs supplémentaires
			 */
			if (strpos($flux['data'],'<!--extra-->')!==FALSE){
				$saisie = pipeline('diogene_ajouter_saisies',array('args'=>$args,'data' => ''));
				/**
				 * On ajoute encore à la fin le sélecteur de statuts à la fin du formulaire
				 * Uniquement si l'on n'est pas dans le privé
				 */
				if($type=='page'){
					$type='article';
				}
				if(!test_espace_prive() && find_in_path('formulaires/selecteur_statut_'.$type.'.html')){
					$saisie .= recuperer_fond('formulaires/selecteur_statut_'.$type,$args['contexte']);
				}else if(!test_espace_prive() && find_in_path('formulaires/selecteur_statut_objet.html') AND $type != 'rubrique'){
					$args['contexte']['type'] = $type;
					$saisie .= recuperer_fond('formulaires/selecteur_statut_objet',$args['contexte']);
				}
				$flux['data'] = preg_replace(',(.*)(<!--extra-->),ims',"\\1<ul>".$saisie."</ul>\\2",$flux['data'],1);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * Charge des valeurs spécifiques dans le formulaire d'édition en cours
 * Les sous plugins peuvent se brancher sur le pipeline spécifique à Diogene : diogene_charger
 * Diogène ajoute dans le $flux de départ l'id_diogene correspondant à l'objet édité ($flux['data']['id_diogene'])
 * ainsi que l'id_diogene dans les hidden.
 * 
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function diogene_formulaire_charger($flux){
	$form = $flux['args']['form'];
	if($form == 'editer_diogene'){
		$complements = unserialize($flux['data']['options_complements']);
		$valeurs = array();
		if(is_array($complements)){
			foreach($complements as $complement => $valeur){
				if(is_array(unserialize($valeur))){
					$valeurs[$complement] = unserialize($valeur);
				}else{
					$valeurs[$complement] = $valeur;
				}
			}
		}
		if($flux['data']['objet'] == 'page'){
			$diogene_orig = _request('id_diogene');
			if($diogene = sql_getfetsel('id_diogene','spip_diogenes',"objet = 'page' AND id_diogene != ".intval($diogene_orig))){
				$flux['data']['editable'] = false;
				$flux['data']['message_erreur'] = _T('diogene:erreur_diogene_multiple_page');
			}else{
				$flux['data']['_hidden'] .= "\n<input type='hidden' name='id_secteur' value='0' />";
			}
		}
		$flux['data'] = array_merge($flux['data'],$valeurs);
	}else{
		$pipeline = pipeline('diogene_objets', array());
		if (substr($form,0,7) == 'editer_' && ($objet = substr($form,7)) && in_array($objet,array_keys($pipeline))){
			$id_table_objet = id_table_objet($objet);
			$id_objet = $flux['data'][$id_table_objet];
			$flux['data']['id_objet'] = $id_objet;
			$id_secteur = $flux['data']['id_secteur'] ? $flux['data']['id_secteur'] : false;
	
			/**
			 * Cas spécifique pour les pages uniques
			 * -* Uniquement dans l'espace public
			 */
			if(($form == 'editer_article') && !$flux['args']['id_parent']){
				$type_objet= _request('type_objet');
				$id_secteur = sql_getfetsel('id_secteur','spip_diogenes','type='.sql_quote($type_objet));
				if(!$flux['args']['id_parent'] && is_numeric($flux['args']['args'][1])){
					$flux['data']['id_parent'] = $flux['args']['args'][1];
					$id_secteur = sql_getfetsel('id_secteur','spip_rubriques','id_rubrique='.$flux['data']['id_parent']);
				}
			}
			
			if(!test_espace_prive() && (($form == 'editer_article') && ($flux['data']['id_parent'] == '-1') OR ($id_secteur == 0))){
				$flux['data']['type'] = 'page';
			}
			
			if(intval($id_secteur)){
				if($objet == 'article'){
					$id_diogene = sql_getfetsel('id_diogene','spip_diogenes','id_secteur='.intval($id_secteur).' AND objet IN ("article","emballe_media")');
				}else{
					$id_diogene = sql_getfetsel('id_diogene','spip_diogenes','id_secteur='.intval($id_secteur).' AND objet ='.sql_quote($objet));
				}
			}else{
				if($pipeline[$objet]['diogene_max'] == 1){
					$id_diogene = sql_getfetsel('id_diogene','spip_diogenes','objet ='.sql_quote($objet));
				}
			}
			
			if(intval($id_diogene)){
				$flux['data']['_hidden'] .= '<input type="hidden" name="id_diogene" value="'.$id_diogene.'" />';
				$flux['data']['id_diogene'] = $id_diogene;
			}

			$post_valeurs = pipeline('diogene_charger',
					array(
						'args' => array(
							$id_table_objet => $id_objet,
							'mode' => 'chargement',
							'valeurs' => $flux['data']
						),
						'data' => array()
					));
	
			if(is_array($post_valeurs)){
				$flux['data'] = array_merge($flux['data'],$post_valeurs);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * Vérifie les valeurs du formulaire avant leur traitement
 * Les sous plugins peuvent se brancher sur le pipeline spécifique à Diogene : diogene_verifier
 * 
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function diogene_formulaire_verifier($flux){
	$form = $flux['args']['form'];
	$pipeline = pipeline('diogene_objets', array());
	if ($objet = substr($form,7) AND in_array($objet,array_keys($pipeline))){
		
		
		if($form == 'rubrique' &&
			(_request('id_rubrique') == _request('id_parent'))){
				$flux['data']['id_parent'] = 'Vous ne pouvez pas mettre cette rubrique dans elle-même.';
		}

		$id_table_objet = id_table_objet($objet);
		$flux = pipeline('diogene_verifier',
			array(
				'args' => array(
					'erreurs' => $flux['data']
				),
				'data' => $flux['data']
			)
		);
		
		$messages = $flux;
		unset($messages['message_ok']);
		if(count($messages) > 0){
			$flux['message_erreur'] = _T('diogene:message_erreur_general');
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Insertion à la fin du traitement des formulaires
 * Les sous plugins peuvent se brancher sur le pipeline spécifique à Diogene : diogene_traiter
 * 
 * On ne s'insère que dans l'espace public
 * - On insère une redirection correcte si le statut est validé
 * - On affiche un message comme quoi l'objet a été mis à jour si c'est le cas
 * 
 * @param array $flux Le contexte d'environnement du pipeline
 * @return array $flux Le contexte d'environnement modifié
 */
function diogene_formulaire_traiter($flux){
	if(!test_espace_prive()){
		/**
		 * Cas des articles
		 */
		if(in_array($flux['args']['form'],array('editer_article'))){
			$id_article = intval($flux['data']['id_article']);
			if(!$flux['data']['page']){
				$flux['data']['page'] = _request('page');
			}
			$infos_article = sql_fetsel('*','spip_articles','id_article='.intval($id_article));
			$flux['data']['message_ok'] = _T('diogene:message_article_mis_a_jour');
			if($infos_article['statut'] == 'publie'){
				$flux['data']['redirect'] = generer_url_entite($id_article,'article');
			}else if($infos_article['statut'] == 'poubelle'){
				$flux['data']['redirect'] = parametre_url(self(),'id_article','');
			}
			$flux['data']['editable'] = true;
		}
		/**
		 * Cas des sites
		 */
		elseif($flux['args']['form'] == 'editer_site'){
			$id_site = intval($flux['data']['id_syndic']);
			$infos_site = sql_fetsel('*','spip_syndic','id_syndic='.intval($id_site));
			if($infos_site['statut'] != 'publie'){
				$flux['data']['message_ok'] = _T('diogene:message_site_mis_a_jour');
			}else{
				$flux['data']['message_ok'] = _T('diogene:message_site_mis_a_jour');
				$flux['data']['redirect'] = generer_url_entite($id_site,'site');
			}
			$flux['data']['editable'] = true;
		}
		/**
		 * Cas des rubriques
		 */
		elseif($flux['args']['form'] == 'editer_rubrique'){
			$id_rubrique = intval($flux['data']['id_rubrique']);
			$infos_rubrique = sql_getfetsel('statut','spip_rubriques','id_rubrique='.intval($id_rubrique));
			if($infos_rubrique == 'publie'){
				$flux['data']['message_ok'] = _T('diogene:message_rubrique_mis_a_jour');
				$flux['data']['redirect'] = generer_url_entite($id_rubrique,'rubrique');
				$flux['data']['editable'] = false;
			}else{
				$flux['data']['message_ok'] = _T('diogene:message_rubrique_creee');
				$flux['data']['editable'] = false;
				$flux['data']['redirect'] = parametre_url(self(),'id_rubrique',$id_rubrique);
			}
		}
		/**
		 * Cas des autres objets possibles
		 */
		elseif(
			substr($flux['args']['form'],0,7) == 'editer_'
			&& ($objet = substr($flux['args']['form'],7))
			&& $id_diogene=sql_getfetsel('id_diogene','spip_diogenes','objet='.sql_quote($objet)) 
		){
			$id_table_objet = id_table_objet($objet);
			$table_objet = table_objet_sql($objet);
			$id_objet = intval($flux['data'][$id_table_objet]);
			$statut_objet = sql_getfetsel('statut',$table_objet,$id_table_objet.'='.intval($id_objet));
			if($statut_objet == 'publie'){
				$flux['data']['redirect'] = generer_url_entite($id_objet,$objet);
				$flux['data']['editable'] = false;
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_insertion (SPIP)
 * 
 * A la creation d'un article on vérifie si on nous envoie la langue
 * si oui on la met correctement dès l'insertion
 *
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié 
 */
function diogene_pre_insertion($flux){
	if(($flux['args']['table'] == 'spip_articles') && _request('changer_lang')){
		$flux['data']['lang'] = _request('changer_lang');
		$flux['data']['langue_choisie'] = 'oui';
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition (SPIP)
 * 
 * A la modification d'un article on vérifie si on nous envoie la langue
 * si elle est différente de celle de l'article on la change
 *
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié 
 */
function diogene_pre_edition($flux){
	$pipeline = pipeline('diogene_objets', array());
	if(in_array($flux['args']['type'],array_keys($pipeline)) && ($flux['args']['action']=='modifier')){
		$flux = pipeline('diogene_traiter',$flux);
	}
	if(($flux['args']['table'] == 'spip_articles') && _request('changer_lang')){
		$flux['data']['lang'] = _request('changer_lang');
		$flux['data']['langue_choisie'] = 'oui';
	}
	
	if($flux['args']['table'] == 'spip_diogenes'){
		$champs = pipeline('diogene_champs_pre_edition',array('polyhier_desactiver','cextras_enleves'));
		if(isset($flux['data']['options_complements'])){
			$options_complements = is_array(unserialize($flux['data']['options_complements'])) ? unserialize($flux['data']['options_complements']) : array();
		}
		foreach(array('champs_ajoutes','champs_caches')
			as $array){
				if($val_array = _request($array)){
					if(is_array($val_array)){
						$flux['data'][$array] = serialize($val_array);
					}else{
						$flux['data'][$array] = $val_array;
					}
				}
		}
		foreach($champs as $champ){
			if(_request($champ)){
				if(is_array($val = _request($champ))){
					$options_complements[$champ] = serialize($val);
				}else{
					$options_complements[$champ] = $val;
				}
			}
		}
		if($flux['data']['objet'] == 'page'){
			$options_complements['polyhier_desactiver'] = 'on';
		}
		if($options_complements){
			$flux['data']['options_complements'] = serialize($options_complements);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline post_edition (SPIP)
 * 
 * On s'insère à la fin des actions d'edition (action/editer_*)
 * Notamment pour lancer un recalcul de la publication des rubriques
 *
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié 
 */
function diogene_post_edition($flux){
	/**
	 * On rejoue le calcul des rubriques car il n'a pas lieu avec le bon
	 * id_rubrique au premier coup
	 * C'est un hack ...
	 */
	if(isset($flux['data']['id_rubrique']) && ($flux['data']['statut'] == 'publie') && ($flux['args']['action'] == 'instituer')){
		$id_rubrique = $flux['data']['id_rubrique'];
		$modifs['statut'] = 'publie';
		include_spip('inc/rubriques');
		calculer_rubriques_if ($id_rubrique, $modifs,'');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline diogene_objets (plugin Diogene)
 * 
 * On ajoute les champs que l'on peut ajouter
 * 
 * @param array $flux Un tableau bidimentionnel listant les champs pouvant être ajoutés aux objets
 * @retrun array $flux Le tableau modifié
 */
function diogene_diogene_objets($flux){
	$flux['article']['champs_sup']['date'] = _T('diogene:champ_date_publication');
	if($GLOBALS['meta']['articles_redac'] !== 'non'){
		$flux['article']['champs_sup']['date_redac'] = _T('diogene:champ_date_publication_anterieure');
	}
	$flux['article']['champs_sup']['forum'] = _T('diogene:champ_forum');
	if($GLOBALS['meta']['activer_sites'] == 'oui'){
		$flux['site'] = array();
	}
	$flux['rubrique'] = array();
	if(defined('_DIR_PLUGIN_PAGES')){
		$flux['page'] = $flux['article'];
		$flux['page']['type_orig'] = 'article';
		$flux['page']['diogene_max'] = 1;
		$flux['page']['ss_rubrique'] = true;
	}
	return $flux;
}

/**
 * Insertion dans le formulaire diogene_avant_formulaire (plugin Diogene)
 * 
 * Insert des scripts javascript nécessaire au bon fonctionnement des formulaires d'édition :
 * -* prive/javascript/presentation.js
 * -* formulaires/dateur/inc-dateur.html si une date est présente dans le formulaire
 * 
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié
 */
function diogene_diogene_avant_formulaire($flux){
	$flux['data'] .= '<script type="text/javascript" src="'.find_in_path('prive/javascript/presentation.js').'"></script>';
	if(is_array(unserialize($flux['args']['champs_ajoutes'])) &&
		(in_array('date',unserialize($flux['args']['champs_ajoutes'])) || in_array('date_redac',unserialize($flux['args']['champs_ajoutes'])))){
	   	$flux['data'] .= recuperer_fond('formulaires/dateur/inc-dateur', $flux['args']);
    }
	return $flux;
}

/**
 * Insertion dans le formulaire diogene_ajouter_saisies (plugin Diogene)
 * 
 * Insert les saisies configurées dans le formulaire
 * 
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié
 */
function diogene_diogene_ajouter_saisies($flux){
	$id_article = $flux['args']['contexte']['id_article'];
	if(is_array(unserialize($flux['args']['champs_ajoutes']))){
		if(in_array('date_redac',unserialize($flux['args']['champs_ajoutes'])) && in_array('date',unserialize($flux['args']['champs_ajoutes'])) && ($GLOBALS['meta']['articles_redac'] != 'non')){
			$dates_ajoutees = 'date_full';
			if(!$flux['args']['contexte']['date']){
				list($flux['args']['contexte']['date_orig'],$flux['args']['contexte']['heure_orig']) = explode(' ',date('d/m/Y H:i',mktime()));
			}else{
				list($flux['args']['contexte']['date_orig'],$flux['args']['contexte']['heure_orig']) = explode(' ',date('d/m/Y H:i',strtotime($flux['args']['contexte']['date'])));
			}
			if($flux['args']['contexte']['date_redac']){
				list($flux['args']['contexte']['date_redac'],$flux['args']['contexte']['heure_redac']) = explode(' ',date('d/m/Y H:i',strtotime($flux['args']['contexte']['date_redac'])));
			}
		}elseif(in_array('date_redac',unserialize($flux['args']['champs_ajoutes'])) && ($GLOBALS['meta']['articles_redac'] != 'non')){
			list($flux['args']['contexte']['date_redac'],$flux['args']['contexte']['heure_redac']) = explode(' ',date('d/m/Y H:i',strtotime($flux['args']['contexte']['date_redac'])));
			$dates_ajoutees = 'date_redac';
		}elseif(in_array('date',unserialize($flux['args']['champs_ajoutes']))){
			if(!$flux['args']['contexte']['date']){
				list($flux['args']['contexte']['date_orig'],$flux['args']['contexte']['heure_orig']) = explode(' ',date('d/m/Y H:i',mktime()));
			}else{
				list($flux['args']['contexte']['date_orig'],$flux['args']['contexte']['heure_orig']) = explode(' ',date('d/m/Y H:i',strtotime($flux['args']['contexte']['date'])));
			}
			$dates_ajoutees = 'date_orig';
		}
		if($dates_ajoutees){
			$flux['args']['contexte']['dates_ajoutees'] = $dates_ajoutees;
			$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_dates',$flux['args']['contexte']);
		}
		if(in_array('forum',unserialize($flux['args']['champs_ajoutes']))){
			include_spip('inc/regler_moderation');
			if(is_numeric($id_article)){
				include_spip('formulaires/activer_forums_objet');
				$flux['args']['contexte']['forums_actuels'] = get_forums_publics($id_article);
			}
			$flux['data'] .= recuperer_fond('formulaires/diogene_ajouter_forums',$flux['args']['contexte']);
		}
	}
    return $flux;
}

/**
 * Insertion dans le formulaire diogene_verifier (plugin Diogene)
 * 
 * Vérification de saisies du formulaire
 * -* vérifie principalement les dates "date" et "date_redac"
 * -* vérifie la valeur pour le forum également
 * 
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié
 */
function diogene_diogene_verifier($flux){
	$erreurs = $flux['args']['erreurs'];

	include_spip('inc/date_gestion');
	
	if(!$erreurs['date'] && ($date = _request('date_orig'))){
		$date_orig = verifier_corriger_date_saisie('orig', 'oui', $erreurs);
	}

	if(!$erreurs['date_redac'] && ($date = _request('date_redac'))){
		$date_redac = verifier_corriger_date_saisie('redac', 'oui', $erreurs);
	}
	
	if(!$erreurs['forums'] && ($forums = _request('forums'))){
		if(!in_array($forums,array('pos','pri','abo','non'))){
			$erreurs['forums'] = _T('diogene:erreur_forums');
		}
	}
	return $flux;
}

/**
 * Insertion dans le formulaire diogene_traiter (plugin Diogene)
 * 
 * Traitement de saisies du formulaire
 * -* traite principalement les dates "date" et "date_redac"
 * -* traite la valeur pour le forum également
 * 
 * @param array $flux le contexte du pipeline
 * @return array $flux le contexte modifié
 */
function diogene_diogene_traiter($flux){
	$id_objet = $flux['args']['id_objet'];
	include_spip('inc/date_gestion');
	if(_request('date_orig')){
		$flux['data']['date'] = date('Y-m-d H:i:s',verifier_corriger_date_saisie('orig', 'oui', $erreurs));
	}
	if(_request('date_redac')){
		$flux['data']['date_redac'] = date('Y-m-d H:i:s',verifier_corriger_date_saisie('redac','oui', $erreurs));
	}
	if($forums = _request('forums')){
		$flux['data']['accepter_forum'] = $forums;
		if ($forums == 'abo') {
			ecrire_meta('accepter_visiteurs', 'oui');
		}
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_forum/$id_objet'");
	}
	return $flux;
}

/**
 * Insertion dans le pipeline (ajouter_menus)
 * 
 * Ajouter des boutons dans les menus pour chaque diogène que l'on sait gérer dans l'espace privé
 * 
 * @param object $boutons_admin La description des boutons
 * @return object $boutons_admin La description des boutons complétée
 */
function diogene_ajouter_menus($boutons_admin) {
	$diogenes = sql_select('*','spip_diogenes','objet != "emballe_media"');
	include_spip('inc/filtres_images_mini');
	include_spip('filtres/images_transforme');
	if(!function_exists('quete_logo')){
		include_spip('public/quete');
	}
	while($diogene = sql_fetch($diogenes)){
		if (autoriser('utiliser', 'diogene',$diogene['id_diogene'])) {
			if($diogene['objet'] == 'rubrique'){
				$url = generer_url_ecrire('rubrique_edit','new=oui&id_parent='.$diogene['id_secteur']);
				$icon = find_in_theme('images/rubrique-add-24.png');
			}
			if($diogene['objet'] == 'article'){
				$url = generer_url_ecrire('article_edit','new=oui&id_rubrique='.$diogene['id_secteur']);
				$icon = find_in_theme('images/article-add-24.png');
			}
			if($diogene['objet'] == 'site'){
				$url = generer_url_ecrire('site_edit','new=oui&id_rubrique='.$diogene['id_secteur']);
				$icon = find_in_theme('images/site-add-24.png');
			}
			
			$icon = extraire_attribut(image_reduire($icon,'16','16'),'src');
			
			if($logo = quete_logo('diogene', 'ON', $diogene['id_diogene'], $diogene['id_secteur'], false)){
				if(defined('_DIR_PLUGIN_BANDO')){
					$icon = extraire_attribut(image_reduire($logo[0],'16','16'),'src');
				}else{
					$icon = extraire_attribut(image_reduire($logo[0],'24','24'),'src');
				}
			}
			
			$boutons_admin['menu_edition']->sousmenu[$diogene['type']] =
			new Bouton($icon, extraire_multi($diogene['titre']),$url);
		}
	}
	return $boutons_admin;
}
?>