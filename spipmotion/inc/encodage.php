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

function inc_encodage_dist($source,$attente,$format=''){
	/**
	 * On vérifie que l'on n'a pas déjà une version dans le format souhaité
	 * Si oui on la supprime avant de la réencoder
	 */
	if($id_document = sql_getfetsel('id_document','spip_documents','id_orig='.intval($source['id_document']).' AND extension='.sql_quote($format))){
		spip_log("Il faut supprimer $id_document",'spipmotion');
		$v = sql_fetsel("id_document,id_vignette,fichier","spip_documents","id_document=".intval($id_document));

		include_spip('inc/documents');
		/**
		 * On ajoute l'id_document dans la liste des documents
		 * à supprimer de la base
		 * On supprime le fichier correspondant
		 */
		$liste[] = $v['id_document'];
		if (@file_exists($f = get_spip_doc($v['fichier']))) {
			supprimer_fichier($f);
		}

		/**
		 * Si le document a une vignette :
		 * - On ajoute l'id_document dans la liste à supprimer
		 * - On supprime le fichier correspondant à la vignette
		 */
		if($v['id_vignette'] > 0){
			spip_log("on supprime sa vignette également","spipmotion");
			$liste[] = $v['id_vignette'];
			$fichier = sql_getfetsel('fichier','spip_documents','id_document='.$v['id_vignette']);
			if (@file_exists($f = get_spip_doc($fichier))) {
				supprimer_fichier($f);
			}
		}

		if(is_array($liste)){
			$in = sql_in('id_document', $liste);
			sql_delete("spip_documents", $in);
			sql_delete("spip_documents_liens", $in);
			sql_delete("spip_spipmotion_attentes", "id_document=".intval($id_document).' AND encode != '.sql_quote('oui').' AND extension='.sql_quote($format).' AND id_spipmotion_attente!='.intval($attente));
		}

		include_spip('inc/invalideur');
		suivre_invalideur(1);
	}
	/**
	 * Puis on lance l'encodage
	 */
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
	 * Si le chemin vers le binaire FFMpeg n'existe pas,
	 * la configuration du plugin crée une meta spipmotion_casse
	 */
	if($GLOBALS['meta']['spipmotion_casse'] == 'oui')
		return;

	/**
	 * On change le statut d'encodage à en_cours pour
	 * - changer les messages sur le site (ce media est en cours d'encodage par exemple)
	 * - indiquer si nécessaire le statut
	 */
	$infos_encodage = array('debut_encodage' => time());
	sql_updateq("spip_spipmotion_attentes",array('encode'=>'en_cours','infos' => serialize($infos_encodage)),"id_spipmotion_attente=".intval($doc_attente));

	$attente = sql_fetsel("*","spip_spipmotion_attentes","id_spipmotion_attente=".intval($doc_attente));
	$extension_attente = $attente['extension'];
	$type_doc = $attente['objet'];
	$id_objet = $attente['id_objet'];

	include_spip('inc/documents');
	$chemin = get_spip_doc($source['fichier']);
	spip_log("encodage de $chemin","spipmotion");

	$fichier = basename($source['fichier']);
	/**
	 * Génération des noms temporaires et finaux
	 * - Le nom du dossier temporaire (tmp/spipmotion)
	 * - Le nom du fichier final (nom_du_fichier-encoded.ext)
	 * - Le nom du fichier temporaire durant l'encodage
	 * - Le nom du fichier de log généré pour chaque fichier
	 */
	$query = "$fichier-$extension_attente-".date('Y_m_d_H-i-s');
	$dossier = _DIR_TMP.'spipmotion/';
	$fichier_final = substr($fichier,0,-(strlen($source['extension'])+1)).'-encoded.'.$extension_attente;
	$fichier_temp = "$dossier$query.$extension_attente";
	$fichier_log = "$dossier$query.log";
	spip_log("le nom temporaire durant l'encodage est $fichier_temp","spipmotion");

	/**
	 * On crée le dossier temporaire s'il n'existe pas
	 */
	if(!is_dir($dossier)){
		sous_repertoire(_DIR_TMP,'spipmotion');
	}

	/**
	 * Si on n'a pas l'info hasaudio c'est que la récupération d'infos n'a pas eu lieu
	 * On relance la récupération d'infos sur le document
	 * On refais une requête pour récupérer les nouvelles infos
	 */
	if(!$source['hasaudio'] OR !$source['hasvideo']){
		$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
		$recuperer_infos($source['id_document']);
		$source = sql_fetsel('*','spip_documents','id_document ='.intval($source['id_document']));
	}

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
	 * Correction des paramètres audio
	 * Uniquement s'il y a une piste audio
	 * -* codec à utiliser
	 * -* bitrate
	 * -* samplerate
	 * -* nombre de canaux
	 */
	if($source['hasaudio'] == 'oui'){
		$texte .= lire_config("spipmotion/acodec_$extension_attente") ? "acodec=".lire_config("spipmotion/acodec_$extension_attente")."\n":'';
		$acodec = lire_config("spipmotion/acodec_$extension_attente") ? "--acodec ".lire_config("spipmotion/acodec_$extension_attente") :'';

		if(in_array(lire_config("spipmotion/acodec_$extension_attente",''),array('vorbis','libvorbis'))){
			$qualite = lire_config("spipmotion/qualite_audio_$extension_attente",'4');
			$audiobitrate_ffmpeg2theora = "--audioquality $qualite";
			$audiobitrate_ffmpeg = "--audioquality ".$qualite;
		}else{
			if(intval($source['audiobitrate']) && (intval($source['audiobitrate']) < (lire_config("spipmotion/bitrate_audio_$extension_attente","64")*1000))){
				$audiobitrates = array('32000','64000','96000','128000','192000','256000');
				if(!in_array($source['audiobitrate'],$audiobitrates)){
					$bitrate_final = min($audiobitrates);
					foreach($audiobitrates as $bitrate){
						if($source['audiobitrate'] >= $bitrate){
							$bitrate_final = $bitrate;
						}
					}
					$abitrate = $bitrate_final;
				}else{
					$abitrate = $source['audiobitrate'];
				}
				$abitrate = floor($abitrate/1000);
			}else{
				$abitrate = lire_config("spipmotion/bitrate_audio_$extension_attente","64");
			}
			$texte .= "ab=".$abitrate."000\n";
			$audiobitrate_ffmpeg = $audiobitrate_ffmpeg2theora = "--audiobitrate ".$abitrate;
		}

		/**
		 * Vérification des samplerates
		 */
		if(intval($source['audiosamplerate']) && (intval($source['audiosamplerate']) < lire_config("spipmotion/frequence_audio_$extension_attente","22050"))){
			$audiosamplerates = array('4000','8000','11025','16000','22050','24000','32000','44100','48000');
			/**
			 * libmp3lame ne gère pas tous les samplerates
			 */
			if($acodec == '--acodec libmp3lame'){
				unset($audiosamplerates[0]);
				unset($audiosamplerates[1]);
				unset($audiosamplerates[3]);
				unset($audiosamplerates[5]);
				unset($audiosamplerates[6]);
				unset($audiosamplerates[8]);
			}
			if(!in_array($source['audiosamplerate'],$audiosamplerates)){
				$audiosamplerate_final = min($audiosamplerates);
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
		if(($source['audiochannels'] > 2)
			OR (in_array($extension_attente,array('ogg','ogv','oga'))
				&& ($source['audiochannels'] < 2)
				&& (lire_config("spipmotion/encodeur_$extension_attente",'') != 'ffmpeg2theora'))){
			spip_log('on passe en deux canaux','spipmotion');
			$audiochannels = 2;
		}else{
			spip_log($source['audiochannels'].' canaux','spipmotion');
			$audiochannels = $source['audiochannels'];
		}

		if(intval($audiochannels) >= 1){
			$texte .= "ac=$audiochannels\n";
			$audiochannels_ffmpeg = "--ac $audiochannels";
		}
	}

	if($GLOBALS['meta']['spipmotion_safe_mode'] == 'oui'){
		$spipmotion_sh = $GLOBALS['meta']['spipmotion_safe_mode_exec_dir'].'/spipmotion.sh'; 
	}else{
		$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');
	}
	
	/**
	 * Encodage
	 * Cas d'un fichier audio
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_audios_encodage',array()))){
		/**
		 * Encodage du son
		 */
		$encodage = $spipmotion_sh.' --e '.$chemin.' --s '.$fichier_temp.' '.$acodec.' '.$audiobitrate_ffmpeg.' '.$audiofreq.' '.$audiochannels_ffmpeg.' --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg").' &> '.$fichier_log;
		spip_log("$encodage",'spipmotion');
		$lancement_encodage = exec($encodage,$retour,$retour_int);
		spip_log($retour_int,'spipmotion');
		if($retour_int == 0){
			$encodage_ok = true;
		}
	}

	/**
	 * Encodage
	 * Cas d'un fichier vidéo
	 *
	 * On corrige les paramètres video avant de lancer l'encodage
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
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
		 * On le fait pour tous les cas pour éviter toute erreur
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
		 */
		$texte .= lire_config("spipmotion/vcodec_$extension_attente") ? "vcodec=".lire_config("spipmotion/vcodec_$extension_attente")."\n":'';
		$vcodec .= lire_config("spipmotion/vcodec_$extension_attente") ? "--vcodec ".lire_config("spipmotion/vcodec_$extension_attente") :'';

		if(intval($source['framerate']) && (intval($source['framerate']) < lire_config("spipmotion/fps_$extension_attente","30"))){
			$fps_num = $source['framerate'];
		}else{
			$fps_num = (intval(lire_config("spipmotion/fps_$extension_attente")) > 0) ? lire_config("spipmotion/fps_$extension_attente") : $source['framerate'];
		}
		$fps = "--fps $fps_num";

		/**
		 * Définition des bitrates
		 * On vérifie ceux de la source et on compare à ceux souhaités dans la conf
		 * Si la source est inférieure, on utilise ceux de la source en utilisant l'option -sameq
		 * ffmpeg2theora lui a besoin d'une estimation de bitrate
		 */
		if(intval($source['videobitrate']) && (intval($source['videobitrate']) < (lire_config("spipmotion/bitrate_$extension_attente","448"))*1000)){
			$vbitrate = null;
			$infos_sup_normal .= ' -sameq ';
			$bitrate_ffmpeg2theora = "-V ".$source['videobitrate'];
		}else{
			$vbitrate = lire_config("spipmotion/bitrate_$extension_attente","448");
			$bitrate_ffmpeg2theora = "-V $vbitrate";
		}

		$texte .= intval($vbitrate) ? "vb=".$vbitrate."000\n" : "";
		$bitrate = intval($vbitrate) ? "--bitrate ".$vbitrate : "";

		/**
		 * Paramètres supplémentaires pour encoder en h264
		 */
		if($vcodec == '--vcodec libx264'){
			$preset_quality = lire_config("spipmotion/vpreset_$extension_attente",'hq');
			if(in_array('--enable-pthreads',lire_config('spipmotion_compiler/configuration'))){
				$infos_sup_normal .= "-threads 0";
			}
			/**
			 * Encodage pour Ipod
			 * http://rob.opendot.cl/index.php/useful-stuff/ipod-video-guide/
			 */
			if(lire_config("spipmotion/format_$extension_attente",'ipod') == 'ipod'){
				$infos_sup_normal .= ' -vpre ipod640';
			}
			/**
			 * Encodage pour PSP
			 * http://rob.opendot.cl/index.php/useful-stuff/psp-video-guide/
			 */
			if(lire_config("spipmotion/format_$extension_attente",'ipod') == 'psp'){
				$infos_sup_normal .= ' -vpre main';
				$infos_sup_normal .= ' -level 21';
				$infos_sup_normal .= ' -refs 2';
			}
			$infos_sup_normal .= " -aspect $width_finale:$height_finale";
			$infos_sup_normal .= ' -f '.lire_config("spipmotion/format_$extension_attente",'ipod');
		}

		$fichier_texte = "$dossier$query.txt";

		ecrire_fichier($fichier_texte,$texte);

		/**
		 * Encodage de la video
		 * Si l'encodeur choisi est ffmpeg2theora et qu'il existe toujours, on l'utilise
		 * sinon on utilise notre script pour ffmpeg
		 */
		$passes = lire_config("spipmotion/passes_$extension_attente",'1');
		spip_log("on est en $passes passe(s)","spipmotion");
		if((lire_config("spipmotion/encodeur_$extension_attente",'') == 'ffmpeg2theora') && (lire_config('spipmotion_ffmpeg2theora/version') > 0)){
			if($passes == 2)
				$deux_passes = '--two-pass';
			$encodage = "ffmpeg2theora $chemin -v ".lire_config('spipmotion/qualite_video_ffmpeg2theora_'.$extension_attente,7)." $bitrate_ffmpeg2theora --soft-target $audiobitrate_ffmpeg2theora -H $samplerate -c $audiochannels --max_size ".$width_finale."x".$height_finale." $deux_passes -F $fps_num --optimize --nice 9 -o $fichier_temp &> $fichier_log";
			spip_log($encodage,'spipmotion');
			$lancement_encodage = exec($encodage,$retour,$retour_int);
			spip_log($retour_int,'spipmotion');
		}else{
			if(($passes == "2") && ((($vcodec == '--vcodec libx264') && ($preset_quality != 'hq')) OR ($vcodec == '--vcodec flv') OR ($extension_attente == 'webm'))){
				spip_log('on encode en 2 passes','spipmotion');
				$preset_1 = $preset_quality ? '-vpre '.$preset_quality.'_firstpass' : '';
				$infos_sup_normal_1 = "--params_supp \"-an $preset_1 -passlogfile $query $infos_sup_normal\"";
				$encodage_1 = $spipmotion_sh." --pass 1 $video_size --e $chemin $vcodec $fps $bitrate $infos_sup_normal_1 --s $fichier_temp --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." &> $fichier_log";
				spip_log($encodage_1,'spipmotion');
				$lancement_encodage_1 = exec($encodage_1,$retour_1,$retour_int_1);
				spip_log($retour_int_1,'spipmotion');
				if($retour_int_1 == 0){
					$infos_sup_normal = $preset_quality ? "-vpre $preset_quality $infos_sup_normal" : $infos_sup_normal;
					$infos_sup_normal_2 = "--params_supp \"-passlogfile $query $infos_sup_normal -deinterlace\"";
					$encodage = $spipmotion_sh." --pass 2 $audiofreq $audiobitrate_ffmpeg $audiochannels_ffmpeg $video_size --e $chemin $acodec $vcodec $fps $bitrate $infos_sup_normal_2 --s $fichier_temp --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." 2> $fichier_log";
					spip_log($encodage,'spipmotion');
					$lancement_encodage = exec($encodage,$retour,$retour_int);
					spip_log($retour_int,'spipmotion');
				}else{
					$retour_int = 1;
				}
			}else{
				spip_log('on encode en 1 passe','spipmotion');
				$infos_sup_normal = $preset_quality ? "-vpre $preset_quality $infos_sup_normal":'';
				if($infos_sup_normal){
					$infos_sup_normal = "--params_supp \"$infos_sup_normal -deinterlace\"";
				}
				$encodage = $spipmotion_sh." $audiofreq $video_size --e $chemin $acodec $vcodec $fps $audiobitrate_ffmpeg $audiochannels_ffmpeg $bitrate $infos_sup_normal --s $fichier_temp --fpre $fichier_texte --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." &> $fichier_log";
				spip_log($encodage,'spipmotion');
				$lancement_encodage = exec($encodage,$retour,$retour_int);
				spip_log($retour_int,'spipmotion');
			}
		}

		if($retour_int == 0){
			$encodage_ok = true;
		}
		if(is_readable($fichier_temp) && ($extension_attente == 'flv') && $encodage_ok){
			/**
			 * Inscrire les metadatas dans la video finale
			 */
			$metadatas_flv = 'flvtool2 -Ux '.$fichier_temp;
			exec($metadatas_flv,$retour,$retour_int);
			spip_log($retour_int,'spipmotion');
		}
	}

	if($encodage_ok){
		spip_log('on ajoute le document dans la base','spipmotion');
		/**
		 * Ajout du nouveau document dans la base de donnée de SPIP
		 * NB : la récupération des infos et du logo est faite automatiquement par
		 * le pipeline post-edition appelé par l'ajout du document
		 */
		$mode = 'document';

		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$x = $ajouter_documents($fichier_temp, $fichier_final, $type_doc, $id_objet, $mode, '', $actif,'','','');

		if(intval($x) > 1){
			unlink($fichier_temp);
			
			/**
			 * Modification de la file d'attente
			 * - On marque le document comme correctement encodé
			 * - On ajoute la date de fin d'encodage
			 */
			$infos_encodage['fin_encodage'] = time();
			sql_updateq("spip_spipmotion_attentes",array('encode'=>'oui','infos' => serialize($infos_encodage)),"id_spipmotion_attente=".intval($doc_attente));

			/**
			 * Tentative de récupération d'un logo du document original
			 */
			if((sql_getfetsel('id_vignette','spip_documents','id_document = '.intval($x)) == 0) && ($source['id_vignette'] > 0)){
				$vignette = sql_fetsel('fichier,extension','spip_documents','id_document='.intval($source['id_vignette']));
				$fichier_vignette = get_spip_doc($vignette['fichier']);
				$string_tmp = basename(get_spip_doc($vignette['fichier'])).'-'.date();
				$nom_vignette = md5($string_tmp).'.'.$vignette['extension'];
				$x2 = $ajouter_documents($fichier_vignette, $nom_vignette, '', '', 'vignette', $x, $actif,'','','');
			}
			/**
			 * Champs que l'on souhaite réinjecter depuis l'original ni depuis un ancien encodage
			 */
			$champs_recup = array('titre' => '0','descriptif' => '0');
			if(_DIR_PLUGIN_PODCAST)
				$champs_recup['podcast'] = 0;
				$champs_recup['explicit'] = 0;
			if(_DIR_PLUGIN_LICENCES)
				$champs_recup['id_licence'] = 0;
			if(_DIR_PLUGIN_GESTDOC)
				$champs_recup['credits'] = 0;
				
			$modifs = array_intersect_key($source, $champs_recup);
			
			/**
			 * On ajoute l'id dur document original id_orig
			 */
			$modifs['id_orig'] = $attente['id_document'];
			
			include_spip('inc/modifier');
			revision_document($x, $modifs);
		}else{
			sql_updateq("spip_spipmotion_attentes",array('encode'=>'non'),"id_spipmotion_attente=".intval($doc_attente));
			spip_log('Il y a une erreur, le fichier n est pas copié','spipmotion');
		}
	}
	/**
	 * Si l'encodage n'est pas ok ...
	 * On donne un statut "erreur" dans la file afin de ne pas la bloquer
	 */
	else{
		$infos_encodage['fin_encodage'] = time();
		$infos_encodage['log'] = spip_file_get_contents($fichier_log);
		sql_updateq("spip_spipmotion_attentes",array('encode'=>'erreur','infos' => serialize($infos_encodage)),"id_spipmotion_attente=".intval($doc_attente));
	}

	if(file_exists(_DIR_RACINE.$query.'-0.log')){
		supprimer_fichier(_DIR_RACINE.$query.'-0.log');
	}
	/**
	 * Invalidation du cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("0",true);

	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('spipmotion_encodage', intval($doc_attente),
			array(
				'id_document' => $x,
				'source' => $source,
				'fichier_log' => $fichier_log,
			)
		);
	}
	return $x;
}
?>