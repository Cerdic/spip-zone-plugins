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

function inc_encodage_dist($source,$attente){
	  return encodage($source,$attente);
}

function encodage($source,$doc_attente){
	/**
	 * On change le statut d'encodage à en_cours pour changer les messages et indiquer si nécessaire le statut
	 */
	sql_updateq("spip_spipmotion_attentes",array('encode'=>'en_cours'),"id_spipmotion_attente=".intval($doc_attente));

	$attente = sql_fetsel("*","spip_spipmotion_attentes","id_spipmotion_attente=".intval($doc_attente));
	$extension_attente = $attente['extension'];
	$type_doc = $attente['objet'];
	$id_objet = $attente['id_objet'];
	spip_log($attente,"spipmotion");

	include_spip('inc/documents');
	$chemin = get_spip_doc($source['fichier']);
	spip_log("encodage de $chemin","spipmotion");

	$fichier = basename($source['fichier']);
	$string = "$fichier-$width-$height";
	$query = md5($string);
	$dossier = _DIR_VAR;
	$fichier_final = substr($fichier,0,-(strlen($source['extension'])+1)).'-encoded.'.$extension_attente;

	$fichier_temp = "$dossier$query.$extension_attente";
	spip_log("le nom temporaire durant l'encodage est $fichier_temp","spipmotion");

	/**
	 * Cas d'un fichier audio
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_audios_encodage',array()))){
		/**
		 * Encodage du son
		 */
		$encodage = find_in_path('script_bash/spipmotion.sh').' --e '.$chemin.' --s '.$fichier_temp.' --audiobitrate '.lire_config("spipmotion/bitrate_audio_$extension_attente","64").' --audiofreq '. lire_config("spipmotion/frequence_audio_$extension_attente","22050").' --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg");
		spip_log("$encodage",'spipmotion');
		$lancement_encodage = exec($encodage,$retour);
		spip_log($retour,'spipmotion');
		spip_log("l'encodage est terminé",'spipmotion');
	}

	/**
	 * Cas d'un fichier vidéo
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
		/**
		 * Calcul de la hauteur en fonction de la largeur souhaitée et de la taille de la video originale
		 */
		$width = $source['largeur'];
		$height = $source['hauteur'];
		$width_finale = lire_config("spipmotion/width_$extension_attente") ? lire_config("spipmotion/width_$extension_attente") : 480;

		if($width<$width_finale){
			$width_finale = $width;
			$height_finale = $height;
		}
		else{
			$height_finale = $source['hauteur']/($source['largeur']/$width_finale);
		}

		spip_log("document original ($chemin) = $width/$height - document final = $width_finale/$height_finale",'spipmotion');

		/**
		 * Définition du framerate d'encodage
		 * - Si le framerate de la source est supérieur à celui de la configuration souhaité, on prend celui de la configuration
		 * - Sinon on garde le même que la source
		 *
		 * TODO faire de même pour le son
		 */
		if(intval($source['framerate']) && (intval($source['framerate']) < lire_config("spipmotion/fps_$extension_attente","15"))){
			$fps = $source['framerate'];
		}else{
			$fps = lire_config("spipmotion/fps_$extension_attente","15");
		}

		/**
		 * Définition des bitrates
		 * On vérifie ceux de la source et on compare à ceux souhaités dans la conf
		 * Si la source est inférieure, on utilise ceux de la source
		 */
		if(intval($source['videobitrate']) && (intval($source['videobitrate']) < lire_config("spipmotion/bitrate_$extension_attente","448"))){
			$bitrate = $source['videobitrate'];
		}else{
			$bitrate = lire_config("spipmotion/bitrate_$extension_attente","448");
		}

		if(intval($source['audiobitrate']) && (intval($source['audiobitrate']) < lire_config("spipmotion/bitrate_audio_$extension_attente","64"))){
			$audiobitrate = $source['audiobitrate'];
		}else{
			$audiobitrate = lire_config("spipmotion/bitrate_audio_$extension_attente","64");
		}

		if(intval($source['audiosamplerate']) && (intval($source['audiosamplerate']) < lire_config("spipmotion/frequence_audio_$extension_attente","22050"))){
			if (intval($source['audiobitrate']) < 11025){
				$audiosamplerate = 11025;
			}else{
				$audiosamplerate = $source['audiosamplerate'];
			}
		}else{
			$audiosamplerate = lire_config("spipmotion/frequence_audio_$extension_attente","22050");
		}
		/**
		 * Encodage de la video
		 */
		$encodage = find_in_path('script_bash/spipmotion.sh').' --e '.$chemin.' --s '.$fichier_temp.' --size '.$width_finale.'x'.$height_finale.' --bitrate '.$bitrate.' --audiobitrate '.$audiobitrate.' --audiofreq '.$audiosamplerate.' --fps '.$fps.' --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg");
		spip_log("$encodage",'spipmotion');
		$lancement_encodage = exec($encodage,$retour);

		spip_log($retour,'spipmotion');
		spip_log("l'encodage est terminé",'spipmotion');

		if(count($retour) > 0){
			$encodage_ok = true;
		}
		if(($extension_attente == 'flv') && $encodage_ok){
			/**
			 * Inscrire les metadatas dans la video finale
			 */
			$metadatas_flv = 'flvtool2 -Ux '.$fichier_temp;
			exec($metadatas_flv,$retour);
			spip_log($metadatas_flv);
		}

	}

	if($encodage_ok){
		/**
		 * Ajout du nouveau document dans la base de donnée de SPIP
		 * NB : la récupération des infos et du logo est faite automatiquement par
		 * le pipeline post-edition appelé par l'ajout du document
		 */
		$mode = 'document';
		$invalider = true;

		sql_updateq("spip_spipmotion_attentes",array('encode'=>'oui'),"id_spipmotion_attente=".intval($doc_attente));

		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$x = $ajouter_documents($fichier_temp, $fichier_final, $type_doc, $id_objet, $mode, '', $actif,'','','');
		spip_log("on ajoute le nouveau fichier qui devient $x","spipmotion");
		unlink($fichier_temp);

		sql_updateq("spip_documents",array('id_orig'=>$attente['id_document']),'id_document='.intval($x));

		if ($invalider) {
			include_spip('inc/invalideur');
			suivre_invalideur("0",true);
			spip_log('invalider', 'spipmotion');
		}
	}

	return $x;
}
?>