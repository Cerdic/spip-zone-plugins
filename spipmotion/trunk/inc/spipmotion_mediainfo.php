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

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Récupération des métadonnées via MediaInfo
 * 
 * @param string $chemin : le chemin du fichier à analyser
 * @return array $infos : un tableau des informations récupérées
 */
function inc_spipmotion_mediainfo_dist($chemin){
	$infos = array();
	if(file_exists($chemin)){
		ob_start();
		passthru("mediainfo -f --Output=XML $chemin");
		$metadatas=ob_get_contents();
		ob_end_clean();
		include_spip('inc/xml');
		$arbre = spip_xml_parse($metadatas);
		spip_xml_match_nodes(",^track type,",$arbre, $tracks);
		foreach($tracks as $track => $info){
			$metas[$track] = $info;
			if($track == 'track type="General"'){
				$infos['titre'] = $info[0]['Title'][0] ? $info[0]['Title'][0] : ($info[0]['Movie_name'][0] ? $info[0]['Movie_name'][0] : $info[0]['Track_name '][0]);
				$infos['descriptif'] = $info[0]['Description'][0] ? $info[0]['Description'][0] : $info[0]['desc'][0];
				$infos['credits'] .= $info[0]['Performer'][0]? $info[0]['Performer'][0].($info[0]['Copyright'][0] ? ' - '.$info[0]['Copyright'][0] : '') : $info[0]['Copyright'][0] ;
				$infos['duree'] = $info[0]['Duration'][0] / 1000;
				$infos['bitrate'] = $info[0]['Overall_bit_rate'][0];
				/**
				 * Récupération de la cover
				 */
				if($info[0]['Cover_Data'][0]){
					$mime = array_shift(explode(' ',$info[0]['Cover_MIME'][0]));
					spip_log($mime,'test');
					switch ($mime) {
						case 'image/jpg':
							$ext = 'jpg';
					 	case 'image/png':
							$ext = 'png';
						case 'image/gif':
							$ext = 'gif';
						default:
							$ext = 'jpg';
					}
					$tmp_file = 'spipmotion-'.str_replace(' ','_',$infos['titre']).'.'.$ext;
		            $dest = sous_repertoire(_DIR_VAR, 'cache-spipmotion_logo');
					$dest = $dest.$tmp_file;
					if ($ok = ecrire_fichier($dest, base64_decode(array_shift(explode(' / ',$info[0]['Cover_Data'][0]))))){
						include_spip('inc/joindre_document');
						$ajouter_documents = charger_fonction('ajouter_documents', 'action');
			
						list($extension,$arg) = fixer_extension_document($dest);
						$cover_ajout = array(array('tmp_name'=>$dest,'name'=> basename($dest)));
						$ajoute = $ajouter_documents('new',$cover_ajout,'',0,'vignette');
			
						if (is_numeric(reset($ajoute))
						  AND $id_vignette = reset($ajoute)){
						  	$infos['id_vignette'] = $id_vignette;
						}
					}
					
					if((isset($id3['date']) OR isset($id3['original_release_time']) OR isset($id3['encoded_time']))){
							if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$id3['date']))
								$valeurs['date'] = $id3['date'];
							else if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$id3['original_release_time']))
								$valeurs['date'] = $id3['original_release_time'];
							else if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$id3['encoded_time']))
								$valeurs['date'] = $id3['encoded_time'];
							
							if(isset($valeurs['date']) && (strlen($valeurs['date'])=='10'))
								$valeurs['date'] = $valeurs['date'].' 00:00:00';
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
					spip_log($infos,'test');
				}
			}
			if($track == 'track type="Video"'){
				if(!$infos['titre'])
					$infos['titre'] = $info[0]['Title'][0] ? $info[0]['Title'][0] : '';
				$infos['videobitrate'] = $info[0]['Bit_rate'][0] ? $info[0]['Bit_rate'][0] : ($info[0]['Nominal_bit_rate'][0] ? $info[0]['Nominal_bit_rate'][0] : '');
				$infos['hauteur'] = $info[0]['Height'][0];
				$infos['largeur'] = $info[0]['Width'][0];
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
				$infos['framerate'] = $info[0]['Frame_rate'][0];
				$infos['framecount'] = $info[0]['Frame_count'][0];
				$infos['rotation'] = intval($info[0]['Rotation'][0]);
				$infos['hasvideo'] = 'oui';
			}
			if($track == 'track type="Audio"'){
				$infos['hasaudio'] = 'oui';
				$infos['audiobitrate'] = $info[0]['Bit_rate'][0];
				$infos['audiochannels'] = $info[0]['Channel_s_'][0];
				$infos['canaux'] = $info[0]['Channel_s_'][0];
				$infos['audiosamplerate'] = $info[0]['Sampling_rate'][0];
				$infos['audiocodec'] = $info[0]['Codec'][0];
				if($infos['audiocodec'] == 'AAC LC'){
					$infos['audiocodecid'] = 'mp4a.40.2';
				}else if($infos['audiocodec'] == 'MPA1L3'){
					$infos['audiocodecid'] = 'mp3a';
				}else{
					$infos['audiocodecid'] = $info[0]['Codec_ID'][0] ? $info[0]['Codec_ID'][0] : strtolower($info[0]['Codec'][0]);
				}
			}
		}
	}
	if(!$infos['hasaudio']){
		$infos['hasaudio'] = 'non';
	}
	if(!$infos['hasvideo']){
		$infos['hasvideo'] = 'non';
	}
	spip_log($infos,'spipmotion');
	$infos['metadatas'] = serialize($metas);
	return $infos;
}
?>