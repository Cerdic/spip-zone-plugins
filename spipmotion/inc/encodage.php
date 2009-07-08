<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 * 
 * Fonction d'encodage des médias
 * 
 * Auteurs :
 * Quentin Drouet
 * 2006-2009 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Faire l'encodage d'une video en .flv
//

function inc_encodage_dist($source,$attente){
	  return encodage($source,$attente);
}

function encodage($source,$video_attente){
	spip_log($source,'spipmotion');
	
	/**
	 * On change le statut d'encodage à en_cours pour changer les messages et indiquer si nécessaire le statut
	 */
	sql_updateq("spip_spipmotion_attentes",array('encode'=>'en_cours'),"id_spipmotion_attente=".intval($video_attente));
	
	include_spip('inc/documents');
	$chemin = get_spip_doc($source['fichier']);

	spip_log("encodage de $chemin","spipmotion");
	
	/**
	 * Calcul de la hauteur en fonction de la largeur souhaitée et de la taille de la video originale
	 */ 	
	$width = $source['largeur'];
	$height = $source['hauteur'];
	$width_finale = lire_config('spipmotion/width') ? lire_config('spipmotion/width') : 480;
	
	if($width<$width_finale){
		$width_finale = $width;
		$height_finale = $height;
	}
	else{
		$height_finale = $source['hauteur']/($source['largeur']/$width_finale);
	}
	
	spip_log("document original ($chemin) = $width/$height - document final = $width_finale/$height_finale",'spipmotion');
	
	$fichier = basename($source['fichier']);
	$string = "$fichier-$width-$height";
	$query = md5($string);
	$dossier = _DIR_VAR;
	$fichier_temp = "$dossier$query.flv";
	spip_log("le nom temporaire durant l'encodage est $fichier_temp","spipmotion");
	
	/**
	 * Définition du framerate d'encodage
	 * - Si le framerate de la source est supérieur à celui de la configuration souhaité, on prend celui de la configuration
	 * - Sinon on garde le même que la source
	 * 
	 * TODO faire de même pour le son
	 */
	if(intval($source['framerate']) < lire_config("spipmotion/fps","15")){
		$fps = $source['framerate'];
	}else{
		$fps = lire_config("spipmotion/fps","15");
	}
	
	/**
	 * Encodage de la video
	 */
	$encodageflv = find_in_path('script_bash/spipmotion.sh').' --e '.$chemin.' --s '.$fichier_temp.' --size '.$width_finale.'x'.$height_finale.' --bitrate '.lire_config("spipmotion/bitrate","448").' --audiobitrate '.lire_config("spipmotion/bitrate_audio","64").' --audiofreq '. lire_config("spipmotion/frequence_audio","22050").' --fps '.$fps.' --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg");
	spip_log("$encodageflv",'spipmotion');
	$lancement_encodage = exec($encodageflv,$retour);
	spip_log($retour,'spipmotion');
	spip_log("l'encodage est terminé",'spipmotion');
	
	/**
	 * Inscrire les metadatas dans la video finale
	 */
	$fichier_final = substr($fichier,0,-4).'.flv';
	$metadatas_flv = 'flvtool2 -Ux '.$fichier_final;
	shell_exec($metadatas_flv,'spipmotion');

	/**
	 * Ajout du nouveau document dans la base de donnée de SPIP
	 * NB : la récupération des infos et du logo est faite automatiquement par le pipeline post-edition appelé par l'ajout du document
	 */
	
	$mode = 'document';
	$invalider = true;
	
	$attente = sql_fetsel("*","spip_spipmotion_attentes","id_spipmotion_attente=".intval($video_attente));
	$type_doc = $attente['objet'];
	$id_objet = $attente['id_objet'];
	spip_log($attente,"spipmotion");
	$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
	$x = $ajouter_documents($fichier_temp, $fichier_final, $type_doc, $id_objet, $mode, $id_document, $actifs);
	spip_log("on ajoute le nouveau fichier qui devient $x","spipmotion");
	unlink($fichier_temp);
	
	sql_updateq("spip_spipmotion_attentes",array('encode'=>'oui'),"id_spipmotion_attente=".intval($video_attente));
	
	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'spipmotion');
	}
	
	return;
}
?>