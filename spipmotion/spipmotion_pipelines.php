<?php

include_spip("inc/spipmotion");

function spipmotion_editer_contenu_objet($flux){
	if(extension_loaded('ffmpeg')){
		$id_document = $flux['args']['id'];
		if($flux['args']['type']=='case_document'){
			$document = sql_fetsel("docs.id_document, docs.extension, L.vu,L.objet,L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
			$extension = $document['extension'];
			$type = $document['objet'];
			$id = $document['id_objet'];
			if(in_array($extension,lire_config('spipmotion/fichiers_videos'))){
				$infos_videos = charger_fonction('infos_videos', 'inc');
				$flux['data'] .= $infos_videos($id,$id_document,$type);
			}
		}
	}
	return $flux;
}
/**
 * Pipeline Cron de SPIPmotion
 * Vérifie la présence à intervalle régulier de vidéos à encoder dans la file d'attente
 * 
 * @return
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function spipmotion_taches_generales_cron($taches_generales){
	$taches_generales['spipmotion_file'] = 60 * 4; 
	return $taches_generales;
}

/**
 * Insertion dans le pipeline post-edition
 * Intervient à chaque modification d'un objet de SPIP notamment lors de l'ajout d'un document
 * 
 * @return 
 * @param object $flux
 */
function spipmotion_post_edition($flux){
	global $connect_id_auteur;
	
	spip_log("pipeline post_edition","spipmotion");
	$id_document = $flux['args']['id_objet'];
	
	/**
	 * Il n'est pas nécessaire de récupérer la vignette d'une vignette
	 */
	$mode = sql_getfetsel('mode','spip_documents','id_document='.intval($id_document));
	spip_log("mode = $mode","spipmotion");
	
	if($mode != 'vignette'){
		if($flux['args']['operation'] == 'ajouter_document'){
			spip_log("operation = ajouter_docs","spipmotion");
			$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
			$extension = $document['extension'];
			
			/**
			 * Si nous sommes dans un format que SPIPmotion peut traiter, 
			 * on lui applique certains traitements
			 */
			if(in_array($extension,lire_config('spipmotion/fichiers_videos'))){
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
				/**
				 * Ajout de la vidéo dans la file d'attente d'encodage si besoin
				 * TODO Passer une par une configuration en CFG
				 */
				if(in_array($extension,lire_config('spipmotion/fichiers_videos_encodage',array()))){
					$en_file = sql_getfetsel("spip_spipmotion_attentes","id_document=$id_document");
					if(!$en_file){
						sql_insertq("spip_spipmotion_attentes", array('id_document'=>$id_document,'objet'=>$document['objet'],'id_objet'=>$document['id_objet'],'encode'=>'non','id_auteur'=> $connect_id_auteur));
						spip_log("on ajoute une video dans la file d'attente","spipmotion");							
					}
					else{
						spip_log("Cette video existe deja dans la file d'attente","spipmotion");							
					}
				}
				if($invalider){
					/**
					 * On invalide le cache de cet élément 
					 */
					include_spip('inc/invalideur');
					suivre_invalideur("id='id_$type/$id'");
				}
			}
		}
	}
	return $flux;
}
	
?>