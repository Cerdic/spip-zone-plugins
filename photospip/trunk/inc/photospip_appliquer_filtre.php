<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction d'application de filtre sur une image
 * 
 * @param string $src Le chemin de l'image source
 * @param string $dest Le chemin de l'image de destination
 * @param string $filtre Le filtre à appliquer
 * @param array $params Les paramètres à passer au filtre
 * @return boolean|array False si quelquechose ne va pas, un array des infos de 
 * l'image de destination si tout est ok 
 */
function inc_photospip_appliquer_filtre_dist($src, $dest, $filtre,$params){
	spip_log("src = $src","photospip");
	spip_log("dest = $dest","photospip");
	spip_log("filtre = $filtre","photospip");
	spip_log("params","photospip");
	spip_log($params,"photospip");
	
	include_spip('inc/filtres');
	include_spip('public/parametrer');
	$src_img = '';
	
	$filtre = chercher_filtre($filtre);
	spip_log($filtre,'photospip');
	if (function_exists($filtre)){
		if(is_array($params)){
			if($filtre == 'image_recadre'){
				$dst_img = $filtre($src,$params[0],$params[1],$params[2]);
				spip_log("$filtre($src,$params[0],$params[1],$params[2]);","photospip");
			}else if(in_array($filtre, array('image_passe_partout','image_reduire'))){
				$dst_img = $filtre($src,$params[0],$params[1]);
				spip_log("$filtre($src,$params[0],$params[1]);","photospip");
			}
			elseif($filtre == 'image_sepia' && $params[0] && !is_null($params[0])){
				$params[0] = str_replace('#','',$params[0]);
				$dst_img = $filtre($src,$params[0]);
			}
			else if($params[0] && !is_null($params[0])){
				spip_log("$filtre($src,".$params[0].")","photospip");
				$dst_img = $filtre($src,$params[0]);
			}else{
				spip_log("$filtre($src)","photospip");
				$dst_img = $filtre($src);
			}
		}
		else{
			$dst_img = $filtre($src);
		}
		$dst_img = extraire_attribut($dst_img,'src');
		spip_log("après le filtre $filtre dst_img = $dst_img","photospip");		
	}else{
		spip_log('le filtre n existe pas','photospip');
		return false;
	}
	spip_log("dst_img = $dst_img",'photospip');
	if (preg_match(',^(.*)\?date=(\d+).([^.]+)$,', $dst_img, $match)) {
		$dst_img = $match[1];
		spip_log($match,'photospip');
		spip_log("On enlève la date : dst_img = $dst_img",'photospip');
	}
	//$dst_img = preg_replace(',\?date=\d+$,','', $dst_img);
	if(preg_match("/\.(png|gif|jpe?g)$/i", $src, $regs)) {
		switch($regs[1]) {
			case 'png':
			  if (function_exists('ImageCreateFromPNG')) {
				$src_img=ImageCreateFromPNG($dst_img);
				spip_log("creation png from $dst_img","photospip");
				$save = '_image_imagepng';
			  }
			  break;
			case 'gif':
			  if (function_exists('ImageCreateFromGIF')) {
				$src_img=ImageCreateFromGIF($dst_img);
				$save = '_image_imagegif';
			  }
			  break;
			case 'jpeg':
			case 'jpg':
			  if (function_exists('ImageCreateFromJPEG')) {
				$src_img=ImageCreateFromJPEG($dst_img);
				spip_log("creation jpg from $dst_img","photospip");
				$save = '_image_imagejpg';
			  }
			  break;
		}
	}

	//if (!$src_img) {
	//	spip_log("photospipfiltre : image non lue, $src","photospip");
	//	return false;
	//}
	//spip_log($src_img,'photospip');

	ImageInterlace($src_img,0);

	$image = $save($src_img,$dest,100);
	
	spip_log("dest $dest","photospip");
	return $image;
}

?>