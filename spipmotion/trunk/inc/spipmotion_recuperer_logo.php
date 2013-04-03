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
 * @param array $infos
 * 		Un array de description du document
 * @param bool $only_return
 * 		Si true, on ne modifie pas le document, on retourne uniquement la nouvelle id_vignette
 * @return int|false $id_vignette
 * 		L'identifiant de la nouvelle vignette si elle existe ou false 
 */
function inc_spipmotion_recuperer_logo($id_document,$seconde=1,$fichier=false,$infos=false,$only_return=false){
	spip_log("SPIPMOTION : recuperation d un logo du document $id_document","spipmotion");
	
	$id_vignette = false;
	
	/**
	 * Pas d'id_document, on retourne false
	 */
	if(!intval($id_document) && (!$fichier OR !file_exists($fichier))){
		spip_log('SPIPMOTION Erreur : pas de bon id_document fourni pour la génération de vignette','spipmotion'._LOG_CRITIQUE);
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
	
	$retour = 0;
	
	if(intval($id_document)){
		include_spip('inc/autoriser');
		if(!autoriser('modifier','document',$id_document)){
			spip_log('SPIPMOTION Erreur : tentative de récupération de logo sans autorisation de modification du document','spipmotion'._LOG_CRITIQUE);
			return false;
		}
		$document = sql_fetsel("*", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
		$vignette_existante = sql_getfetsel('id_document','spip_documents','id_document='.intval($document['id_vignette']));
		if(!$vignette_existante)
			$vignette_existante = 'new';
		$chemin = get_spip_doc($document['fichier']);
		$string_temp = "$id-$type-$id_document";
	}
	else if($fichier && is_array($infos) && $only_return){
		$chemin = $fichier;
		$document = $infos;
		$string_temp = "$fichier-".date("Y-m-dHis");
	}else{
		spip_log('Mauvais arguments pour récupérer la vignette','spipmotion');
		return false;
	}
	if(!$document['duree'] OR $document['duree'] == ''){
		spip_log('Erreur : le document n a pas de durée','spipmotion');
		return false;
	}

	if($document['hasvideo'] == 'oui'){
		include_spip('inc/filtres_images_mini');
		include_spip('action/editer_document');
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		
		$vignette = false;
		if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui')
			$spipmotion_sh = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion_vignette.sh'; 
		else
			$spipmotion_sh = find_in_path('script_bash/spipmotion_vignette.sh');
		
		$query = md5($string_temp);
		$dossier_temp = _DIR_VAR;
		$fichier_temp = "$dossier_temp$query.jpg";
		while(!$vignette && ($seconde <= intval($document['duree']))){
			$params_supp = '';
			/**
			 * Forcer la vignette comme le display aspect ratio
			 */
			if(is_numeric($document['aspect_ratio'])){
				$params_supp = " --params_supp \"-aspect ".$document['aspect_ratio']."\"";
				$document['hauteur'] = intval($document['largeur'] / $document['aspect_ratio']);
			}
			$cmd_vignette = $spipmotion_sh.' --e '.$chemin.' --size '.$document['largeur'].'x'.$document['hauteur'].' --s '.$fichier_temp.' --ss '.$seconde." $params_supp";
			$lancement_vignette = exec($cmd_vignette,$retour_vignette,$retour_int);
			/**
			 * Le retour du script n'est pas bon, il est certainement non exécutable
			 */
			if($retour_int >= 126){
				$erreur = _T('spipmotion:erreur_script_spipmotion_non_executable');
				spip_log("SPIPMOTION Erreur : $erreur",'spipmotion'._LOG_CRITIQUE);
				return false;
			}
			if($retour_int == 0){
				$vignette = true;
				/**
				 * Le fichier temporaire n'existe pas, il y a un pb quelque part
				 */
				if(!file_exists($fichier_temp) OR (filesize($fichier_temp) == 0)){
					spip_log("SPIPMOTION Erreur : le fichier $fichier_temp n'existe pas",'spipmotion'._LOG_CRITIQUE);
					return false;
				}else{
					$img_finale = $fichier_temp;
					$mode = 'vignette';
					/**
					 * On teste si on a le plugin de fonctions supplémentaires d'images
					 * pour le filtre image_monochrome
					 */
					if(defined('_DIR_PLUGIN_FONCTIONS_IMAGES')){
						include_spip('fonctions_images_fonctions');
						/**
						 * Si on se retrouve avec 10 images monochromes d'affilée,
						 * on tente de récupérer la vignette du document original
						 */
						if($retour>10){
							if($document['mode'] != 'conversion')
								$original = sql_fetsel('id_document,id_vignette','spip_documents','id_document='.intval($document['id_document']));
							else{
								$id_original = sql_getfetsel('doc.id_document','spip_documents as doc LEFT JOIN spip_documents_liens as lien ON doc.id_document=lien.id_document','lien.objet="document" AND lien.id_document='.intval($id_document));
								$original = sql_fetsel('id_document,id_vignette','spip_documents','id_document='.intval($id_original));
							}
							if(intval($original['id_vignette']) > 0){
								$vignette = sql_getfetsel('fichier','spip_documents','id_document='.intval($original['id_vignette']));
								$vignette = get_spip_doc($vignette);
								$x = $ajouter_documents($vignette_existante,
												array(array('tmp_name'=>$img_finale,'name'=> $img_finale)),
								    			'', 0, 'vignette');
								$id_vignette = reset($x);
								if(intval($id_vignette)){
									$vignette = true;
									if(!$only_return && ($document['id_vignette'] != $id_vignette))
										document_modifier($id_document, array('id_vignette'=>$id_vignette));
								}
							    return $id_vignette;
							}
							return false;
						}
						/**
						 * Ici on teste si la vignette récupérée est monochrome,
						 * si elle l'est :
						 * - On supprime l'image temporaire
						 * - On augmente le nombre de seconde de 3, on essaiera donc 
						 * de récupérer une vignette 3 secondes plus tard
						 * - On remet $vignette à false
						 * - On incrémente le nombre de $retour
						 */
						else if(!filtrer('image_monochrome',$fichier_temp)){
							spip_unlink($img_finale);
							$seconde = $seconde+3;
							$vignette = false;
							$retour++;
						}
						else if(file_exists($img_finale)){
							if(($document['rotation'] == '90') && ($document['mode'] != 'conversion'))
								$img_finale = extraire_attribut(filtrer('image_rotation',$fichier_temp,90),'src');
							$x = $ajouter_documents($vignette_existante,
													array(array('tmp_name'=>$img_finale,'name'=> $img_finale)),
									    			'', 0, 'vignette');
							$id_vignette = reset($x);
							if(intval($id_vignette)){
								$vignette = true;
								if(!$only_return && ($document['id_vignette'] != $id_vignette))
									document_modifier($id_document, array('id_vignette'=>$id_vignette));
							}
							spip_unlink($img_finale);
						}else
							return false;
					}
					/**
					 * On n'a pas le plugin de fonctions d'images supplémentaires
					 * On insère comme vignette ce qu'on a
					 */
					else{
						if(file_exists($img_finale)){
							$img_finale = extraire_attribut(filtrer('image_rotation',$fichier_temp,90),'src');
							$x = $ajouter_documents($vignette_existante,
												array(array('tmp_name'=>$img_finale,'name'=> $img_finale)),
								    			'', 0, 'vignette');
							$id_vignette = reset($x);
							if(intval($id_vignette)){
								$vignette = true;
								if(!$only_return && ($document['id_vignette'] != $id_vignette))
									document_modifier($id_document, array('id_vignette'=>$id_vignette));
							}
							spip_unlink($img_finale);
						}
					}
				}
			}else
				return false;	
			
		}
	}else
		spip_log('Erreur : ce document n a pas de piste video','spipmotion');
	
	return $id_vignette;
}
?>