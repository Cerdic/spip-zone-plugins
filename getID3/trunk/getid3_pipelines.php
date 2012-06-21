<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline post_edition
 * Récupération d'informations sur le document lors de son insertion en base
 *
 * @param array $flux Le contexte du pipeline
 * @return $flux le $flux modifié
 */
function getid3_post_edition($flux){
	if(in_array($flux['args']['operation'],array('ajouter_document','modifier'))){
		static $getid3_done = false;
		if(!$getid3_done){
			$id_document = $flux['args']['id_objet'];
			$son_modif_id3 = array("mp3,ogg,oga,flac");
			$son_recup_id3 = array("mp3","ogg","flac","aiff","aif","wav","m4a","oga");
			$extensions_vignettes = array("png","gif","jpg");
			$conf_id3 = lire_config('getid3/reecriture_tags',array());
			$document = sql_fetsel("*", "spip_documents","id_document=".sql_quote($id_document));
			$extension = $document['extension'];
			if($flux['args']['operation'] == 'ajouter_document'){
				$getid3_done = true;
				/**
				 * Récupération automatique des infos des fichiers sons à leur insertion
				 */
				if(in_any($extension,$son_recup_id3)){
					$recuperer_infos = charger_fonction('getid3_recuperer_infos','inc');
					$infos = $recuperer_infos($id_document);
				}
				/**
				 * L'ajout est une vignette
				 * Insertion de la vignette automatiquement dans le mp3 si changement
				 */
				else if(in_any($extension,$extensions_vignettes)
					&& ($document_orig = sql_fetsel('*','spip_documents','id_vignette='.intval($id_document)))
					&& ($document_orig['distant'] != 'oui')
					&& in_array($document_orig['extension'],$son_modif_id3)
				){
					include_spip('inc/documents');
					
					$fichier_orig = get_spip_doc($document_orig['fichier']);
					$recuperer_id3 = charger_fonction('recuperer_id3','inc');
					$valeurs = $recuperer_id3($fichier_orig);
					
					$files[] = get_spip_doc($document['fichier']);
					
					foreach($valeurs as $valeur => $info){
						if(preg_match('/cover/',$valeur) && (count($files) == 0)){
							$files[] = $info;
						}else{
							$valeurs[$valeur] = filtrer_entites($info);
						}
					}
					
					/**
					 * On écrit les tags
					 */
					$ecrire_id3 = charger_fonction('getid3_ecrire_infos','inc');
					$err = $ecrire_id3($document_orig['id_document'],$valeurs,$files);
				}
			}
			/**
			 * Mise à jour des tags des mp3 si besoin
			 */
			if($flux['args']['action'] == 'modifier'){
	        	$getid3_done = true;
				
				if(in_any($extension,$son_modif_id3)){
					$update = false;
					foreach($flux['data'] as $key => $value){
						if(in_array($key,$conf_id3))
							$update = true;
					}
					if(is_numeric($flux['data']['id_vignette'])){
						$update = true;
					}
					if($update){
						$files = null;
						
						/**
						 * On récupère tout d'abord les anciens tags
						 */	
						include_spip('inc/documents');
						$fichier = get_spip_doc($document['fichier']);
						$recuperer_id3 = charger_fonction('recuperer_id3','inc');
						$valeurs = $recuperer_id3($fichier);
						
						if(is_numeric($flux['data']['id_vignette'])){
							$files[] = get_spip_doc(sql_getfetsel('fichier','spip_documents','id_document='.intval($flux['data']['id_vignette'])));
						}
						foreach($valeurs as $valeur => $info){
							if(preg_match('/cover/',$valeur) && (count($files) == 0)){
								$files[] = $info;
							}else{
								$valeurs[$valeur] = filtrer_entites($info);
							}
						}
						
						if(isset($flux['data']['titre']) && in_array('titre',$conf_id3))
							$valeurs['title'] = $flux['data']['titre'];
							
						if(isset($flux['data']['descriptif']) && in_array('descriptif',$conf_id3))
							$valeurs['comment'] = $flux['data']['descriptif'];
							
						/**
						 * On écrit les tags
						 */
						$ecrire_id3 = charger_fonction('getid3_ecrire_infos','inc');
						$err = $ecrire_id3($id_document,$valeurs,$files);
					}
				}
			}
		}
	}
	return $flux;
}

/**
 * Ajouter le lien vers la modifs des id3
 *
 * @param array $flux
 * @return array
 */
function getid3_document_desc_actions($flux){
	$infos = sql_fetsel('distant,extension','spip_documents','id_document='.intval($flux['args']['id_document']));
	$son_recup_id3 = array("mp3","ogg","flac","aiff","aif","wav","m4a","oga");
	$son_modif_id3 = lire_config('getid3_write',array('mp3'));
	$id_document = $flux['args']['id_document'];
	if(($infos['distant'] == 'non') && in_array($infos['extension'],$son_modif_id3)){
		$redirect = self();
		$url = parametre_url(generer_url_ecrire('document_id3_editer','id_document='.intval($id_document)),'redirect',$redirect);
		$texte = _T('getid3:lien_modifier_id3');
		if($flux['args']['position'] == 'galerie'){
			$flux['data'] .= "[<a href='$url'>$texte</a>]";
		}else{
			$flux['data'] .= "<span class='sep'> | </span><a href='$url' target='_blank' class='editbox'>$texte</a>";
		}
	}if(($infos['distant'] == 'non') && in_array($infos['extension'],$son_recup_id3)){
		$texte2 = _T('getid3:lien_recuperer_infos');
		$action2 = generer_action_auteur('getid3_infos', "0/article/$id_document", $redirect);
		$flux['data'] .= "<span class='sep'> | </span><a href='$action2'>$texte2</a>";
	}
	return $flux;
}

/**
 * Pipeline Cron de GetID3
 *
 * Vérifie chaque jour que les logiciels nécessaires sont présents
 *
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function getid3_taches_generales_cron($taches_generales){
	$taches_generales['getid3_taches_generales'] = 24*60*60;
	return $taches_generales;
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
function getid3_recuperer_fond($flux){
	if ($flux['args']['fond']=='modeles/document_desc'){
		if(isset($flux['args']['contexte']['id_document']) && ($flux['args']['contexte']['id_document'] > 0)){
			$son_recup_id3 = array("mp3","ogg","flac","aiff","aif","wav","m4a","oga");
			$extension = sql_getfetsel("extension", "spip_documents","id_document=".intval($flux['args']['contexte']['id_document']));
			if(in_array($extension,$son_recup_id3))
				$flux['data']['texte'] .= recuperer_fond('prive/inclure/prive_infos_son',$flux['args']['contexte']);
		}
	}
	return $flux;
}
?>