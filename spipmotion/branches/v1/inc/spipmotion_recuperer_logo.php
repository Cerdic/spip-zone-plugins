<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de récupération de vignette depuis un document video
 * On utilise un script bash pour cela spipmotion_vignette.sh
 * @param int $id_document L'id numérique du document
 * @param int $frame la frame à capturer
 */
function inc_spipmotion_recuperer_logo($id_document,$seconde=2){
	spip_log("SPIPMOTION : recuperation d un logo du document $id_document","spipmotion");
	if(!intval($id_document)){
		return false;
	}

	include_spip('inc/documents');
	include_spip('inc/filtres_images_mini');
	$retour = 0;
	$document = sql_fetsel("docs.id_orig,docs.hasvideo,docs.id_document,docs.fichier,docs.duree,docs.hauteur,docs.largeur", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".sql_quote($id_document));
	$chemin_court = $document['fichier'];
	$chemin = get_spip_doc($chemin_court);
	
	if($document['hasvideo'] == 'oui'){
		$vignette = false;
		if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui'){
			$spipmotion_sh = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion_vignette.sh'; 
		}else{
			$spipmotion_sh = find_in_path('script_bash/spipmotion_vignette.sh');
		}
		while(!$vignette && ($seconde < $document['duree'])){
			$string_temp = "$id-$type-$id_document";
			$query = md5($string_temp);
			$dossier_temp = _DIR_VAR;
			$fichier_temp = "$dossier_temp$query.jpg";
			$cmd_vignette = $spipmotion_sh.' --e '.$chemin.' --size '.$document['largeur'].'x'.$document['hauteur'].' --s '.$fichier_temp.' --ss '.$seconde;
			$lancement_vignette = exec($cmd_vignette,$retour_vignette,$retour_int);
			if($retour_int >= 126){
				$erreur = _T('spipmotion:erreur_script_spipmotion_non_executable');
				spip_log($erreur,'spipmotion');
				//ecrire_fichier($fichier_log,$erreur);
			}
			
			if($retour_int == 0){
				$vignette = true;
				if(!file_exists($fichier_temp) OR (filesize($fichier_temp) == 0)){
					spip_log('on se casse?','spipmotion');
					return false;
				}else{
					$img_finale = $fichier_temp;
					$mode = 'vignette';
					$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
					if(defined('_DIR_PLUGIN_FONCTIONS_IMAGES')){
						include_spip('fonctions_images_fonctions');
						if($retour>10){
							if($document['id_orig'] == '0'){
								$versions = sql_select('id_document,id_vignette','spip_documents','id_orig='.intval($document['id_document']),'','taille DESC');
							}
							else{
								$versions = sql_select('id_document,id_vignette','spip_documents','id_orig='.intval($document['id_orig']));
							}
							while($version = sql_fetch($versions)){
								spip_log($version,'spipmotion');
								if(intval($version['id_vignette']) > 0){
									$vignette = sql_getfetsel('fichier','spip_documents','id_document='.intval($version['id_vignette']));
									$vignette = get_spip_doc($vignette);
									$x = $ajouter_documents($vignette, $vignette,
								    	$type, $id, $mode, $id_document, $actifs);
								    return $x;
								}
							}
							return false;
						}else if(!filtrer('image_monochrome',$fichier_temp)){
								unlink($img_finale);
								$frame = $frame+50;
								$retour++;
							}else if(file_exists($img_finale)){
								$x = $ajouter_documents($img_finale, $img_finale,
									    $type, $id, $mode, $id_document, $actifs);
								unlink($img_finale);
								$vignette = true;
							}else{
								return false;
							}
						}else{
							if(file_exists($img_finale)){
								$x = $ajouter_documents($img_finale, $img_finale,
									    $type, $id, $mode, $id_document, $actifs);
								unlink($img_finale);
								$vignette = true;
							}
						}
					}
				}else{
					return false;	
				}
		}
	}
	return $x;
}
?>