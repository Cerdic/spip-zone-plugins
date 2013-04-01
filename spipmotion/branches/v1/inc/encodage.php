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

function inc_encodage_dist($source,$attente,$format=''){
	if(!is_array($GLOBALS['spipmotion_metas'])){
		$inc_meta = charger_fonction('meta', 'inc');
		$inc_meta('spipmotion_metas');
	}
	
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

	$spipmotion_compiler = @unserialize($GLOBALS['spipmotion_metas']['spipmotion_compiler']);
	$ffmpeg_version = $spipmotion_compiler['ffmpeg_version'] ? $spipmotion_compiler['ffmpeg_version'] : '0.7';
	$rep_dest = sous_repertoire(_DIR_VAR, 'cache-spipmotion');

	$attente = sql_fetsel("*","spip_spipmotion_attentes","id_spipmotion_attente=".intval($doc_attente));
	$extension_attente = $attente['extension'];
	$type_doc = $attente['objet'];
	$id_objet = $attente['id_objet'];

	$encodeur = lire_config("spipmotion/encodeur_$extension_attente",'');
	
	if(($encodeur == 'ffmpeg2theora') && $GLOBALS['meta']['spipmotion_ffmpeg2theora_casse'] == 'oui')
		$encodeur = 'ffmpeg';
	
	if($source['rotation'] == '90')
		$encodeur = 'ffmpeg';
	
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
	$dossier = sous_repertoire(_DIR_VAR, 'cache-spipmotion');
	$fichier_final = substr($fichier,0,-(strlen($source['extension'])+1)).'-encoded.'.$extension_attente;
	$fichier_temp = "$dossier$query.$extension_attente";
	$fichier_log = "$dossier$query.log";
	
	/**
	 * Si on n'a pas l'info hasaudio c'est que la récupération d'infos n'a pas eu lieu
	 * On relance la récupération d'infos sur le document
	 * On refais une requête pour récupérer les nouvelles infos
	 */
	if(!$source['hasaudio'] OR !$source['hasvideo']){
		spip_log('on récup les infos pour vérif audio','spipmotion');
		$recuperer_infos = charger_fonction('spipmotion_recuperer_infos','inc');
		$recuperer_infos($source['id_document']);
		$source = sql_fetsel('*','spip_documents','id_document ='.intval($source['id_document']));
		if(!$source['hasaudio'] OR !$source['hasvideo']){
			spip_log('La source n a ni audio ni video','spipmotion');
			return false;
		}
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
		$acodec = lire_config("spipmotion/acodec_$extension_attente") ? "--acodec ".lire_config("spipmotion/acodec_$extension_attente") :'';
		if(($encodeur == "ffmpeg") && ($acodec == "--acodec vorbis")){
			$acodec = '--acodec libvorbis';
		}
		if(in_array(lire_config("spipmotion/acodec_$extension_attente",''),array('vorbis','libvorbis'))){
			$qualite = lire_config("spipmotion/qualite_audio_$extension_attente",'4');
			$audiobitrate_ffmpeg2theora = "--audioquality $qualite";
			$audiobitrate_ffmpeg = "--audioquality $qualite";
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
			if($acodec == '--acodec libfaac'){
				unset($audiosamplerates[0]);
				unset($audiosamplerates[1]);
				unset($audiosamplerates[2]);
				unset($audiosamplerates[3]);
			}
			/**
			 * ffmpeg ne peut resampler
			 * On force le codec audio à aac s'il était à libmp3lame
			 */
			if(($source['audiochannels'] > 2) && ($encodeur != 'ffmpeg2theora')){
				$samplerate = $source['audiosamplerate'];
				if($acodec == '--acodec libmp3lame'){
					$acodec = '--acodec libfaac';
					$audiobitrate_ffmpeg = $audiobitrate_ffmpeg2theora = "--audiobitrate 128";
				}
			}else if(!in_array($source['audiosamplerate'],$audiosamplerates)){
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
			if(($source['audiochannels'] > 2) && ($encodeur != 'ffmpeg2theora')){
				$samplerate = $source['audiosamplerate'];
				if($acodec == '--acodec libmp3lame'){
					$acodec = '--acodec libfaac';
					$audiobitrate_ffmpeg = $audiobitrate_ffmpeg2theora = "--audiobitrate 128";
				}
			}else{
				$samplerate = lire_config("spipmotion/frequence_audio_$extension_attente","22050");
			}
		}
		$audiofreq = "--audiofreq ".$samplerate;
		$texte .= "ar=$samplerate\n";
		
		/**
		 * On passe en stereo ce qui a plus de 2 canaux et ce qui a un canal et dont
		 * le format choisi est vorbis (l'encodeur vorbis de ffmpeg ne gère pas le mono)
		 */
		if(in_array($extension_attente,array('ogg','ogv','oga'))
				&& ($source['audiochannels'] < 2)
				&& ($encodeur != 'ffmpeg2theora')){
			$audiochannels = 2;
		}else{
			$audiochannels = $source['audiochannels'];
		}

		if(intval($audiochannels) >= 1){
			/**
			 * Apparemment le mp3 n'aime pas trop le 5.1 channels des AC3 donc on downgrade en 2 channels en attendant
			 */
			if($extension_attente == 'mp3'){
				$texte .= "ac=2\n";
				$audiochannels_ffmpeg = "--ac 2";
			}else{
				$texte .= "ac=$audiochannels\n";
				$audiochannels_ffmpeg = "--ac $audiochannels";
			}
		}
	}

	if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui'){
		$spipmotion_sh = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion.sh'; 
	}else{
		$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');
	}
	
	/**
	 * On change le statut d'encodage à en_cours pour
	 * - changer les messages sur le site (ce media est en cours d'encodage par exemple)
	 * - indiquer si nécessaire le statut
	 */
	$infos_encodage = array('debut_encodage' => time());
	sql_updateq("spip_spipmotion_attentes",array('encode'=>'en_cours','infos' => serialize($infos_encodage)),"id_spipmotion_attente=".intval($doc_attente));

	/**
	 * Encodage
	 * Cas d'un fichier audio
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_audios_encodage',array()))){
		/**
		 * Encodage du son
		 */
		$encodage = $spipmotion_sh.' --e '.$chemin.' --s '.$fichier_temp.' '.$acodec.' '.$audiobitrate_ffmpeg.' '.$audiofreq.' '.$audiochannels_ffmpeg.' -f --p '.lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg").' --log '.$fichier_log;
		spip_log("$encodage",'spipmotion');
		$lancement_encodage = exec($encodage,$retour,$retour_int);
		spip_log($retour_int,'spipmotion');
		if($retour_int == 0){
			$encodage_ok = true;
		}else if($retour_int >= 126){
			$erreur = _T('spipmotion:erreur_script_spipmotion_non_executable');
			ecrire_fichier($fichier_log,$erreur);
		}
	}

	/**
	 * Encodage
	 * Cas d'un fichier vidéo
	 *
	 * On corrige les paramètres video avant de lancer l'encodage
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
		$format = lire_config("spipmotion/format_$extension_attente");
		
		$width = $source['largeur'];
		$height = $source['hauteur'];
		$width_finale = lire_config("spipmotion/width_$extension_attente",480);
		
		/**
		 * Les ipod/iphones 3Gs et inférieur ne supportent pas de résolutions > à 640x480
		 */
		if($format == 'ipod' && ($width_finale > 640))
			$width_finale = 640;

		/**
		 * On n'agrandit jamais la taille
		 * si la taille demandée est supérieure à la taille originale
		 */
		if($width < $width_finale){
			$width_finale = $width;
			$height_finale = $height;
		}
		else{
			/**
			 * Calcul de la hauteur en fonction de la largeur souhaitée
			 * et de la taille de la video originale
			 */
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

		$fps_conf = (intval(lire_config("spipmotion/fps_$extension_attente","30")) > 0) ? lire_config("spipmotion/fps_$extension_attente","30") : ((intval($source['framerate']) > 0) ? intval($source['framerate']) : 24);
		if(intval($source['framerate']) && (intval($source['framerate']) < $fps_conf)){
			$fps_num = $source['framerate'];
		}else{
			$fps_num = (intval($fps_conf) > 0) ? $fps_conf : $source['framerate'];
		}
		$fps = "--fps $fps_num";

		/**
		 * Définition des bitrates
		 * On vérifie ceux de la source et on compare à ceux souhaités dans la conf
		 * Si la source est inférieure, on utilise ceux de la source en utilisant l'option -sameq
		 * ffmpeg2theora lui a besoin d'une estimation de bitrate
		 */
		if(intval($source['videobitrate']) && (intval($source['videobitrate']) < (lire_config("spipmotion/bitrate_$extension_attente","448"))*1000)){
			if($encodeur == 'ffmpeg2theora'){
				$vbitrate = $source['videobitrate'];
			}else{
				$vbitrate = null;
			}
			if(version_compare($ffmpeg_version,'1.0.0','<')){
				$infos_sup_normal .= ' -sameq ';
			}else{
				$infos_sup_normal .= ' -q:v 0 ';
			}
			$bitrate = "--bitrate ".$source['videobitrate'];
		}else{
			$vbitrate = lire_config("spipmotion/bitrate_$extension_attente","448");
			$bitrate = "--bitrate $vbitrate";
		}

		$texte .= intval($vbitrate) ? "vb=".$vbitrate."000\n" : "";
		$bitrate = intval($vbitrate) ? "--bitrate ".$vbitrate : "";

		/**
		 * Paramètres supplémentaires pour encoder en h264
		 */
		if($vcodec == '--vcodec libx264'){
			$preset_quality = lire_config("spipmotion/vpreset_$extension_attente",'slow');
			$configuration = array();
			if(is_array($spipmotion_compiler['configuration'])){
				$configuration = $spipmotion_compiler['configuration'];
			}
			if(in_array('--enable-pthreads',$configuration)){
				$infos_sup_normal .= " -threads 0 ";
			}
			/**
			 * Encodage pour Ipod/Iphone (<= 3G)
			 */
			if($format == 'ipod'){
				if(version_compare($ffmpeg_version,'0.7.10','<')){
					$infos_sup_normal .= ' -vpre baseline -vpre ipod640';
				}else{
					if(version_compare($ffmpeg_version,'1.1.0','<')){
						$infos_sup_normal .= ' -profile baseline -vpre ipod640';
					}else{
						$infos_sup_normal .= ' -profile:v baseline -vpre ipod640';
					}
						
				}
			}
			/**
			 * Encodage pour PSP
			 * http://rob.opendot.cl/index.php/useful-stuff/psp-video-guide/
			 */
			else if($format == 'psp'){
				$infos_sup_normal .= ' -vpre main';
				$infos_sup_normal .= ' -level 21';
				$infos_sup_normal .= ' -refs 2';
			}
			$infos_sup_normal .= " -aspect $width_finale:$height_finale";
			if($format)
				$infos_sup_normal .= ' -f '.$format;
		}

		$fichier_texte = "$dossier$query.txt";

		ecrire_fichier($fichier_texte,$texte);

		/**
		 * Encodage de la video
		 * Si l'encodeur choisi est ffmpeg2theora et qu'il existe toujours, on l'utilise
		 * sinon on utilise notre script pour ffmpeg
		 */
		$passes = lire_config("spipmotion/passes_$extension_attente",'1');
		$pass_log_file = $dossier.$query.'-pass';
		
		$ffmpeg2theora = @unserialize($GLOBALS['spipmotion_metas']['spipmotion_ffmpeg2theora']);
		if(($encodeur == 'ffmpeg2theora') && ($ffmpeg2theora['version'] > 0)){
			if($passes == 2)
				$deux_passes = '--two-pass';
			$encodage = $spipmotion_sh." --force true $video_size --e $chemin --videoquality ".lire_config('spipmotion/qualite_video_ffmpeg2theora_'.$extension_attente,7)." $fps $bitrate $audiofreq $audiobitrate_ffmpeg2theora $audiochannels_ffmpeg2theora --s $fichier_temp $deux_passes --log $fichier_log --encodeur ffmpeg2theora";
			spip_log($encodage,'spipmotion');
			$lancement_encodage = exec($encodage,$retour,$retour_int);
		}else{
			if(($vcodec == '--vcodec libtheora'))
				$passes = 1;
			if(($passes == "2") && ((($vcodec == '--vcodec libx264') && ($preset_quality != 'hq')) OR ($vcodec == '--vcodec flv') OR ($vcodec == '--vcodec libtheora') OR ($extension_attente == 'webm'))){
				spip_log('Premiere passe','spipmotion');
				if ($ffmpeg_version < '0.7'){
					$preset_1 = $preset_quality ? '-vpre '.$preset_quality.'_firstpass' : '';
				}else{
					$preset_1 = $preset_quality ? '-preset '.$preset_quality : '';
				}
				if($source['rotation'] == '90'){
					$rotation = "-vf transpose=1";
				}
				$infos_sup_normal_1 = "--params_supp \"-an $preset_1 -passlogfile $pass_log_file $infos_sup_normal $rotation\"";
				$encodage_1 = $spipmotion_sh." --force true --pass 1 $video_size --e $chemin $vcodec $fps $bitrate $infos_sup_normal_1 --s $fichier_temp --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." --log $fichier_log";
				spip_log($encodage_1,'spipmotion');
				$lancement_encodage_1 = exec($encodage_1,$retour_1,$retour_int_1);
				/**
				 * La première passe est ok 
				 * On lance la seconde
				 */
				if($retour_int_1 == 0){
					spip_log('Seconde passe','spipmotion');
					if ($ffmpeg_version < '0.7'){
						$infos_sup_normal = $preset_quality ? "-vpre $preset_quality $infos_sup_normal" : $infos_sup_normal;
					}else{
						$infos_sup_normal = $preset_quality ? "-preset $preset_quality $infos_sup_normal" : $infos_sup_normal;
					}
					$metadatas_supp = '';
					$metas_orig = @unserialize($source['metas']);
					
					$infos_sup_normal_2 = '--params_supp \'-passlogfile '.$pass_log_file.' '.$infos_sup_normal.' '.$rotation.' '.$metadatas.'\'';
					$fichier_log = "$fichier_log-pass2.log";
					$encodage = $spipmotion_sh." --force true --pass 2 $audiofreq $audiobitrate_ffmpeg $audiochannels_ffmpeg $video_size --e $chemin $acodec $vcodec $fps $bitrate $infos_sup_normal_2  --fpre $fichier_texte --s $fichier_temp --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." --log $fichier_log";
					spip_log($encodage,'spipmotion');
					$lancement_encodage = exec($encodage,$retour,$retour_int);
				}else{
					$retour_int = 1;
				}
			}else{
				spip_log('on encode en 1 passe','spipmotion');
				if ($ffmpeg_version < '0.7'){
					$infos_sup_normal = $preset_quality ? "-vpre $preset_quality $infos_sup_normal":'';
				}else{
					$infos_sup_normal = $preset_quality ? "-preset $preset_quality $infos_sup_normal":'';
				}
				if($source['rotation'] == '90'){
					$rotation = "-vf transpose=1";
				}
				if($infos_sup_normal){
					$infos_sup_normal = "--params_supp \"$infos_sup_normal\"";
				}
				$encodage = $spipmotion_sh." --force true $audiofreq $video_size --e $chemin $acodec $vcodec $fps $audiobitrate_ffmpeg $audiochannels_ffmpeg $bitrate $infos_sup_normal --s $fichier_temp --fpre $fichier_texte --p ".lire_config("spipmotion/chemin","/usr/local/bin/ffmpeg")." --log $fichier_log";
				spip_log($encodage,'spipmotion');
				$lancement_encodage = exec($encodage,$retour,$retour_int);
			}
		}

		if($retour_int == 0){
			$encodage_ok = true;
		}else if($retour_int >= 126){
			$erreur = _T('spipmotion:erreur_script_spipmotion_non_executable');
			ecrire_fichier($fichier_log,$erreur);
		}
	}

	if($encodage_ok && file_exists(get_spip_doc($source['fichier']))){
		/**
		 * Ajout du nouveau document dans la base de donnée de SPIP
		 * NB : la récupération des infos et du logo est faite automatiquement par
		 * le pipeline post-edition appelé par l'ajout du document
		 */
		$mode = 'document';
		spip_log('Ajout du document en base','spipmotion');
		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$x = $ajouter_documents($fichier_temp, $fichier_final, $type_doc, $id_objet, $mode, '', $actif,'','','');
		spip_log('le nouveau document est le '.$x,'spipmotion');
		if(intval($x) > 1){
			supprimer_fichier($fichier_temp);
			
			/**
			 * Modification de la file d'attente
			 * - On marque le document comme correctement encodé
			 * - On ajoute la date de fin d'encodage
			 */
			
			$infos_encodage['fin_encodage'] = time();
			spip_log('Insertion du temps final d encodage : '.$infos_encodage['fin_encodage'],'spipmotion');
			sql_updateq("spip_spipmotion_attentes",array('encode'=>'oui','infos' => serialize($infos_encodage)),"id_spipmotion_attente=".intval($doc_attente));

			/**
			 * Tentative de récupération d'un logo du document original
			 */
			if((sql_getfetsel('id_vignette','spip_documents','id_document = '.intval($x)) == 0) && ($source['id_vignette'] > 0)){
				$vignette = sql_fetsel('fichier,extension','spip_documents','id_document='.intval($source['id_vignette']));
				$fichier_vignette = get_spip_doc($vignette['fichier']);
				$string_tmp = basename(get_spip_doc($vignette['fichier'])).'-'.mktime();
				$nom_vignette = md5($string_tmp).'.'.$vignette['extension'];
				$x2 = $ajouter_documents($fichier_vignette, $nom_vignette, '', '', 'vignette', $x, $actif,'','','');
			}
			/**
			 * Champs que l'on souhaite réinjecter depuis l'original ni depuis un ancien encodage
			 */
			$champs_recup = array('titre' => '0','descriptif' => '0');
			if(_DIR_PLUGIN_PODCAST)
				$champs_recup['podcast'] = 0;
				$champs_recup['explicit'] = 'non';
			if(_DIR_PLUGIN_LICENCES)
				$champs_recup['id_licence'] = 0;
			if(_DIR_PLUGIN_MEDIAS)
				$champs_recup['credits'] = '';
				
			$modifs = array_intersect_key($source, $champs_recup);
			
			/**
			 * On ajoute l'id dur document original id_orig
			 */
			$modifs['id_orig'] = $attente['id_document'];
			
			include_spip('inc/modifier');
			revision_document($x, $modifs);
			
			$reussite = 'oui';
		}else{
			sql_updateq("spip_spipmotion_attentes",array('encode'=>'non'),"id_spipmotion_attente=".intval($doc_attente));
			spip_log('Il y a une erreur, le fichier n est pas copié','spipmotion');
			$reussite = 'non';
		}
	}else if(!file_exists(get_spip_doc($source['fichier']))){
		spip_log('Le document original a été supprimé entre temps','spipmotion');
		supprimer_fichier($fichier_temp);
		$reussite = 'non';
		sql_delete("spip_spipmotion_attentes","id_spipmotion_attente=".intval($doc_attente));
	}
	/**
	 * Si l'encodage n'est pas ok ...
	 * On donne un statut "erreur" dans la file afin de ne pas la bloquer
	 */
	else{
		$infos_encodage['fin_encodage'] = time();
		$infos_encodage['log'] = spip_file_get_contents($fichier_log);
		$reussite = 'non';
		sql_updateq("spip_spipmotion_attentes",array('encode'=>'erreur','infos' => serialize($infos_encodage)),"id_spipmotion_attente=".intval($doc_attente));
	}

	if(file_exists(_DIR_RACINE.$query.'-0.log')){
		supprimer_fichier(_DIR_RACINE.$query.'-0.log');
	}
	if(file_exists($pass_log_file)){
		supprimer_fichier($pass_log_file);
	}
	if(file_exists($pass_log_file.'.mbtree')){
		supprimer_fichier($pass_log_file.'.mbtree');
	}
	if(file_exists(_DIR_RACINE.$query.'.mbtree')){
		supprimer_fichier(_DIR_RACINE.$query.'.mbtree');
	}
	if(file_exists($fichier_temp)){
		supprimer_fichier($fichier_temp);
	}
	if(file_exists(_DIR_RACINE.$query.'-pass')){
		supprimer_fichier(_DIR_RACINE.$query.'-pass');
	}
	pipeline('post_spipmotion_encodage',
				array(
					'args' => array(
						'id_document' => $x,
						'id_document_orig' => $attente['id_document'],
						'reussite' => $reussite
					),
					'data' => ''
				)
			);
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