<?php
/**
 * Plugin smushit
 * 
 * Auteur :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * @package SPIP\Smushit\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline post_image_filtrer
 * 
 * @param string $flux
 * 		Le tag image (<img src...>) à réduire
 * @return string $flux
 * 		Le nouveau tag image
 */
function smush_post_image_filtrer($flux) {
	$flux = filtrer('image_smush',$flux);
	return $flux;
}

/**
 * Fonction de réduction d'image
 * Nécessite que la fonction exec() soit utilisable
 * Nécessite certains binaires sur le serveur :
 * -* identify : apt-get install imagemagick
 * -* convert : apt-get install imagemagick
 * -* pngnq : apt-get install pngnq
 * -* pngoptim : apt-get install pngoptim
 * -* jpegtran : apt-get install libjpeg-progs
 * 
 * @param string $im
 * 		Le tag image (<img src...>) à réduire
 * @return string
 * 		Le nouveau tag image
 */
function image_smush($im) {
	$fonction = array('smush', func_get_args());
	$image = _image_valeurs_trans($im,"smush",false,$fonction);
	
	if (!$image) return("");
	
	$im = $image["fichier"];
	$dest = $image["fichier_dest"];
	
	$creer = $image["creer"];

	if ($creer) {
		$format = trim(exec('identify -format %m '.$im));
	
		if ($format == 'GIF') {
			$dest = $tmp.'.png';
			exec('convert '.$im.' '.$dest);
			$source = $dest;
			$format = 'PNG';
		}
	
		else if ($format == 'PNG') {
			$nq = substr($im,0,-4).'-nq8.png';
			exec('pngnq '.$im.' && optipng -o5 '.$nq.' -out '.$dest,$out);
			if(file_exists($nq))
				spip_unlink($nq);
		}
	
		else if ($format == 'JPEG') {
			$fsize = filesize($im);
			$dest = $tmp.'.jpg';
			if ($fsize < 10*1024) {
				exec('jpegtran -copy none -optimize '.$im.' > '.$dest);
			}
			else {
				exec('jpegtran -copy none -progressive '.$im.' > '.$dest);
			}
		}
	}
	return _image_ecrire_tag($image,array('src'=>$dest));
}
?>