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
 * Insertion dans le pipeline post_image_filtrer (SPIP)
 * 
 * On passe toutes les images dans le filter image_smush sauf si :
 * -* une constante _SMUSH_INTERDIRE_AUTO est définier
 * -* la case eviter_traitement_auto de la config est cochée
 * 
 * @param string $flux
 * 		Le tag image (<img src...>) à réduire
 * @return string $flux
 * 		Le nouveau tag image
 */
function smush_post_image_filtrer($flux) {
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	if((!isset($GLOBALS['meta']['smush_casse']) || $GLOBALS['meta']['smush_casse'] != 'oui') && !defined('_SMUSH_INTERDIRE_AUTO') && (lire_config('smush/eviter_traitement_auto','off') != 'on'))
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
			$dest = $dest.'.png';
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
			if ($fsize < 10*1024)
				exec('jpegtran -copy none -optimize '.$im.' > '.$dest);
			else
				exec('jpegtran -copy none -progressive '.$im.' > '.$dest);
		}
	}
	return _image_ecrire_tag($image,array('src'=>$dest));
}

/**
 * Pipeline taches_generales_cron de Smush (SPIP)
 *
 * Vérifie la présence à intervalle régulier des logiciels présents
 * 
 * @param array $taches_generales 
 * 		Un array des tâches du cron de SPIP
 * @return array $taches_generales
 * 		L'array des taches complété
 */
function smush_taches_generales_cron($taches_generales){
	$taches_generales['smush_taches_generales'] = 24*60*60;
	return $taches_generales;
}
?>