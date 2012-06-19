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

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline document_desc_actions (Mediathèque)
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
	$id_document = $flux['args']['id_document'];
	$infos_doc = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
	if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && (($video = in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos',array()))) OR ($son = in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_audios',array()))))){
		include_spip('inc/documents');
		if(file_exists(get_spip_doc($infos_doc['fichier']))){
			$redirect = ancre_url(self(),"doc".$id_document);
			if($video){
				$url_action_logo = generer_action_auteur('spipmotion_logo', "0/article/$id_document", $redirect);
				$texte_logo = _T('spipmotion:recuperer_logo');
				$flux['data'] .= " | <a href='$url_action_logo'>$texte_logo</a>";
				if(extension_loaded('ffmpeg')){
					$texte2 = _T('spipmotion:recuperer_infos');
					$action2 = generer_action_auteur('spipmotion_infos', "0/article/$id_document", $redirect);
					$flux['data'] .= " | <a href='$action2'>$texte2</a>";
				}
			}

			$sorties_audio = lire_config('spipmotion/fichiers_audios_sortie',array());
			$sorties_audio = array_diff($sorties_audio,array($infos_doc['extension']));
			if(
				($infos_doc['mode'] != 'conversion')
				&& in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
				$statut_encodage = sql_getfetsel('encode','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode IN ("en_cours","non")');
				if($statut_encodage == 'en_cours'){
					$action3 = '';
					$texte3 = _T('spipmotion:info_document_encodage_en_cours');
				}elseif ($statut_encodage == 'non'){
					$action3 = '';
					$texte3 = _T('spipmotion:document_dans_file_attente');
				}else{
					$texte3 = _T('spipmotion:encoder_video');
					$action3 = generer_action_auteur('spipmotion_ajouter_file_encodage', "0/article/$id_document", $redirect);
				}
			}else if(
				($infos_doc['mode'] != 'conversion')
				&& in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_audios_encodage',array()))
				&& (count($sorties_audio)>0)
			){
				$statut_encodage = sql_getfetsel('encode','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode IN ("en_cours","non")');
				if($statut_encodage == 'en_cours'){
					$action3 = false;
					$texte3 = _T('spipmotion:document_dans_file_attente');
				}elseif ($statut_encodage == 'non'){
					$action3 = false;
					$texte3 = _T('spipmotion:document_dans_file_attente');
				}else{
					$texte3 = _T('spipmotion:encoder_son');
					$action3 = generer_action_auteur('spipmotion_ajouter_file_encodage', "0/article/$id_document", $redirect);
				}
			}
			if($action3)
				$flux['data'] .= " | <a href='$action3'>$texte3</a>";
			else if($texte3)
				$flux['data'] .= " | $texte3";
		}else{
			$texte = _T('spipmotion:erreur_document_plus_disponible');
			$flux['data'] .= " | $texte";
		}
	}
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
	$taches_generales['spipmotion_file'] = 3*60;
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
		include_spip('inc/config');
		$id_document = $flux['args']['id_objet'];

		/**
		 * Il n'est pas nécessaire de récupérer la vignette d'une vignette
		 * ni ses infos.
		 */
		$infos_doc = sql_fetsel('fichier,mode,distant','spip_documents','id_document='.intval($id_document));
		$mode = $infos_doc['mode'];
		$fichier = $infos_doc['fichier'];

		if(($mode != 'vignette') && ($infos_doc['distant'] == 'non')){
			$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.id_orig,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
			$extension = $document['extension'];

			/**
			 * Si nous sommes dans un format vidéo que SPIPmotion peut traiter,
			 * on lui applique certains traitements
			 * Les fichiers sonores sont gérés par le plugin getID3 pour cela
			 */

			if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && in_array($extension,lire_config('spipmotion/fichiers_videos',array()))){
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
			 * On l'ajoute dans la file d'attente d'encodage si nécessaire
			 * Si et seulement si on a l'option d'activée dans la conf
			 */
			if(lire_config('spipmotion/encodage_auto') == 'on'){
				$fichier = basename(get_spip_doc($document['fichier']));
				$racine = preg_replace('/-encoded-(\d)/','',substr($fichier,0,-(strlen($document['extension'])+1)));
				$racine = preg_replace('/-encoded-(\d+)/','',$racine);
				$racine = preg_replace('/-encoded/','',$racine);
				$id_doc = sql_getfetsel('id_document','spip_documents',"fichier LIKE '%$racine%' AND id_document != $id_document AND id_orig=0");
				if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && !preg_match('/-encoded/',$document['fichier']) OR !$id_doc){
					include_spip('action/spipmotion_ajouter_file_encodage');
					spipmotion_genere_file($id_document,$document['objet'],$document['id_objet']);
					$encodage_direct = charger_fonction('spipmotion_encodage_direct','inc');
					$encodage_direct();
				}
			}
			/**
			 * On invalide le cache de cet élément si nécessaire
			 */
			if($invalider){
				include_spip('inc/invalideur');
				suivre_invalideur("id='id_$type/$id'");
			}
		}
	}else if($flux['args']['operation'] == 'supprimer_documents'){
		sql_delete('spip_spipmotion_attentes','id_document = '.$flux['args']['id_objet'].' AND encode!='.sql_quote('oui'));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline insert_head_css (SPIP)
 * On ajoute la css de spipmotion dans le public
 * 
 * @param string $flux
 * 		Le contenu du head
 * @return string $flux
 * 		Le head modifié
 */
function spipmotion_insert_head_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('spipmotion.css', 'css/', false)).'" type="text/css" media="all" />
';
	return $flux;
}

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * On ajoute la css de spipmotion dans le privé
 * 
 * @param string $flux
 * 		Le contenu du head
 * @return string $flux
 * 		Le head modifié
 */
function spipmotion_header_prive($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('spipmotion.css', 'css/', false)).'" type="text/css" media="all" />
';
	return $flux;
}

/**
 * Insertion dans le pipeline jquery_plugins (SPIP)
 * On ajoute deux javascript dans le head
 * 
 * @param array $plugins
 * 		L'array des js insérés
 * @return array $plugins
 * 		L'array des js insérés modifié
 */
function spipmotion_jquery_plugins($plugins){
	if(!in_array(_DIR_LIB_FLOT.'/jquery.flot.js',$plugins)){
		$plugins[] = _DIR_LIB_FLOT.'/jquery.flot.js';
	}
	$plugins[] = 'javascript/spipmotion_flot_extras.js';
	return $plugins;
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
	
	$encodage_direct = charger_fonction('spipmotion_encodage_direct','inc');
	$encodage_direct();
	
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
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);
		}
	
		if(count($erreurs) > 0){
			include_spip('inc/invalideur');
			suivre_invalideur('1');
	
			/**
			 * On force le rechargement de la page car on a récupéré de nouvelles infos sur ffmpeg
			 */
			$flux['data']['redirect'] = self();
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
	return $flux;
}
?>