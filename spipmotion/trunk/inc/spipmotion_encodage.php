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

function inc_spipmotion_encodage_dist($id_document,$options = array()){
	if(!is_array($GLOBALS['spipmotion_metas'])){
		$inc_meta = charger_fonction('meta', 'inc');
		$inc_meta('spipmotion_metas');
	}
	/**
	 * On vérifie s'il y a des ffmpeg en cours sur le serveur,
	 * s'il y en a 3 ou plus, on attend
	 */
	$ps_ffmpeg = exec('ps -C ffmpeg',$retour,$retour_int);
	if(($retour_int == 1) && (count($retour) >= 3)){
		spip_log('Il y a a apparemment trop de processus de ffmpeg en cours, on attend donc','spipmotion');
		$ret['success'] = true;
		$ret['statut'] = 'non';
		return $ret;
	}

	$format = $options['format'];
	$source = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
	/**
	 * On vérifie que l'on n'a pas déjà une version dans le format souhaité
	 * Si oui on la supprime avant de la réencoder
	 */
	 if($id_document = sql_getfetsel('document.id_document',
									'spip_documents as document LEFT JOIN spip_documents_liens as lien ON document.id_document=lien.id_document',
									'lien.id_objet='.intval($source['id_document']).' AND lien.objet='.sql_quote("document").' AND document.extension='.sql_quote($format).' AND document.mode='.sql_quote("conversion"))){
		spip_log("Il faut supprimer $id_document",'spipmotion');
		$v = sql_fetsel("id_document,id_vignette,fichier","spip_documents","id_document=".intval($id_document));

		include_spip('inc/documents');
		/**
		 * On ajoute l'id_document dans la liste des documents
		 * à supprimer de la base
		 * On supprime le fichier correspondant
		 */
		$liste[] = $v['id_document'];
		if (($nb = sql_countsel('spip_documents','fichier='.sql_quote($v['fichier'])) == '1') && @file_exists($f = get_spip_doc($v['fichier']))) {
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
			if (($nb = sql_countsel('spip_documents','fichier='.sql_quote($fichier)) == '1') && @file_exists($f = get_spip_doc($fichier))) {
				supprimer_fichier($f);
			}
		}

		if(is_array($liste)){
			$in = sql_in('id_document', $liste);
			sql_delete("spip_documents", $in);
			sql_delete("spip_documents_liens", $in);
			sql_delete("spip_facd_conversions", "id_document=".intval($id_document).' AND statut != '.sql_quote('oui').' AND extension='.sql_quote($format).' AND id_facd_conversion!='.intval($options['id_facd_conversion']));
		}
	}
	/**
	 * Puis on lance l'encodage
	 */
	return encodage($source,$options);
}

/**
 * Fonction de lancement de l'encodage
 *
 * @param array $source Les informations du fichier source
 * @param int $doc_attente id_facd_conversion L'id de la file d'attente
 */
function encodage($source,$options){
	include_spip('plugins/installer');
	$ret = array();
	spip_log('On encode le document : '.$source['id_document'],'spipmotion');
	/**
	 * Si le chemin vers le binaire FFMpeg n'existe pas,
	 * la configuration du plugin crée une meta spipmotion_casse
	 */
	if($GLOBALS['meta']['spipmotion_casse'] == 'oui'){
		$ret['success'] = false;
		$ret['erreur'] = 'spipmotion_casse';
		return false;
	}

	include_spip('inc/config');
	$spipmotion_compiler = @unserialize($GLOBALS['spipmotion_metas']['spipmotion_compiler']);
	$ffmpeg_version = $spipmotion_compiler['ffmpeg_version'] ? $spipmotion_compiler['ffmpeg_version'] : '0.7';
	$rep_dest = sous_repertoire(_DIR_VAR, 'cache-spipmotion');

	$extension_attente = $options['format'];

	$encodeur = lire_config("spipmotion/encodeur_$extension_attente",'');

	$ffmpeg2theora = @unserialize($GLOBALS['spipmotion_metas']['spipmotion_ffmpeg2theora']);

	if(
		($source['rotation'] == '90')
		 OR ($encodeur == 'ffmpeg2theora' && !$ffmpeg2theora['version'])){
		$encodeur = 'ffmpeg';
	}

	include_spip('inc/documents');
	$chemin = get_spip_doc($source['fichier']);
	$fichier = basename($source['fichier']);
	spip_log("encodage de $chemin","spipmotion");

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
	 * 
	 * $infos_sup_normal correspond aux paramètres supplémentaires envoyés à la commande, 
	 * spécifique en fonction de plusieurs choses :
	 * - rotation;
	 */
	$texte = $infos_sup_normal = '';

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
		$codec_audio = lire_config("spipmotion/acodec_$extension_attente");
		if($extension_attente == "mp3")
			$codec_audio = "libmp3lame";
		else if(in_array($extension_attente,array('ogg','oga')))
			$codec_audio = "libvorbis";
		else if(!$codec_audio){
			if($extension_attente == 'ogv')
				$codec_audio = "libvorbis";
		}
		$acodec = $codec_audio ? "--acodec ".$codec_audio :'';

		/**
		 * Forcer libvorbis si on utilise ffmpeg
		 */
		if(($encodeur == "ffmpeg") && ($acodec == "--acodec vorbis"))
			$acodec = '--acodec libvorbis';

		if(in_array($codec_audio,array('vorbis','libvorbis'))){
			$qualite = lire_config("spipmotion/qualite_audio_$extension_attente",'4');
			$audiobitrate_ffmpeg2theora = $audiobitrate_ffmpeg = "--audioquality $qualite";
		}else{
			/**
			 * S'assurer que le bitrate choisi fonctionne
			 */
			if(intval($source['audiobitrate']) && (intval($source['audiobitrate']) < (lire_config("spipmotion/bitrate_audio_$extension_attente","64")*1000))){
				$audiobitrates = array('32000','64000','96000','128000','192000','256000');
				if(!in_array($source['audiobitrate'],$audiobitrates)){
					$abitrate = min($audiobitrates);
					foreach($audiobitrates as $bitrate){
						if($source['audiobitrate'] >= $bitrate){
							$abitrate = $bitrate;
							break;
						}
					}
				}else
					$abitrate = $source['audiobitrate'];

				$abitrate = floor($abitrate/1000);
			}else
				$abitrate = lire_config("spipmotion/bitrate_audio_$extension_attente","64");
			$texte .= "ab=".$abitrate."000\n";
			$audiobitrate_ffmpeg = $audiobitrate_ffmpeg2theora = "--audiobitrate ".$abitrate;
		}

		/**
		 * Vérification des samplerates
		 */
		if(intval($source['audiosamplerate']) && (intval($source['audiosamplerate']) < lire_config("spipmotion/frequence_audio_$extension_attente","22050"))){
			/**
			 * libmp3lame ne gère pas tous les samplerates
			 * ni libfaac
			 */
			if($acodec == '--acodec libmp3lame')
				$audiosamplerates = array('11025','22050','44100');
			else if($acodec == '--acodec libfaac')
				$audiosamplerates = array('22050','24000','32000','44100','48000');
			else
				$audiosamplerates = array('4000','8000','11025','16000','22050','24000','32000','44100','48000');
			/**
			 * ffmpeg ne peut resampler
			 * On force le codec audio à aac s'il était à libmp3lame et que le nombre de canaux était > 2
			 */
			if(($source['audiochannels'] > 2) && ($encodeur != 'ffmpeg2theora')){
				$samplerate = $source['audiosamplerate'];
				if($acodec == '--acodec libmp3lame'){
					$acodec = '--acodec libfaac';
					$audiobitrate_ffmpeg = $audiobitrate_ffmpeg2theora = "--audiobitrate 128";
				}
			}else if(!in_array($source['audiosamplerate'],$audiosamplerates)){
				$samplerate = min($audiosamplerates);
				foreach($audiosamplerates as $audiosamplerate){
					if($source['audiosamplerate'] >= $audiosamplerate){
						$samplerate = $audiosamplerate;
						break;
					}
				}
			}else
				$samplerate = $source['audiosamplerate'];
		}else{
			if(($source['audiochannels'] > 2) && ($encodeur != 'ffmpeg2theora')){
				$samplerate = $source['audiosamplerate'];
				if($acodec == '--acodec libmp3lame'){
					$acodec = '--acodec libfaac';
					$audiobitrate_ffmpeg = $audiobitrate_ffmpeg2theora = "--audiobitrate 128";
				}
			}else
				$samplerate = lire_config("spipmotion/frequence_audio_$extension_attente","22050");
		}
		if($samplerate){
			$audiofreq = "--audiofreq ".$samplerate;
			$texte .= "ar=$samplerate\n";
		}
		/**
		 * On passe en stereo ce qui a plus de 2 canaux et ce qui a un canal et dont
		 * le format choisi est vorbis (l'encodeur vorbis de ffmpeg ne gère pas le mono)
		 */
		if(in_array($extension_attente,array('ogg','ogv','oga')) && ($source['audiochannels'] < 2)
			&& ($encodeur != 'ffmpeg2theora')){
			$audiochannels = 2;
		}else
			$audiochannels = $source['audiochannels'];

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
		$ss_audio = '';
	}else
		$ss_audio = '-an';

	if($GLOBALS['spipmotion_metas']['spipmotion_safe_mode'] == 'oui')
		$spipmotion_sh = $GLOBALS['spipmotion_metas']['spipmotion_safe_mode_exec_dir'].'/spipmotion.sh'; 
	else
		$spipmotion_sh = find_in_path('script_bash/spipmotion.sh');

	/**
	 * Encodage
	 * Cas d'un fichier audio
	 */
	if(in_array($source['extension'],lire_config('spipmotion/fichiers_audios_encodage',array()))){
		/**
		 * Encodage du son
		 */
		$encodage = $spipmotion_sh.' --e '.$chemin.' --s '.$fichier_temp.' '.$acodec.' '.$audiobitrate_ffmpeg.' '.$audiofreq.' '.$audiochannels_ffmpeg.' --log '.$fichier_log;
		spip_log("$encodage",'spipmotion');
		$lancement_encodage = exec($encodage,$retour,$retour_int);
		if($retour_int == 0){
			$ret['success'] = true;
		}else if($retour_int >= 126){
			$ret['success'] = false;
			$ret['erreur'] = _T('spipmotion:erreur_script_spipmotion_non_executable');
			ecrire_fichier($fichier_log,$ret['erreur']);
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
		if($source['rotation'] == '90'){
			$width = $source['hauteur'];
			$height = $source['largeur'];
		}else{
			$width = $source['largeur'];
			$height = $source['hauteur'];
		}
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
		/**
		 * Calcul de la hauteur en fonction de la largeur souhaitée
		 * et de la taille de la video originale
		 */
		else
			$height_finale = intval(round($height/($width/$width_finale)));

		/**
		 * Pour certains codecs (libx264 notemment), width et height doivent être
		 * divisibles par 2
		 * On le fait pour tous les cas pour éviter toute erreur
		 */
		if(!is_int($width_finale/2))
			$width_finale = $width_finale +1;
		if(!is_int($height_finale/2))
			$height_finale = $height_finale +1;

		$video_size = "--size ".$width_finale."x".$height_finale;

		/**
		 * Définition du framerate d'encodage
		 * - Si le framerate de la source est supérieur à celui de la configuration souhaité, on prend celui de la configuration
		 * - Sinon on garde le même que la source
		 */
		$texte .= lire_config("spipmotion/vcodec_$extension_attente") ? "vcodec=".lire_config("spipmotion/vcodec_$extension_attente")."\n":'';
		$vcodec .= lire_config("spipmotion/vcodec_$extension_attente") ? "--vcodec ".lire_config("spipmotion/vcodec_$extension_attente") :'';

		$fps_conf = (intval(lire_config("spipmotion/fps_$extension_attente","30")) > 0) ? lire_config("spipmotion/fps_$extension_attente","30") : ((intval($source['framerate']) > 0) ? intval($source['framerate']) : 24);
		if(intval($source['framerate']) && (intval($source['framerate']) < $fps_conf))
			$fps_num = $source['framerate'];
		else
			$fps_num = (intval($fps_conf) > 0) ? $fps_conf : $source['framerate'];

		$fps = "--fps $fps_num";

		/**
		 * Définition des bitrates
		 * On vérifie ceux de la source et on compare à ceux souhaités dans la conf
		 * Si la source est inférieure, on utilise ceux de la source en utilisant l'option -qscale 0
		 * ffmpeg2theora lui a besoin d'une estimation de bitrate
		 */
		if(intval($source['videobitrate']) && (intval($source['videobitrate']) < (lire_config("spipmotion/bitrate_$extension_attente","600"))*1000)){
			if(($encodeur == 'ffmpeg2theora') OR ($vcodec == '--vcodec libtheora'))
				$vbitrate = $source['videobitrate'];
			else{
				$vbitrate = null;
				if(spip_version_compare($ffmpeg_version,'1.0.0','<'))
					$infos_sup_normal .= ' -sameq ';
				else
					$infos_sup_normal .= ' -q:v 0 ';
			}
			$bitrate = "--bitrate ".$source['videobitrate'];
		}else{
			$vbitrate = lire_config("spipmotion/bitrate_$extension_attente","600");
			$bitrate = "--bitrate $vbitrate";
		}

		$texte .= intval($vbitrate) ? "vb=".$vbitrate."000\n" : '';
		$bitrate = intval($vbitrate) ? "--bitrate ".$vbitrate : '';

		$configuration = array();
		if(is_array($spipmotion_compiler['configuration']))
			$configuration = $spipmotion_compiler['configuration'];

		/**
		 * Paramètres supplémentaires pour encoder en h264
		 */
		if($vcodec == '--vcodec libx264'){
			$preset_quality = lire_config("spipmotion/vpreset_$extension_attente",'slow');
			if(in_array('--enable-pthreads',$configuration))
				$infos_sup_normal .= " -threads 0 ";

			/**
			 * Encodage pour Ipod/Iphone (<= 3G)
			 */
			if($format == 'ipod'){
				if(spip_version_compare($ffmpeg_version,'0.7.20','<'))
					$infos_sup_normal .= ' -vpre baseline -vpre ipod640 -bf 0';
				else
					$infos_sup_normal .= ' -profile:v baseline -vpre ipod640 -bf 0';	
			}
			/**
			 * Encodage pour PSP
			 * http://rob.opendot.cl/index.php/useful-stuff/psp-video-guide/
			 */
			else if($format == 'psp')
				$infos_sup_normal .= ' -vpre main -level 21 -refs 2';
		}
		if(($vcodec == "--vcodec libtheora") && ($encodeur != 'ffmpeg2theora')){
			if(in_array('--enable-pthreads',$configuration))
				$infos_sup_normal .= " -threads 0 ";
		}

		if($source['rotation'] != 90){
			$aspect = $source['aspect_ratio'] ? $source['aspect_ratio']: "$width_finale:$height_finale";
			$infos_sup_normal .= " -aspect $aspect";
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

		if(($encodeur == 'ffmpeg2theora') && ($ffmpeg2theora['version'] > 0)){
			if($passes == 2) $deux_passes = '--two-pass';
			$encodage = $spipmotion_sh." --force true $video_size --e $chemin --videoquality ".lire_config('spipmotion/qualite_video_ffmpeg2theora_'.$extension_attente,7)." $fps $bitrate $audiofreq $audiobitrate_ffmpeg2theora $audiochannels_ffmpeg2theora --s $fichier_temp $deux_passes --log $fichier_log --encodeur ffmpeg2theora";
			spip_log($encodage,'spipmotion');
			$lancement_encodage = exec($encodage,$retour,$retour_int);
		}else{
			if(($passes == "2") && ((($vcodec == '--vcodec libx264') && ($preset_quality != 'hq')) OR ($vcodec == '--vcodec flv') OR ($vcodec == '--vcodec libtheora') OR ($extension_attente == 'webm'))){
				spip_log('Premiere passe','spipmotion');
				if (spip_version_compare($ffmpeg_version,'1.0.0','<')){
					$preset_1 = $preset_quality ? ' -vpre '.$preset_quality.'_firstpass' : '';
				}else
					$preset_1 = $preset_quality ? ' -preset '.$preset_quality : '';

				if($source['rotation'] == '90'){
					$metadatas = '';
					if (spip_version_compare($ffmpeg_version,'1.0.0','<')){
						$rotation = "-vf transpose=1";
					}else{
						$metadatas = "-metadata:s:v:0 rotate=0";
						$rotation = "-filter:v transpose=1";
					}
					$infos_sup_normal .= "$rotation $metadatas";
				}

				/**
				 * Même si dans tous les tutos il est spécifié de mettre -an pour ne pas utiliser l'audio dans la première passe
				 * Il s'avère que dans certains cas (source désynchronisée), l'encodage plante
				 * Du coup on utilise exactement les mêmes réglages dans les 2 passes
				 */
				$infos_sup_normal_1 = "--params_supp \"$preset_1 -passlogfile $pass_log_file $infos_sup_normal\"";
				$encodage_1 = $spipmotion_sh." --force true --pass 1 $audiofreq $audiobitrate_ffmpeg $audiochannels_ffmpeg $video_size --e $chemin $vcodec $fps $bitrate $infos_sup_normal_1 --s $fichier_temp --log $fichier_log";
				spip_log($encodage_1,'spipmotion');
				$lancement_encodage_1 = exec($encodage_1,$retour_1,$retour_int_1);
				/**
				 * La première passe est ok 
				 * On lance la seconde
				 */
				if($retour_int_1 == 0){
					spip_log('Seconde passe','spipmotion');

					if (spip_version_compare($ffmpeg_version,'0.7.20','<'))
						$preset_2 = $preset_quality ? " -vpre $preset_quality":'';
					else
						$preset_2 = $preset_quality ? " -preset $preset_quality":'';

					$infos_sup_normal_2 = "--params_supp \"-passlogfile $pass_log_file $ss_audio $preset_2 $infos_sup_normal $metadatas\"";
					$encodage = $spipmotion_sh." --force true --pass 2 $audiofreq $audiobitrate_ffmpeg $audiochannels_ffmpeg $video_size --e $chemin $acodec $vcodec $fps $bitrate $infos_sup_normal_2  --fpre $fichier_texte --s $fichier_temp --log $fichier_log";
					spip_log($encodage,'spipmotion');
					$lancement_encodage = exec($encodage,$retour,$retour_int);
				}else{
					spip_log('SPIPMOTION Erreur : Le retour de l encodage est revenu en erreur','spipmotion'._LOG_CRITICAL);
					$retour_int = 1;
				}
			}else{
				$metadatas = $metadatas_supp = "";
				$infos_sup_normal .= " $ss_audio ";
				if (spip_version_compare($ffmpeg_version,'0.7.0','<'))
					$infos_sup_normal .= $preset_quality ? " -vpre $preset_quality":'';
				else
					$infos_sup_normal .= $preset_quality ? " -preset $preset_quality":'';

				if($source['rotation'] == '90'){
					$metadatas = "";
					if (spip_version_compare($ffmpeg_version,'1.0.0','<')){
						$rotation = "-vf transpose=1";
					}else{
						$metadatas = "-metadata:s:v:0 rotate=0";
						$rotation = "-filter:v transpose=1";
					}
					$infos_sup_normal .= " $rotation $metadatas";
				}

				if(strlen($infos_sup_normal) > 1)
					$infos_sup_normal = "--params_supp \"$infos_sup_normal\"";
				$encodage = $spipmotion_sh." --force true $audiofreq $video_size --e $chemin $acodec $vcodec $fps $audiobitrate_ffmpeg $audiochannels_ffmpeg $bitrate $infos_sup_normal --s $fichier_temp --fpre $fichier_texte --log $fichier_log";
				spip_log($encodage,'spipmotion');
				$lancement_encodage = exec($encodage,$retour,$retour_int);
			}
		}

		if($retour_int == 0){
			$ret['success'] = true;
		}else if($retour_int >= 126){
			$ret['success'] = false;
			$ret['erreur'] = _T('spipmotion:erreur_script_spipmotion_non_executable');
			ecrire_fichier($fichier_log,$ret['erreur']);
		}
	}

	if($ret['success'] && file_exists(get_spip_doc($source['fichier']))){
		if(!sql_getfetsel('id_document','spip_documents','id_document='.intval($source['id_document']))){
			spip_connect_db('mysql-master','','mediaspip','zjX5uPfP','mu_filmscanece5','mysql', 'spip','');
		}
		/**
		 * Ajout du nouveau document dans la base de donnée de SPIP
		 * NB : la récupération des infos et du logo est faite automatiquement par
		 * le pipeline post-edition appelé par l'ajout du document
		 */
		$mode = 'conversion';
		spip_log('Ajout du document en base','spipmotion');
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		$doc = array(array('tmp_name'=>$fichier_temp,'name'=>$fichier_final,'mode'=>$mode));

		/**
		 * Tentative de récupération d'un logo du document original
		 * si pas déjà de vignette
		 */
		if($source['id_vignette'] > 0){
			$vignette = sql_fetsel('fichier,extension','spip_documents','id_document='.intval($source['id_vignette']));
			$fichier_vignette = get_spip_doc($vignette['fichier']);
			$vignette = array(array('tmp_name'=>$fichier_vignette,'name'=>$fichier_vignette));
			$x2 = $ajouter_documents('new', $vignette, '', 0, 'vignette');
			$id_vignette = reset($x2);
			if (is_numeric($id_vignette))
			  	$source['id_vignette'] = $id_vignette;
		}else
			$source['id_vignette'] = $id_vignette;

		/**
		 * Champs que l'on souhaite réinjecter depuis l'original ni depuis un ancien encodage
		 */
		$champs_recup = array('titre' => '','descriptif' => '');
		if(defined('_DIR_PLUGIN_PODCAST')){
			$champs_recup['podcast'] = 0;
			$champs_recup['explicit'] = 'non';
		}if(defined('_DIR_PLUGIN_LICENCES'))
			$champs_recup['id_licence'] = 0;
		$champs_recup['credits'] = '';
		$champs_recup['id_vignette'] = '';

		$modifs = array_intersect_key($source, $champs_recup);
		foreach($modifs as $champs=>$val){
			set_request($champs,$val);
		}

		$x = $ajouter_documents('new',$doc, 'document', $source['id_document'], $mode);
		$x = reset($x);
		if(intval($x) > 1){
			supprimer_fichier($fichier_temp);
			$ret['id_document'] = $x;
			$ret['success'] = true;
		}else{
			spip_log('Il y a une erreur, le fichier n est pas copié','spipmotion');
			$ret['erreur'] = 'Il y a une erreur, le fichier n est pas copié';
			$ret['success'] = false;
		}
	}else if(!file_exists(get_spip_doc($source['fichier']))){
		spip_log('Le document original a été supprimé entre temps','spipmotion');
		supprimer_fichier($fichier_temp);
		$ret['erreur'] = 'Le document original a été supprimé entre temps';
		$ret['success'] = false;
	}
	/**
	 * Si l'encodage n'est pas ok ...
	 * On donne un statut "erreur" dans la file afin de ne pas la bloquer
	 */
	else{
		$infos_encodage['fin_encodage'] = time();
		$infos_encodage['log'] = spip_file_get_contents($fichier_log);
		$ret['infos'] = $infos_encodage;
		$ret['erreur'] = 'Encodage en erreur';
		$ret['success'] = false;
	}

	/**
	 * On supprime les différents fichiers temporaires qui auraient pu être créés
	 * si on a une réussite
	 */
	if($ret['success']){
		$files = array(
					$fichier_temp,
					$fichier_texte,
					$pass_log_file.'-0.log',
					$pass_log_file.'.mbtree',
					$pass_log_file.'-0.log.mbtree',
					_DIR_RACINE.$query.'.mbtree',
					_DIR_RACINE.$query.'-pass'
				);
		foreach($files as $file){
			if(file_exists($file)) supprimer_fichier($file);
		}
	}
	pipeline('post_spipmotion_encodage',
				array(
					'args' => array(
						'id_document' => $x,
						'id_document_orig' => $source['id_document'],
						'reussite' => $reussite
					),
					'data' => ''
				)
			);

	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('spipmotion_encodage', intval($options['id_facd_conversion']),
			array(
				'id_document' => $x,
				'source' => $source,
				'fichier_log' => $fichier_log,
			)
		);
	}
	return $ret;
}
?>