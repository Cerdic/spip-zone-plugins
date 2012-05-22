<?php
/**
 * Photospip
 * Un Photoshop-light dans spip?
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
/*

@argument balise : balise sur lequel est appliquer le filtre appliquer_filtre
@argument filtre : nom du filtre à appliquer
@param : paramètres pour le filtre 

@return : retourn la balise traité par filtre si le filtre existe autrement la balise appelante non traitée

*/

function photospip_appliquer_filtre($balise, $filtre,$param1=NULL,$param2=NULL,$param3=NULL) {
	$filtre = chercher_filtre($filtre);
	spip_log("On a trouvé $filtre",'photospip');
	if (function_exists($filtre)){
		spip_log("$filtre($balise,$param1,$param2,$param3);","photospip");
		if ($param1){
			return $filtre($balise,$param1,$param2,$param3);
			spip_log("$filtre($balise,$param1,$param2,$param3);","photospip");
		}
		else{
			return $filtre($balise);
			spip_log("$filtre($balise,$param1,$param2,$param3);","photospip");
		}
	} else {
		return $balise;
	}
}

/////////////////////////////////////////////////////////////////////
//
// Appliquer le filtre image
//
/////////////////////////////////////////////////////////////////////

function photospipfiltre ($src, $dest, $filtre,$params){
	spip_log("src = $src","photospip");
	spip_log("dest = $dest","photospip");
	spip_log("filtre = $filtre","photospip");
	spip_log("params = $params","photospip");
	
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
			}
			else{
				spip_log("$filtre($src,".$params[3].")","photospip");
				$dst_img = $filtre($src,$params[3]);		
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

	if(preg_match("/\.(png|gif|jpe?g|bmp)$/i", $src, $regs)) {
		switch($regs[1]) {
			case 'png':
			  if (function_exists('ImageCreateFromPNG')) {
				$src_img=ImageCreateFromPNG($dst_img);
				spip_log("creation png from $dst_img","photospip");
				$save = 'imagepng';
			  }
			  break;
			case 'gif':
			  if (function_exists('ImageCreateFromGIF')) {
				$src_img=ImageCreateFromGIF($dst_img);
				$save = 'imagegif';
			  }
			  break;
			case 'jpeg':
			case 'jpg':
			  if (function_exists('ImageCreateFromJPEG')) {
				$src_img=ImageCreateFromJPEG($dst_img);
				spip_log("creation jpg from $dst_img","photospip");
				$save = 'Imagejpeg';
			  }
			  break;
			case 'bmp':
			  if (function_exists('ImageCreateFromWBMP')) {
				$src_img=@ImageCreateFromWBMP($dst_img);
				$save = 'imagewbmp';
			  }
			  break;
		}
	}

	if (!$src_img) {
		spip_log("photospipfiltre : image non lue, $src","photospip");
		return false;
	}

	$size=getimagesize($src);
	if (!($size[0] * $size[1])) return false;

	ImageInterlace($src_img,0);

	$save($src_img,$dest);
	spip_log("dest $dest","photospip");
	return true;
}
?>