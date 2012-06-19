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
 * Fonction de récupération de vignette depuis un document video
 * On utilise un script bash pour cela spipmotion_vignette.sh
 * 
 * @param int $id_document 
 * 		L'id numérique du document
 * @param int $frame
 * 		La frame à capturer
 */
function inc_spipmotion_recuperer_logo($id_document,$seconde=2){
	spip_log("SPIPMOTION : recuperation d un logo du document $id_document","spipmotion");
	/**
	 * Pas d'id_document, on retourne false
	 */
	if(!intval($id_document)){
		spip_log('SPIPMOTION Erreur : pas de bon id_document fourni pour la génération de vignette','spipmotion'._LOG_CRITIQUE);
		return false;
	}
	
	include_spip('inc/autoriser');
	if(!autoriser('modifier','document',$id_document)){
		spip_log('SPIPMOTION Erreur : tentative de récupération de logo sans autorisation de modification du document','spipmotion'._LOG_CRITIQUE);
		return false;
	}
	/**
	 * Le script de génération de vignette n'est pas là, on retourne false
	 */
	if($GLOBALS['meta']['spipmotion_spipmotion_vignette_sh_casse'] == 'oui'){
		spip_log('SPIPMOTION Erreur : le script de génération de vignette n\'est pas disponible','spipmotion'._LOG_CRITIQUE);
		return false;
	}
	include_spip('inc/documents');
	include_spip('inc/filtres_images_mini');
	$retour = 0;
	$document = sql_fetsel("docs.hasvideo,docs.id_document,docs.fichier,docs.duree,docs.id_vignette,docs.mode", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin_court = $document['fichier'];
	$chemin = get_spip_doc($chemin_court);
	
	if($document['hasvideo'] == 'oui'){
		$vignette = false;
		if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui'){
			$spipmotion_sh = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion_vignette.sh'; 
		}else{
			$spipmotion_sh = find_in_path('script_bash/spipmotion_vignette.sh');
		}
		$string_temp = "$id-$type-$id_document";
		$query = md5($string_temp);
		$dossier_temp = _DIR_VAR;
		$fichier_temp = "$dossier_temp$query.jpg";
		while(!$vignette && ($seconde < $document['duree'])){
			$cmd_vignette = $spipmotion_sh.' --e '.$chemin.' --s '.$fichier_temp.' --ss '.$seconde;
			$lancement_vignette = exec($cmd_vignette,$retour_vignette,$retour_int);
			if($retour_int >= 126){
				$erreur = _T('spipmotion:erreur_script_spipmotion_non_executable');
				spip_log("SPIPMOTION Erreur : $erreur",'spipmotion'._LOG_CRITIQUE);
				return false;
			}
			
			if($retour_int == 0){
				$vignette = true;
				if(!file_exists($fichier_temp) OR (filesize($fichier_temp) == 0)){
					spip_log("SPIPMOTION Erreur : le fichier $fichier_temp n'existe pas",'spipmotion'._LOG_CRITIQUE);
					return false;
				}else{
					$img_finale = $fichier_temp;
					$mode = 'vignette';
					$ajouter_documents = charger_fonction('ajouter_documents', 'action');
					include_spip('action/editer_document');
					if(defined('_DIR_PLUGIN_FONCTIONS_IMAGES')){
						include_spip('fonctions_images_fonctions');
						/**
						 * Si on se retrouve avec 10 images monochromes d'affilée,
						 * on tente de récupérer la vignette du document original
						 */
						if($retour>10){
							if($document['mode'] != 'conversion'){
								$original = sql_fetsel('id_document,id_vignette','spip_documents','id_document='.intval($document['id_document']));
							}
							else{
								$id_original = sql_getfetsel('doc.id_document','spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document','lien.objet="document" AND lien.id_document='.intval($id_document));
								$original = sql_fetsel('id_document,id_vignette','spip_documents','id_document='.intval($id_original));
							}
							if(intval($original['id_vignette']) > 0){
								$vignette = sql_getfetsel('fichier','spip_documents','id_document='.intval($original['id_vignette']));
								$vignette = get_spip_doc($vignette);
								$x = $ajouter_documents($document['id_vignette']?$document['id_vignette']:'new',
												array(array('tmp_name'=>$img_finale,'name'=> $img_finale)),
								    			'', 0, 'vignette');
								$x = reset($x);
								if(intval($x)){
									$vignette = true;
									$id_vignette = $x;
									document_modifier($id_document, array('id_vignette'=>$x));
								}
							    return $x;
							}
							return false;
						}else if(!filtrer('image_monochrome',$fichier_temp)){
								unlink($img_finale);
								$frame = $frame+50;
								$retour++;
							}else if(file_exists($img_finale)){
								$x = $ajouter_documents($document['id_vignette']?$document['id_vignette']:'new',
													array(array('tmp_name'=>$img_finale,'name'=> $img_finale)),
									    			'', 0, 'vignette');
								$x = reset($x);
								if(intval($x)){
									$vignette = true;
									$id_vignette = $x;
									document_modifier($id_document, array('id_vignette'=>$x));
								}
								unlink($img_finale);
							}else{
								return false;
							}
						}else{
							if(file_exists($img_finale)){
								$x = $ajouter_documents($document['id_vignette']?$document['id_vignette']:'new',
													array(array('tmp_name'=>$img_finale,'name'=> $img_finale)),
									    			'', 0, 'vignette');
								$x = reset($x);
								if(intval($x)){
									$vignette = true;
									$id_vignette = $x;
									document_modifier($id_document, array('id_vignette'=>$x));
								}
							}
						}
					}
				}
			else{
				return false;	
			}
		}
	}
	return $x;
}
?>