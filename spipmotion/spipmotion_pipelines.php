<?php
/**
 * Insertion dans le pipeline editer_contenu_objet
 * 
 * Affiche les boutons supplémentaires de :
 * - récupération de logo dans le cas d'une vidéo
 * - récupération d'informations spécifiques dans le cas d'une video
 * (Dans le cas d'un son, c'est le plugin getID3 qui s'en charge)
 * - bouton de demande d'encodage / de réencodage du son ou de la vidéo
 * 
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function spipmotion_editer_contenu_objet($flux){
	if(is_array($flux['args']) && ($flux['args']['type']=='case_document')){
		$id_document = $flux['args']['id'];
		$document = sql_fetsel("docs.id_document, docs.id_orig, docs.extension, L.vu,L.objet,L.id_objet,doc.mode,doc.distant", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$extension = $document['extension'];
		$type = $document['objet'];
		$id = $document['id_objet'];
		if($document['distant'] !== 'distant'){
			if(in_array($extension,lire_config('spipmotion/fichiers_videos',array()))){
				if($document['id_orig'] > 0){
					$flux['data'] .= '<p>'._T('spipmotion:version_encodee_de',array('id_orig'=>$document['id_orig'])).'</p>';
				}
				else if(extension_loaded('ffmpeg')){
					$infos_videos = charger_fonction('spipmotion_infos_videos', 'inc');
					$flux['data'] .= $infos_videos($id,$id_document,$type);
				}
			}
			if(in_array($extension,lire_config('spipmotion/fichiers_audios',array()))){
				if($document['id_orig'] > 0){
					$flux['data'] .= '<p>'._T('spipmotion:version_encodee_de',array('id_orig'=>$document['id_orig'])).'</p>';
				}else{
					$infos_audios = charger_fonction('spipmotion_infos_audios', 'inc');
					$flux['data'] .= $infos_audios($id,$id_document,$type);
				}
			}
		}
	}
	return $flux;
}
/**
 * Pipeline Cron de SPIPmotion
 * 
 * Vérifie la présence à intervalle régulier de fichiers à encoder 
 * dans la file d'attente
 * 
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function spipmotion_taches_generales_cron($taches_generales){
	$taches_generales['spipmotion_file'] = 60 * 4; 
	return $taches_generales;
}

/**
 * Insertion dans le pipeline post-edition
 * 
 * Intervient à chaque modification d'un objet de SPIP 
 * notamment lors de l'ajout d'un document
 * 
 * @return $flux Le contexte de pipeline complété
 * @param array $flux Le contexte du pipeline
 */
function spipmotion_post_edition($flux){
	if(in_array($flux['args']['operation'],array('ajouter_document','document_copier_local'))){
		spip_log("SPIPMOTION : pipeline post_edition","spipmotion");
		spip_log($flux['args'],'spipmotion');
		$id_document = $flux['args']['id_objet'];
		
		/**
		 * Il n'est pas nécessaire de récupérer la vignette d'une vignette
		 */
		$infos_doc = sql_fetsel('fichier,mode,distant','spip_documents','id_document='.intval($id_document));
		$mode = $infos_doc['mode'];
		$fichier = $infos_doc['fichier'];
		spip_log("SPIPMOTION : mode = $mode","spipmotion");
		spip_log("SPIPMOTION : distant = ".$infos_doc['distant'],"spipmotion");
		
		if(($mode != 'vignette') && ($infos_doc['distant'] == 'non')){
		
			spip_log("operation = ajouter_docs","spipmotion");
			$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.id_orig,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
			spip_log('id_origine = '.$document['id_orig'],'emballe_medias');
			$extension = $document['extension'];
			
			/**
			 * Si nous sommes dans un format vidéo que SPIPmotion peut traiter, 
			 * on lui applique certains traitements
			 */
			if(in_array($extension,lire_config('spipmotion/fichiers_videos',array()))){
				if (class_exists('ffmpeg_movie')) {
					spip_log("id_document=$id_document - extension = ".$document['extension'],"spipmotion");

					/**
					 * Récupération des informations de la vidéo
					 */
					$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
					$infos = $recuperer_infos($id_document);

					/**
					 * Récupération d'un logo de la vidéo
					 */
					$recuperer_logo = charger_fonction("spipmotion_recuperer_logo","inc");
					$logo = $recuperer_logo($id_document);

					$invalider = true;
				}
			}
			
			/**
			 * On l'ajoute dans la file d'attente d'encodage si nécessaire
			 */
			if(!preg_match('/encoded/',$fichier)){
				include_spip('action/spipmotion_ajouter_file_encodage');
				spipmotion_genere_file($id_document,$document['objet'],$document['id_objet']);
			}
			if($invalider){
				/**
				 * On invalide le cache de cet élément si nécessaire
				 */
				include_spip('inc/invalideur');
				suivre_invalideur("id='id_$type/$id'");
			}
		}
	}
	return $flux;
}
	
?>