<?php 
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2013 - Distribué sous licence GNU/GPL
 * 
 * Récupération des metadonnées d'un fichier via mediainfo : http://mediainfo.sourceforge.net/fr
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des métadonnées via MediaInfo
 * 
 * On passe un chemin de fichier en argument et si ce fichier existe, mediainfo renvoit 
 * les metadatas intéressantes dans un array
 * 
 * @param string $chemin
 * 		Le chemin du fichier à analyser
 * @return array $infos
 * 		Un tableau des informations récupérées
 */
function inc_spipmotion_mediainfo_dist($chemin){
	$infos = array('hasaudio'=>'non','hasvideo' => 'non');
	if(file_exists($chemin)){
		include_spip('inc/filtres');
		include_spip('inc/charset');
		
		/**
		 * On enregistre les metadonnées dans un fichier xml 
		 * qui sera traité par la suite
		 */
		ob_start();
		passthru("mediainfo -f --Output=XML '$chemin' --LogFile=$chemin.xml");
		ob_end_clean();

		if(file_exists($chemin.'.xml')){
			$lire_fichier = lire_fichier($chemin.'.xml',$metadatas);
			include_spip('inc/xml');
			$arbre = spip_xml_parse($metadatas);
			spip_xml_match_nodes(",^track type,",$arbre, $tracks);
			foreach($tracks as $track => $info){
				$metas[$track] = $info;
				if($track == 'track type="General"'){
					$infos['titre'] = $info[0]['Title'][0] ? $info[0]['Title'][0] : ($info[0]['Movie_name'][0] ? $info[0]['Movie_name'][0] : $info[0]['Track_name '][0]);
					$infos['descriptif'] = $info[0]['Description'][0] ? $info[0]['Description'][0] : $info[0]['desc'][0];
					if($infos['descriptif'] == ''){
						if(isset($info[0]['Performer'][0]))
							$infos['descriptif'] .= unicode_to_utf_8($info[0]['Performer'][0])."\n";
						if(isset($info[0]['Album'][0]))
							$infos['descriptif'] .= unicode_to_utf_8($info[0]['Album'][0])."\n";
						if(isset($info[0]['Recorded_date'][0]))
							$infos['descriptif'] .= unicode_to_utf_8($info[0]['Recorded_date'][0])."\n";
						if(isset($info[0]['Genre'][0]))
							$infos['descriptif'] .= unicode_to_utf_8($info[0]['Genre'][0])."\n";
						if(isset($info[0]['Track_name_Position'][0]))
							$infos['descriptif'] .= $info[0]['Track_name_Position'][0].($info[0]['Track_name_Total'][0] ? '/'.$info[0]['Track_name_Total'][0]:'')."\n";
						if(isset($info[0]['Performer_Url'][0]))
							$infos['descriptif'] .= "\n".utf8_encode($info[0]['Performer_Url'][0])."\n";
					}
					$infos['credits'] .= $info[0]['Performer'][0]? unicode_to_utf_8($info[0]['Performer'][0]).($info[0]['Copyright'][0] ? ' - '.$info[0]['Copyright'][0] : '') : $info[0]['Copyright'][0] ;
					$infos['duree'] = $info[0]['Duration'][0] / 1000;
					if(!$infos['duree'])
						$infos['duree'] = isset($info[0]['duration'][0]) ? (($info[0]['duration'][0] > 1000) ? ($info[0]['duration'][0]/1000) :$info[0]['duration'][0]) : '';
					$infos['bitrate'] = $info[0]['Overall_bit_rate'][0];
					$infos['encodeur'] = $info[0]['Writing_library'][0];
					if(!$infos['encodeur'])
						$infos['encodeur'] = $info[0]['Writing_application'][0];
					
					if(isset($info[0]['comapplequicktimelocationISO6709'][0])){
						$coords = preg_match('/((\+|-)\d+\.\d+)((\+|-)\d+\.\d+)((\+|-)\d+\.\d+)\//',$info[0]['comapplequicktimelocationISO6709'][0],$matches);
						if(isset($matches[1]) && isset($matches[3])){
							$infos['lat'] = $matches[1];
							$infos['lon'] = $matches[3];
						}
					}
					/**
					 * Récupération de la cover (pour les fichiers audio)
					 * 
					 * On écrit l'image dans le répertoire local/cache-spipmotion_logo
					 * On ajoute la vignette en base de donnée (c'est un document en mode vignette)
					 * On ajoute id_vignette dans l'array d'info qui sera ensuite utilisé
					 */
					if($info[0]['Cover_Data'][0]){
						$mime = array_shift(explode(' ',$info[0]['Cover_MIME'][0]));
						spip_log($mime,'tickets');
						switch ($mime) {
							case 'image/jpg':
								$ext = 'jpg';
								break;
							case 'image/jpeg':
								$ext = 'jpeg';
								break;
						 	case 'image/png':
								$ext = 'png';
								break;
							case 'image/gif':
								$ext = 'gif';
								break;
							default:
								$ext = 'jpg';
						}
						$tmp_file = 'spipmotion-'.str_replace('.','_',str_replace(':','_',str_replace(' ','_',$infos['titre']))).'.'.$ext;
			            $dest = sous_repertoire(_DIR_VAR, 'cache-spipmotion_logo');
						$dest = $dest.$tmp_file;
						if ($ok = ecrire_fichier($dest, base64_decode($info[0]['Cover_Data'][0]))){
							include_spip('inc/joindre_document');
							$ajouter_documents = charger_fonction('ajouter_documents', 'action');
				
							list($extension,$arg) = fixer_extension_document($dest);
							$cover_ajout = array(array('tmp_name'=>$dest,'name'=> basename($dest)));
							$ajoute = $ajouter_documents('new',$cover_ajout,'',0,'vignette');
				
							if (is_numeric($id_vignette = reset($ajoute)))
							  	$infos['id_vignette'] = $id_vignette;
							spip_unlink($dest);
						}
					}
					/**
					 * On tente de trouver une date correcte?
					 * 
					 * Soit dans :
					 * -* Original_Released_date
					 * -* Recorded_date
					 * -* Encoded_date
					 */
					foreach(array($info[0]['Original_Released_date'][0],$info[0]['Encoded_date'][0]) as $date){
						$date = trim(str_replace('UTC','',$date));
						if(preg_match('#^[0-9]{4}-[0-9]{1,2}- [0-9]{1}$#',$date)){
							$date = preg_replace("#\.|/| #i",'0',$date,1);
						}
						$date = preg_replace("#\.|/| #i",'-',$date);
						if(preg_match('#^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$#',$date)){
							list($annee,$mois,$jour) = explode('-',$date);
							if (checkdate($mois, $jour, $annee)){
								// normaliser
								if ($date = recup_date($date)){
									if ($date = mktime($date[3], $date[4], 0, (int)$date[1], (int)$date[2], (int)$date[0])) {
										$date = date("Y-m-d H:i:s", $date);
										$date = vider_date($date); // enlever les valeurs considerees comme nulles (1 1 1970, etc...)
										if ($date) {
											$infos['date'] = $date;
											break;
										}
									}
								}
							}
						}
					}
					
					/**
					 * Si on a du contenu dans les messages de copyright, 
					 * on essaie de trouver la licence, si on a le plugin Licence
					 * 
					 * Pour l'instant uniquement valable sur les CC
					 */
					if(defined('_DIR_PLUGIN_LICENCE') && ((strlen($infos['descriptif']) > 0) OR strlen($infos['credits']) > 0)){
						include_spip('licence_fonctions');
						if(function_exists('licence_recuperer_texte')){
							foreach(array($infos['descriptif'],$infos['credits']) as $contenu){
								$infos['id_licence'] = licence_recuperer_texte($contenu);
								if(intval($infos['id_licence']))
									break;
							}
						}
					}
				}
				/**
				 * Les infos techniques vidéo
				 */
				if($track == 'track type="Video"'){
					if(!$infos['titre'])
						$infos['titre'] = $info[0]['Title'][0] ? $info[0]['Title'][0] : '';
					$infos['videobitrate'] = $info[0]['Bit_rate'][0] ? $info[0]['Bit_rate'][0] : ($info[0]['Nominal_bit_rate'][0] ? $info[0]['Nominal_bit_rate'][0] : '');
					$infos['hauteur'] = $info[0]['Original_height'][0] ? $info[0]['Original_height'][0] : $info[0]['Height'][0];
					$infos['largeur'] = $info[0]['Original_width'][0] ? $info[0]['Original_width'][0] : $info[0]['Width'][0];
					$infos['videocodec'] = $info[0]['Format'][0];
					$infos['videocodecid'] = $info[0]['Codec_ID'][0] ? $info[0]['Codec_ID'][0] : strtolower($info[0]['Format'][0]);
					if($infos['videocodecid'] == 'avc1'){
						if(isset($info[0]['Format_profile'][0])){
							if(preg_match('/^Baseline.*/',$info[0]['Format_profile'][0]))
								$infos['videocodecid'] = 'avc1.42E01E';
							if(preg_match('/^Main.*/',$info[0]['Format_profile'][0]))
								$infos['videocodecid'] = 'avc1.4D401E';
							if(preg_match('/^High.*/',$info[0]['Format_profile'][0]))
								$infos['videocodecid'] = 'avc1.64001E';
						}
					}else if($infos['videocodec'] == 'Sorenson Spark'){
						$infos['videocodecid'] = 'h263';
					}
					$infos['aspect_ratio'] = $info[0]['Display_aspect_ratio'][0] ? $info[0]['Display_aspect_ratio'][0] : '';
					
					/**
					 * On a un display aspect ratio
					 * 
					 * Si le ration de width / height est vraiment trop différent de celui qui est dans les metas,
					 * c'est un truc étrange de la vidéo
					 * 
					 * On doit donc recréer une largeur en fonction de la hauteur qui soit valable, sinon les encodages auront des problèmes de ratio,
					 * notamment lors de la lecture en flash
					 */
					if(($infos['aspect_ratio'] != '') && (($infos['aspect_ratio'] - ($infos['largeur']/$infos['hauteur'])) > '0.1')){
						$infos['largeur'] = round($infos['hauteur']*$infos['aspect_ratio']);
					}
					$infos['framerate'] = $info[0]['Frame_rate'][0];
					$infos['framecount'] = $info[0]['Frame_count'][0];
					$infos['rotation'] = intval($info[0]['Rotation'][0]);
					$infos['hasvideo'] = 'oui';
				}
				/**
				 * Les infos techniques audio
				 */
				if($track == 'track type="Audio"'){
					$infos['audiobitrate'] = $info[0]['Bit_rate'][0];
					$infos['audiochannels'] = $info[0]['Channel_s_'][0];
					$infos['audiochannels'] = $info[0]['Channel_s_'][0];
					$infos['audiosamplerate'] = $info[0]['Sampling_rate'][0];
					$infos['audiocodec'] = $info[0]['Codec'][0];
					$infos['audiobitratemode'] = strtolower($info[0]['Bit_rate_mode'][0]);
					if($infos['audiocodec'] == 'AAC LC')
						$infos['audiocodecid'] = 'mp4a.40.2';
					else if($infos['audiocodec'] == 'MPA1L3')
						$infos['audiocodecid'] = 'mp3a';
					else
						$infos['audiocodecid'] = $info[0]['Codec_ID'][0] ? $info[0]['Codec_ID'][0] : strtolower($info[0]['Codec'][0]);
					if($infos['audiobitrate'] && $infos['audiochannels'] && $infos['audiocodec'] && $infos['audiobitratemode'])
						$infos['hasaudio'] = 'oui';
				}
			}
			/**
			 * Suppression du fichier temporaire ayant l'ensemble des metadonnées
			 */
			spip_unlink($chemin.'.xml');
		}
		/**
		 * On met l'ensemble des metas dans le champs metadatas
		 */
		$infos['metadatas'] = serialize(array('Retrieved infos in database' => $infos));
	}
	else
		spip_log('fichier_non_existant');
	
	return $infos;
}
?>