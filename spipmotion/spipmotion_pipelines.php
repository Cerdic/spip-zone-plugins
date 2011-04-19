<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

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
	$type_form = $flux['args']['type'];
	$id_document = $flux['args']['id'];
	if(is_array($flux['args']) && (in_array($type_form,array('illustrer_document','case_document','document')))){
		$document = sql_fetsel("docs.id_document, docs.id_orig, docs.extension,docs.mode,docs.distant, L.vu,L.objet,L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$extension = $document['extension'];
		$type = $document['objet'];
		$id = $document['id_objet'];
		if(in_array($type_form,array('case_document','document'))){
			if($document['distant'] !== 'oui'){
				$ajouts = '';
				if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && in_array($extension,lire_config('spipmotion/fichiers_videos',array()))){
					if($document['id_orig'] > 0){
						$ajouts .= '<p>'._T('spipmotion:version_encodee_de',array('id_orig'=>$document['id_orig'])).'</p>';
					}
					if(extension_loaded('ffmpeg')){
						$infos_videos = charger_fonction('spipmotion_infos_videos', 'inc');
						$ajouts .= $infos_videos($id,$id_document,$type);
					}
				}
				if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && in_array($extension,lire_config('spipmotion/fichiers_audios',array()))){
					if($document['id_orig'] > 0){
						$flux['data'] .= '<p>'._T('spipmotion:version_encodee_de',array('id_orig'=>$document['id_orig'])).'</p>';
					}
					else{
						$infos_audios = charger_fonction('spipmotion_infos_audios', 'inc');
						$ajouts .= $infos_audios($id,$id_document,$type);
					}
				}
				if($type_form == 'case_document'){
					$flux['data'] .= $ajouts;
				}else{
					if(preg_match(",<li [^>]*class=[\"']editer_infos.*>(.*)<\/li>,Uims",$flux['data'],$regs)){
						$infos_doc = recuperer_fond('prive/prive_infos_video', $contexte=array('id_document'=>$id_document));
						$flux['data'] = preg_replace(",($regs[1]),Uims","\\1".$infos_doc,$flux['data']);
					}
				}
			}
		}
		else if(in_array($type_form,array('illustrer_document'))){
			if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && in_array($extension,lire_config('spipmotion/fichiers_videos',array()))){
				if(preg_match(",<div [^>]*id=[\"'](formulaire_illustrer_document.*)[\"'].*>(.*)<\/div>,Uims",$flux['data'],$regs)){
					$redirect = ancre_url(self(),$regs[1]);
					$url_action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
					$texte = _T('spipmotion:lien_recuperer_logo_fichier');
					$recuperer_vignette = " | <a href='$url_action'>$texte</a>";
					$flux['data'] = preg_replace(",(<div [^>]*class=[\"']sourceup.*>(.*)<\/div>),Uims","\\2".$recuperer_vignette,$flux['data']);
				}
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline document_desc_actions (Mediathèque)
 * On ajoute un lien pour récupérer le logo et relancer les encodages
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
				($infos_doc['id_orig'] == 0)
				&& in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
				$statut_encodage = sql_getfetsel('encode','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode IN ("en_cours","non")');
				if($statut_encodage == 'en_cours'){
					$action3 = '';
					$texte3 = _T('spipmotion:document_en_cours_encodage');
				}elseif ($statut_encodage == 'non'){
					$action3 = '';
					$texte3 = _T('spipmotion:document_dans_file_attente');
				}else{
					$texte3 = _T('spipmotion:encoder_video');
					$action3 = generer_action_auteur('spipmotion_ajouter_file_encodage', "0/article/$id_document", $redirect);
				}
			}else if(
				($infos_doc['id_orig'] == 0)
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
			 */
			$fichier = basename(get_spip_doc($document['fichier']));
			$racine = preg_replace('/-encoded-(\d+)/','',substr($fichier,0,-(strlen($document['extension'])+1)));
			$id_doc = sql_getfetsel('id_document','spip_documents',"fichier LIKE '%$racine%' AND id_document != $id_document AND id_orig=0");
			if(($GLOBALS['meta']['spipmotion_casse'] != 'oui') && !preg_match('/-encoded/',$document['fichier']) OR !$id_doc){
				include_spip('action/spipmotion_ajouter_file_encodage');
				spipmotion_genere_file($id_document,$document['objet'],$document['id_objet']);
			}

			/**
			 * On invalide le cache de cet élément si nécessaire
			 */
			if($invalider){
				include_spip('inc/invalideur');
				suivre_invalideur("id='id_$type/$id'");
			}
		}
	}
	return $flux;
}

function spipmotion_insert_head_css($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('spipmotion.css', 'css/', false)).'" type="text/css" media="all" />
';
	return $flux;
}

function spipmotion_header_prive($flux){
	$flux .= '
<link rel="stylesheet" href="'.direction_css(find_in_path('spipmotion.css', 'css/', false)).'" type="text/css" media="all" />
';
	return $flux;
}

function spipmotion_jquery_plugins($array){
	if(!in_array(_DIR_LIB_FLOT.'/jquery.flot.js',$array)){
		$array[] = _DIR_LIB_FLOT.'/jquery.flot.js';
	}
	$array[] = 'javascript/spipmotion_flot_extras.js';
	return $array;
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
				
				$images = array();
				foreach($infos_origine as $info_origine => $info){
					if(preg_match('/cover/',$info_origine)){
						$images[] = $info;
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
?>