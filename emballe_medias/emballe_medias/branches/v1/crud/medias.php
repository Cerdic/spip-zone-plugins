<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('action/editer_article');

/**
 * Interface C(r)UD pour Emballe medias
 */

/**
 * Create :
 * Crée un media soit :
 * -* Un article réceptacle
 * -* Un document associé à l'article 
 * -* Un pipeline qui fait le travail pour les choses additionnelles
 * 
 * @param $dummy
 * @param array $set : Le contenu des champs à mettre en base
 * @return array : un array avec (bool) success, (string) message et (array) result indiquant l'id créé 
 */
function crud_medias_create_dist($dummy,$set=null){
	$tmp_name = $set['document']['tmp_name'];
	if(!file_exists($tmp_name)){
		$e = _L('document inexistant');
	}else{
		$crud = charger_fonction('crud','action');
		if(($id_auteur= intval($GLOBALS['visiteur_session']['id_auteur']))>0){
			$type = $set['em_type'] ? $set['em_type'] : 'normal';
		
			$titre_sans_extension = explode('.',basename($tmp_name));
			if(count($titre_sans_extension > 1))
				array_pop($titre_sans_extension);
				$titre_doc = str_replace('_',' ',implode(' ',$titre_sans_extension));
		
			// si l'option de config "chercher_article" est active
			if (lire_config('emballe_medias/fichiers/chercher_article','off') == 'on')
				$id_article = sql_getfetsel("art.id_article","spip_articles AS art LEFT JOIN spip_auteurs_articles AS aut ON (art.id_article=aut.id_article)","art.statut IN ('prop','prepa') AND art.em_type = ".sql_quote($type)." AND aut.id_auteur = ".intval($id_auteur),"","art.maj");
	
			if(!intval($id_article)){
				/**
				 * On recherche la rubrique dans laquelle on va préenregistrer notre article
				 * -* soit elle est passée directement par le script d'upload
				 * -* soit on récupère le secteur du premier spip_diogènes dont l'objet est emballe_medias et dans ce cas :
				 * -** Soit on autorise à publier à la racine de ce secteur et c'est cette rubrique (cf config)
				 * -** Soit on prend la première rubrique de ce secteur
				 */
				
				if(!$id_rubrique = intval($set['id_rubrique'])){
					$rub_diogene = sql_getfetsel('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));
					if(lire_config('emballe_medias/fichiers/publier_dans_secteur','off') != 'on'){
						$id_rubrique = sql_getfetsel('id_rubrique','spip_rubriques','id_parent='.intval($rub_diogene));
					}
					if(!$id_rubrique){
						$id_rubrique = $rub_diogene;
					}
				}
				
				/**
				 * Création de l'article qui sert de réceptacle au document
				 * On utilise le crud de création d'article du coup
				 */
				$c = array(
					'id_rubrique' => $id_rubrique,
					'titre' => $titre_doc,
					'em_type' => $type
				);
				
				foreach(array('surtitre', 'titre', 'soustitre', 'descriptif',
					'nom_site', 'url_site', 'chapo', 'texte', 'ps') as $champ){
					if($set[$champ])
						$c[$champ] = $set[$champ];
				}
				if($set['statut'] && in_array($set['statut'],array('prepa','publie'))){
					$statut = $set['statut'];
				}
				if((lire_config('diogene/statuts/article_statut_defaut','prop') != 'prop') OR isset($statut)){
					$c['date'] = date("Y-m-d H:i:s");
					$c['statut'] = $statut ? $statut : lire_config('diogene/statuts/article_statut_defaut');
					$c['id_parent'] = $id_rubrique;
				}
				
				$res = $crud('create','article','',$c);
				if(is_numeric($res['result']['id'])){
					$id_article = $res['result']['id'];
				}else{
					spip_unlink($tmp_name);
					$e = _L('Impossible de créer l article');
				}
			}else if(is_numeric($id_document = sql_getfetsel('id_document','spip_documents_liens','objet="article" AND id_objet='.intval($id_article)))){
				$e = _L("Document $id_document déja existant");
			}else{
				foreach(array('surtitre', 'titre', 'soustitre', 'descriptif',
					'nom_site', 'url_site', 'chapo', 'texte', 'ps') as $champ){
					if($set[$champ])
						$c[$champ] = $set[$champ];
				}
				$res_update_article = $crud('update','article',$id_article,$c);
			}
			
			if(!$e && intval($id_article)){
				/**
				 * Création du document que l'on lie à l'article
				 */
				$set_document = array(
						'source' => $tmp_name,
						'name' => $set['document']['name'],
						'type' => 'article',
						'id_objet' => $id_article,
						'mode' => 'document'
				);
				$res_document = $crud('create','document','',$set_document);
				
				spip_unlink($tmp_name);
				if(is_numeric($res_document['result']['id'])){
					$id_document = $res_document['result']['id'];
			
					$mime = $set['document']['type'];
					
					/**
					 * Si le document a un titre et un descriptif et pas l'article, on les donne aussi à l'article
					 */
					
					if(!$set['titre'] && !$set['texte']){
						$infos_doc = sql_fetsel('titre,descriptif','spip_documents','id_document='.intval($id_document));
						if(!$set['titre'])
							$c['titre'] = $infos_doc['titre'] ? $infos_doc['titre'] : $titre_doc;
						if(!$set['texte'])
							$c['texte'] = $infos_doc['descriptif'];
						unset($infos_doc['descriptif']);
						$res_update_article = $crud('update','article',$id_article,$c);
					}
					
					if(defined('_DIR_PLUGIN_LICENCE') && isset($set['id_licence'])){
						include_spip('inc/modifier');
						$c['id_licence'] = $set['id_licence'];
						revision_article($id_article, $c);
					}
					
					/**
					 * Création du point géolocalisé
					 * Nécessite le plugin GIS
					 * Nécessite un array "gis" dans les arguments avec au moins une latitude et une longitude
					 */
					if(defined('_DIR_PLUGIN_GIS') && is_array($set['gis'])){
						if(is_numeric($set['gis']['lat']) AND is_numeric($set['gis']['lon'])){
							$id_gis = sql_getfetsel('gis.id_gis','spip_gis AS gis LEFT JOIN spip_gis_liens AS liens ON gis.id_gis=liens.id_gis','objet="article" AND id_objet='.intval($id_article));
							$set['gis']['objet'] = 'article';
							$set['gis']['id_objet'] = $id_article; 
							$args_update= array('objet'=>'gis','id_objet'=>'','set'=>$set['gis']);
							if(intval($id_gis) > 0){
								$gis = $crud('update','gis',$id_gis,$set['gis']);
							}else{
								$gis = $crud('create','gis','',$set['gis']);
							}
						}
					}
					
					/**
					 * Création des tags
					 * Nécessite le plugin SPIPicious
					 * Nécessite un string "tags" dans les argumentsn les tags doivent être séparés par des points virgule
					 */
					if(defined('_DIR_PLUGIN_SPIPICIOUS') && $set['tags']){
						$tableau_tags = array_map('trim',explode(';', $set['tags']));
						$id_groupe = lire_config('spipicious/groupe_mot','1');
						$id_table_objet = id_table_objet('article');
						$table_mot = table_objet_sql('spip_mots_'.table_objet('article'));
						include_spip('action/spipicious_ajouter_tags');
						list($message,$invalider,$err) = spipicious_ajouter_tags($tableau_tags,$GLOBALS['visiteur_session']['id_auteur'],$id_article,'article',$id_table_objet,$table_mot,$id_groupe);
					}
					/**
					 * Traitements spécifiques après l'upload
					 * lié à emballe medias
					 */ 
					pipeline('em_post_upload_medias',
						array(
							'args' => array(
								'id_document' => $id_document,
								'mime' => $mime,
								'objet' => 'article',
								'id_objet' => $id_article,
								'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
								'ancien_document' => $ancien_document,
								'action_document' => $action_document
							)
						)
					);
					$id = $id_article;
				}else{
					$e = 'Impossible de créer le document';
				}
			}
		}else{
			$e = _L('identification obligatoire');	
		}
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

/**
 * Update :
 * Met à jour un media
 * -* On doit spécifier un article
 * -* Cet article doit être existant
 * -* Cet article doit être dans la rubrique des médias
 * @param $dummy
 * @param array $set : Le contenu des champs à mettre en base
 * @return array : un array avec (bool) success, (string) message et (array) result indiquant l'id mis à jour 
 */
function crud_medias_update_dist($id,$set=null){
	if(!intval($args['id_article'])){
		$e = _L('spécifier un id_article');
	}
	$infos_articles = sql_fetsel('id_article,id_secteur','spip_articles','id_article='.intval($id)); 
	if(!$infos_articles){
		$e = _L('spécifier un media existant');
	}else{
		$secteur_diogene = sql_getfetsel('id_secteur','spip_diogenes','objet='.sql_quote('emballe_media'));
		if($infos_articles['id_secteur'] != $secteur_diogene)
			$e = _L('spécifier un media dans le bon secteur');
	}
	if(!e){
		$crud = charger_fonction('crud','action');
		foreach(array('surtitre', 'titre', 'soustitre', 'descriptif',
			'nom_site', 'url_site', 'chapo', 'texte', 'ps','id_licence') as $champ){
			if($set[$champ])
				$c[$champ] = $set[$champ];
		}
		if($args['statut'] && in_array($set['statut'],array('prepa','publie','poubelle'))){
			$c['statut'] = $set['statut'];
		}
		
		$article = $crud('update','article',$id,$c);
		if($article['success']){
			/**
			 * Mise à jour du point géolocalisé ou création si non existant
			 * Nécessite le plugin GIS
			 * Nécessite un array "gis" dans les arguments avec au moins une latitude et une longitude
			 */
			if(defined('_DIR_PLUGIN_GIS') && is_array($args['gis'])){
				if(is_numeric($args['gis']['lat']) AND is_numeric($args['gis']['lon'])){
					$id_gis = sql_getfetsel('gis.id_gis','spip_gis AS gis LEFT JOIN spip_gis_liens AS liens ON gis.id_gis=liens.id_gis','objet="article" AND id_objet='.intval($id));
					$set['gis']['objet'] = 'article';
					$set['gis']['id_objet'] = $id; 
					$args_update= array('objet'=>'gis','id_objet'=>'','set'=>$set['gis']);
					if(intval($id_gis) > 0){
						$gis = $crud('update','gis',$id_gis,$set['gis']);
					}else{
						$gis = $crud('create','gis','',$set['gis']);
					}
				}
			}
		}else{
			$e = $article['message'];
		}
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

/**
 * Delete :
 * Supprime un point géolocalisé
 * 
 * @param $dummy
 * @param int $id : L'identifiant numérique du point à supprimer
 * @return array : un array avec (bool) success, (string) message et (array) result indiquant l'id supprimé 
 */
function crud_medias_delete_dist($id){
	return false;
	if(autoriser('supprimer','gis',$id)){
		list($e,$ok) = supprimer_gis($id);
	}
	return array('success'=>$e?false:true,'message'=>$e?$e:$ok,'result'=>array('id'=>$id));
}

?>