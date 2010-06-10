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

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_encodage_dist($source,$attente){
	  return encodage($source,$attente);
}

/**
 * Fonction de lancement de l'encodage
 *
 * @param array $source Les informations du fichier source
 * @param int $doc_attente id_spipmotion_attente L'id de la file d'attente
 */
function encodage($source,$doc_attente){
	/**
	 * On change le statut d'encodage à en_cours pour
	 * - changer les messages
	 * - indiquer si nécessaire le statut
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
	$query = "$fichier-$extension_attente-".date('Y_m_d_H-i-s');
	$dossier = _DIR_TMP.'spipmotion/';
	$fichier_final = substr($fichier,0,-(strlen($source['extension'])+1)).'-encoded.'.$extension_attente;

	if(!is_dir($dossier)){
		sous_repertoire(_DIR_TMP,'spipmotion');
	}
	$fichier_temp = "$dossier$query.$extension_attente";
	$fichier_log = "$dossier$query.log";
	spip_log("le nom temporaire durant l'encodage est $fichier_temp","spipmotion");

	/**
	 * $texte est le contenu du fichier de preset que l'on passe à la commande
	 * Certaines valeurs ne fonctionnent pas (et doivent être passées à la commande directement)
	 * comme:
	 * s = la taille
	 * r = le nombre de frames par secondes
	 * ac = le nombre de channels audio (ne provoquent pas d'erreurs mais ne passent pas)
	 */
	$texte = '';

	/**
	 * Quelques définitions communes aux videos et sons
	 * Vérifications de certaines options afin qu'elles ne cassent pas les encodages
	 */

	/**
	 * Correction des bitrates audios si nécessaires
	 */
	if(intval($source['audiobitrate']) && (intval($source['audiobitrate']) < lire_config("spipmotion/bitrate_audio_$extension_attente","64"))){
		$audiobitrates = array('32','64','96','128','192','256');
		if(!in_array($source['audiobitrate'],$audiobitrates)){
			$bitrate_final = '32';
			foreach($audiobitrates as $bitrate){
				if($source['audiobitrate'] > $bitrate){
					$bitrate_final = $bitrate;
				}
			}
			$abitrate = $bitrate_final;
		}else{
			$abitrate = $source['audiobitrate'];
		}
	}else{
		$abitrate = lire_config("spipmotion/bitrate_audio_$extension_attente","64");
	}

	$texte .= "ab=".$abitrate."000\n";
	$audiobitrate = "--audiobitrate ".$abitrate;

	/**
	 * Vérification des samplerates
	 */
	if(intval($source['audiosamplerate']) && (intval($source['audiosamplerate']) < lire_config("spipmotion/frequence_audio_$extension_attente","22050"))){
		$audiosamplerates = array('11025','16000','22050','44100','48000');
		if(!in_array($source['audiosamplerate'],$audiosamplerates)){
			$audiosamplerate_final = '11025';
			foreach($audiosamplerates as $samplerate){
				if($source['audiosamplerate'] >= $samplerate){
					$audiosamplerate_final = $samplerate;
				}
			}
			$samplerate = $audiosamplerate_final;
		}else{
			$samplerate = $source['audiosamplerate'];
		}
	}else{
		$samplerate = lire_config("spipmotion/frequence_audio_$extension_attente","22050");
	}
	$audiofreq = "--audiofreq ".$samplerate;
	$texte .= "ar=$samplerate\n";

	/**
	 * On passe en stereo ce qui a plus de 2 canaux et ce qui a un canal et dont
	 * le format choisi est vorbis (l'encodeur vorbis de ffmpeg ne gère pas le mono)
	 */
	if(($source['audiochannels'] > 2) OR (in_array($extension_attente,array('ogg','ogv','oga')) && ($source['audiochannels'] < 2))){
		spip_log('on passe en deux canaux','test');
		$audiochannels = 2;
	}else{
		$audiochannels = $source['audiochannels'];
	}

	$texte .= "ac=$audiochannels\n";
	$audiochannels = "--ac $audiochannels";

	/**
	 * Cas d'un fichier audio
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_audios_encodage',array()))){
		/**
		 * Encodage du son
		 */
		$encodage = find_in_path('script_bash/spipmotion.sh').' --e '.$chemin.' --s '.$fichier_temp.' '.$audiobitrate.' '.$audiofreq.' '.$audiochannels.' --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg").' &> '.$fichier_log;
		spip_log("$encodage",'spipmotion');
		$lancement_encodage = exec($encodage,$retour);
		spip_log($retour,'spipmotion');
		spip_log("l'encodage est terminé",'spipmotion');
		if(filesize($fichier_temp) > 100){
			$encodage_ok = true;
		}else{
			spip_log("l'encodage est en erreur",'spipmotion');
			/**
			 * Analyse des erreurs...
			 * On a créé un fichier de log
			 * $fichier_log = "$dossier$query.log";
			 */
		}
	}

	/**
	 * Cas d'un fichier vidéo
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){

		if(($source['largeur'] == 0) OR ($source['hauteur'] == 0) OR ($source['audiochannels'] == 0)){
			$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
			$recuperer_infos($source['id_document']);
			$source = sql_fetsel('*','spip_documents','id_document ='.intval($source['id_document']));
			if(($source['largeur'] == 0) OR ($source['hauteur'] == 0)){
				sql_updateq("spip_spipmotion_attentes",array('encode'=>'non'),"id_spipmotion_attente=".intval($doc_attente));
				return false;
			}
		}
		/**
		 * Calcul de la hauteur en fonction de la largeur souhaitée
		 * et de la taille de la video originale
		 */
		$width = $source['largeur'];
		$height = $source['hauteur'];
		$width_finale = lire_config("spipmotion/width_$extension_attente",480);

		if($width < $width_finale){
			$width_finale = $width;
			$height_finale = $height;
		}
		else{
			$height_finale = intval(round($source['hauteur']/($source['largeur']/$width_finale)));
		}

		/**
		 * Pour certains codecs (libx264 notemment), width et height doivent être
		 * divisibles par 2
		 */
		if(!is_int($width_finale/2)){
			$width_finale = $width_finale +1;
		}
		if(!is_int($height_finale/2)){
			$height_finale = $height_finale +1;
		}

		$video_size = "--size ".$width_finale."x".$height_finale;

		/**
		 * Définition du framerate d'encodage
		 * - Si le framerate de la source est supérieur à celui de la configuration souhaité, on prend celui de la configuration
		 * - Sinon on garde le même que la source
		 *
		 * TODO faire de même pour le son
		 */
		$texte .= lire_config("spipmotion/acodec_$extension_attente") ? "acodec=".lire_config("spipmotion/acodec_$extension_attente")."\n":'';
		$acodec = lire_config("spipmotion/acodec_$extension_attente") ? "--acodec ".lire_config("spipmotion/acodec_$extension_attente") :'';
		$texte .= lire_config("spipmotion/vcodec_$extension_attente") ? "vcodec=".lire_config("spipmotion/vcodec_$extension_attente")."\n":'';
		$vcodec .= lire_config("spipmotion/vcodec_$extension_attente") ? "--vcodec ".lire_config("spipmotion/vcodec_$extension_attente") :'';

		if(intval($source['framerate']) && (intval($source['framerate']) < lire_config("spipmotion/fps_$extension_attente","15"))){
			$fps_num = $source['framerate'];
		}else{
			$fps_num = lire_config("spipmotion/fps_$extension_attente","15");
		}
		$fps = "--fps $fps_num";

		/**
		 * Définition des bitrates
		 * On vérifie ceux de la source et on compare à ceux souhaités dans la conf
		 * Si la source est inférieure, on utilise ceux de la source
		 */
		if(intval($source['videobitrate']) && (intval($source['videobitrate']) < lire_config("spipmotion/bitrate_$extension_attente","448"))){
			$vbitrate = $source['videobitrate'];
		}else{
			$vbitrate = lire_config("spipmotion/bitrate_$extension_attente","448");
		}

		$texte .= "vb=".$vbitrate."000\n";
		$bitrate = "--bitrate ".$vbitrate;

		if($vcodec == '--vcodec libx264'){
			$vpre2 = '--vpre2 hq';
			$vpre = '--vpre ipod640';
		}
		$fichier_texte = "$dossier$query.txt";

		ecrire_fichier($fichier_texte,$texte);

		/**
		 * Encodage de la video
		 * Si l'encodeur choisi est ffmpeg2theora et qu'il existe toujours, on l'utilise
		 * sinon on utilise notre script pour ffmpeg
		 */
		if((lire_config("spipmotion/encodeur_$extension_attente",'') == 'ffmpeg2theora') && (lire_config('spipmotion_ffmpeg2theora/version') > 0)){
			$encodage = "ffmpeg2theora $chemin -v 8 -V $vbitrate -A $abitrate -H $samplerate --max_size ".$width_finale."x".$height_finale." -F $fps_num --optimize --nice 9 -o $fichier_temp &> $fichier_log";
		}else{
			$encodage = find_in_path('script_bash/spipmotion.sh')." $audiofreq $video_size --e $chemin $acodec $vcodec $fps $audiobitrate $audiochannels $bitrate $vpre $vpre2 --s $fichier_temp --fpre $fichier_texte --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." &> $fichier_log";
		}
		spip_log($encodage,'spipmotion');
		$lancement_encodage = exec($encodage,$retour);

		spip_log($retour,'spipmotion');

		if(filesize($fichier_temp) > 100){
			$encodage_ok = true;
		}else{
			spip_log("l'encodage est en erreur",'spipmotion');
			/**
			 * Analyse des erreurs...
			 * On a créé un fichier de log
			 * $fichier_log = "$dossier$query.log";
			 */
		}
		if(is_readable($fichier_temp) && ($extension_attente == 'flv') && $encodage_ok){
			/**
			 * Inscrire les metadatas dans la video finale
			 */
			$metadatas_flv = 'flvtool2 -Ux '.$fichier_temp;
			exec($metadatas_flv,$retour);
			spip_log($retour,'spipmotion');
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
		unlink($fichier_temp);

		sql_updateq("spip_documents",array('id_orig'=>$attente['id_document']),'id_document='.intval($x));

		/**
		 * Invalidation du cache
		 */
		if ($invalider) {
			include_spip('inc/invalideur');
			suivre_invalideur("0",true);
		}
	}else{
		sql_updateq("spip_spipmotion_attentes",array('encode'=>'non'),"id_spipmotion_attente=".intval($doc_attente));
	}

	return $x;
}
?>