<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline document_desc_actions (medias)
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
function spipmotion_document_desc_actions($flux){
	$flux['data'] .= recuperer_fond('prive/squelettes/inclure/spipmotion_document_desc_action',$flux['args']);
	return $flux;
}

/**
 * Pipeline Cron de SPIPmotion (SPIP)
 *
 * Vérifie la présence à intervalle régulier de fichiers à encoder
 * dans la file d'attente
 *
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function spipmotion_taches_generales_cron($taches_generales){
	$taches_generales['spipmotion_taches_generales'] = 24*60*60;
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
	if(in_array($flux['args']['operation'], array('ajouter_document','document_copier_local'))){
		$id_document = $flux['args']['id_objet'];
		/**
		 * Il n'est pas nécessaire de récupérer la vignette d'une vignette ni d'un document distant
		 * ni ses infos.
		 */
		$infos_doc = sql_fetsel('mode,distant,extension','spip_documents','id_document='.intval($id_document));
		if(($infos_doc['mode'] != 'vignette') && ($infos_doc['distant'] == 'non')){
			include_spip('inc/config');
			/**
			 * Si nous sommes dans un format vidéo que SPIPmotion peut traiter,
			 * on lui applique certains traitements :
			 * -* récupération d'une vignette
			 * La récupération des infos est faite directement via metadata/video lors de l'insertion
			 * Les fichiers sonores sont gérés par le plugin getID3 pour cela
			 */
			if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos',array()))){
				/**
				 * Récupération des informations de la vidéo
				 */
				//$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
				//$infos = $recuperer_infos($id_document);

				/**
				 * Récupération d'un logo de la vidéo
				 */
				$recuperer_logo = charger_fonction("spipmotion_recuperer_logo","inc");
				$logo = $recuperer_logo($id_document);
				$invalider = true;
			}
			if(
				($GLOBALS['meta']['spipmotion_casse'] != 'oui')
				&& ($infos_doc['mode'] != 'conversion')
				&& (lire_config('spipmotion/encodage_auto','off') == 'on') 
				&& (in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos',array())) OR in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_audios',array())))){
				/**
				 * On l'ajoute dans la file d'attente d'encodage si nécessaire
				 * Si et seulement si on a l'option d'activée dans la conf
				 */
				include_spip('action/spipmotion_ajouter_file_encodage');
				spipmotion_genere_file($id_document);
				/**
				 * On lance une conversion directe en tache de fond
				 */
				$conversion_directe = charger_fonction('facd_convertir_direct','inc');
				$conversion_directe();
				$invalider = true;
			}
			/**
			 * On invalide le cache de cet élément si nécessaire
			 */
			if($invalider){
				include_spip('inc/invalideur');
				suivre_invalideur("id='id_document/$id_document'");
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline jqueryui_plugin (plugin jQuery UI)
 * 
 * On ajoute le chargement des js pour les tabs (utilisés dans la conf)
 * 
 * @param array $plugins 
 * 		Un tableau des scripts déjà demandé au chargement
 * @retune array $plugins 
 * 		Le tableau complété avec les scripts que l'on souhaite 
 */
function spipmotion_jqueryui_plugins($plugins){
	$plugins[] = "jquery.ui.tabs";
	return $plugins;
}

function spipmotion_post_spipmotion_encodage($flux){
	if($flux['args']['reussite'] == 'oui'){
		$origine = sql_fetsel('extension,fichier','spip_documents','id_document='.intval($flux['args']['id_document_orig']));
		if(in_array($origine['extension'],array('mp3','flac','ogg','oga'))){
			$extension_nouveau = sql_getfetsel('extension','spip_documents','id_document='.intval($flux['args']['id_document']));
			if(in_array($extension_nouveau,lire_config('getid3_write',array()))){
				include_spip('inc/documents');
				$recuperer_id3 = charger_fonction('recuperer_id3','inc');
				$infos_write = array(
					'title' => 0,
					'artist' => 0,
					'year' => 0,
					'date'=>0,
					'album' => 0,
					'genre' => 0,
					'comment' => 0,
					'tracknumber' => 0
				);
				$infos_origine = $recuperer_id3(get_spip_doc($origine['fichier']));
				
				if($extension_nouveau == 'mp3'){
					$images = array();
					foreach($infos_origine as $info_origine => $info){
						if(preg_match('/cover/',$info_origine)){
							$images[] = $info;
						}
					}
				}
				$infos_encode = array_intersect_key($infos_origine,$infos_write);
				$ecrire_infos = charger_fonction('getid3_ecrire_infos','inc');
				$ecrire_infos($flux['args']['id_document'],$infos_encode,$images);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * Vérification de certaines valeurs de la configuration
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function spipmotion_formulaire_verifier($flux){
	if($flux['args']['form'] == 'configurer_spipmotion'){
		foreach($_POST as $key => $val){
			if(preg_match('/(bitrate|height|width|frequence_audio|fps|passes|qualite_video|qualite_audio).*/',$key) && $val){
				if(!ctype_digit($val)){
					$flux['data'][$key] = _T('spipmotion:erreur_valeur_int');
				}else if(preg_match('/(height|width).*/',$key) && ($val < 100)){
					$flux['data'][$key] = _T('spipmotion:erreur_valeur_int_superieur',array('val'=> 100));
				}
			}
		}
		if(count($erreur) > 0)
			$flux['data']['message_erreur'] = _T('spipmotion:erreur_formulaire_configuration');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * 
 * Traitement spécifique à la validation du formulaire de configuration
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function spipmotion_formulaire_traiter($flux){
	if($flux['args']['form'] == 'configurer_spipmotion'){
		$valeurs = $_POST;
	
		$verifier_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
		$erreurs = $verifier_binaires($valeurs);
		
		if(!in_array('ffmpeg',$erreurs)){
			/**
			 * On récupère les informations du nouveau ffmpeg
			 */
			$ffmpeg_infos = charger_fonction('spipmotion_ffmpeg_infos','inc');
			$ffmpeg_infos(true);
		}
	
		if(count($erreurs) > 0){
			include_spip('inc/invalideur');
			suivre_invalideur('1');
	
			/**
			 * On force le rechargement de la page car on a récupéré de nouvelles infos sur ffmpeg
			 */
			//$flux['data']['redirect'] = self();
		}
	}
	return $flux;
}


/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * 
 * On affiche les informations du document
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function spipmotion_recuperer_fond($flux){
	if ($flux['args']['fond']=='modeles/document_desc'){
		if(isset($flux['args']['contexte']['id_document']) && ($flux['args']['contexte']['id_document'] > 0)){
			$extension = sql_getfetsel("extension", "spip_documents","id_document=".intval($flux['args']['contexte']['id_document']));
			if(in_array($extension,lire_config('spipmotion/fichiers_videos',array()))){
				$flux['data']['texte'] .= recuperer_fond('prive/squelettes/inclure/prive_infos_video', $flux['args']['contexte']);
			}
		}
	}
	if ($flux['args']['fond']=='prive/squelettes/contenu/facd'){
		$flux['data']['texte'] .= recuperer_fond('prive/squelettes/inclure/file_stats', $flux['args']['contexte']);
	}
	if ($flux['args']['fond']=='prive/squelettes/navigation/facd'){
		$flux['data']['texte'] .= recuperer_fond('prive/squelettes/navigation/spipmotion_file', $flux['args']['contexte']);
	}
	return $flux;
}
?>