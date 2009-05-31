<?php

// imageflow_fonctions.php

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of "Portfolio ImageFlow".
	
	"Portfolio ImageFlow" is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	"Portfolio ImageFlow" is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with "Portfolio ImageFlow"; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de "Portfolio ImageFlow". 
	
	"Portfolio ImageFlow" est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	"Portfolio ImageFlow" est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/filtres_images');
include_spip('inc/imageflow_api_globales');

/*
 * image_avec_reflet ()
 * Effet de reflet sur une image
 * @author Christian Paulus (paladin@quesaco.org), largement inspiré des scripts de SPIP et celui de Richard Davey
 * @param $img Balise de l'image source
 * @param $alt Texte alternatif
 * @param $name Nom de la balise; Utilise' par Porfolio Imageflow pour transmettre l'url de l'image originale
 * @param $title Titre de l'image
 * @param $longdesc Description de l'image ou URL sur description
 * @param $style CSS style
 * @param $bgc Couleur de fond. 'none' pour fond transparent. De'faut 'none'
 * @param $tint Teinte du reflet, en hexadécimal
 * @param $fade_start Opacite' de debut de degrade. Par defaut 80%
 * @param $fade_end Opacite' de fin de degrade. Par defaut 0%
 * @param $height Hauteur en pourcentage du reflet. Par de'faut 50%
 * @param $width Largeur finale. Par de'faut, prend celui des vignettes de'fini dans /ecrire/?exec=config_fonctions
 * @return Chemin de l'image re'sultat, ou false si erreur, ou "" si rien
 * @see http://www.quesaco.org/Portfolio-ImageFlow-pour-SPIP#image_avec_reflet
 * @see http://www.spip.net/fr_article3327.html
 * @see http://reflection.corephp.co.uk
 */
if(!function_exists('image_avec_reflet')) {
	function image_avec_reflet (
		$img
		, $alt = ""
		, $name = ""
		, $title = ""
		, $longdesc = ""
		, $style = "margin:0;padding:0;border:0;"
		, $bgc = ""
		, $tint = ""
		, $fade_start = ""
		, $fade_end = ""
		, $height = ""
		, $width = ""
	) {
		
		$attributs = array('alt', 'name', 'title', 'longdesc', 'style');
		$preferences_default = array(
			'bgc' => "none"
			,'tint' => "#7F7F7F"
			, 'fade_start' => "80%"
			, 'fade_end' => "0%"
			, 'height' => "50%"
		);
		$parametres = array_keys($preferences_default);
		
		// Si utilise' avec le plugin, prendre les reglages definis si non transmis par le filtre
		if(function_exists('imageflow_get_all_preferences')) 
		{
			$preferences_meta = imageflow_get_all_preferences();
		} 
		// Si filtre utilisé sans le plugin, appliquer les prefs par défaut
		foreach($preferences_default as $key => $default) 
		{
			// l'appel via un modele renvoie 0x0d0a0909090909 meme si vide (?)
			$$key = trim($$key);
			if(empty($$key)) 
			{
				$$key = isset($preferences_meta[$key]) ? $preferences_meta[$key] : $default;
			}
		}
		// cas à part. Pas dans les prefs.
		$width = trim($width);

		foreach(array_merge($attributs, $parametres) as $key) 
		{
			$$key = trim($$key);
		}

		if(($s = imageflow_php_gd_versions_ok()) !== true) {
			imageflow_log("Err: "._T('imageflow:'.$s));
			return(false);
		}	

		if(!$img) {
			imageflow_log("Err: Balise img manquante en parametre");
			return(false);
		}	

		if(!$src_img = imageflow_get_src ($img)) {
			imageflow_log("Err: Balise img src vide");
			return(false);
		}
		
		list($src_width, $src_height, $type) = getimagesize($src_img);
		
		if($src_width < 1) {
			imageflow_log("Err: image $fichier invalide");
			return(false);
		}
		
		if($type < 1 || $type > 3) {
			imageflow_log("Err: image $fichier format invalide (type: $type).");
			return(false);
		}
		
		/*
		 * La largeur est, par ordre de priorité :
		 * - celle transmise en argument
		 * - celle précisée dans la balise, attribut 'width'
		 * - celle originale
		 * Si $width < à l'original, réduire l'image
		 * Si $width >, confier l'agrandissement au navigateur via css
		 */
		$width = intval($width);
		$img_width = extraire_attribut($img, 'width');
		if ($width <= 0) {
			$width = $img_width ? $img_width : $src_width;
		}
		$dest_width = $width;
		if($width < $src_width) {
			$img = image_reduire($img, $width, 0);
			$src_img = imageflow_get_src ($img);
			list($src_width, $src_height, $type) = getimagesize($src_img);
		}
		elseif ($width > $src_width)
		{
			$width = $src_width;
		}
		
		/*
		 * calcul de la hauteur
		 */ 
		//	height (how tall should the reflection be?)
		if ($height) {
			$output_height = $height;
			//	Have they given us a percentage?
			if (substr($output_height, -1) == '%')
			{
				//	Yes, remove the % sign
				$output_height = (int) substr($output_height, 0, -1);
	
				//	Gotta love auto type casting ;)
				if ($output_height == 100)
				{
					$output_height = "0.99";
				}
				elseif ($output_height < 10)
				{
					$output_height = "0.0$output_height";
				}
				else
				{
					$output_height = "0.$output_height";
				}
			}
			else
			{
				$output_height = (int) $output_height;
			}
		}
		else
		{
			//	No height was given, so default to 50% of the source images height
			$output_height = 0.50;
		}
		
		//	Calculate the height of the output image
		if ($output_height < 1)
		{
			//	The output height is a percentage
			$new_height = $src_height * $output_height;
		}
		else
		{
			//	The output height is a fixed pixel value
			$new_height = $output_height;
		}

		$dest_height = intval($src_height + $new_height);
		
		if(!function_exists('hexcolor2hexdeccolor'))
		{
			function hexcolor2hexdeccolor ($color, $default) 
			{
				$result = array();
				
				//	Does it start with a hash? If so then strip it
				$hex_color = str_replace('#', '', $color);
				switch (strlen($hex_color))
				{
					case 6:
						$result['red'] = hexdec(substr($hex_color, 0, 2));
						$result['green'] = hexdec(substr($hex_color, 2, 2));
						$result['blue'] = hexdec(substr($hex_color, 4, 2));
						break;
					case 3:
						$result['red'] = substr($hex_color, 0, 1);
						$result['green'] = substr($hex_color, 1, 1);
						$result['blue'] = substr($hex_color, 2, 1);
						$result['red'] = hexdec($result['red'] . $result['red']);
						$result['green'] = hexdec($result['green'] . $result['green']);
						$result['blue'] = hexdec($result['blue'] . $result['blue']);
						break;
					default:
						//	Wrong values passed, default to white
						$result['red'] = $result['green'] = $result['blue'] = $default;
				} 
				return ($result);
			}
		}
		
		// bgc (the background colour used, transparent if 'none')
		if($bgc != 'none')
		{
			$bgcolor = hexcolor2hexdeccolor($bgc, 127);
		}
		else $bgcolor = false;
		
		//	tint (the colour used for the tint, defaults to white if not given)
		if (empty($tint))
		{
			$tintcolor['red'] = $tintcolor['green'] = $tintcolor['blue'] = 127;
		}
		else
		{
			//	Extract the hex colour
			$tintcolor = hexcolor2hexdeccolor($tint, 127);
			
		}
	
		if ($fade_start)
		{
			if (strpos($fade_start, '%') !== false)
			{
				$alpha_start = str_replace('%', '', $fade_start);
				$alpha_start = (int) (127 * $alpha_start / 100);
			}
			else
			{
				$alpha_start = (int) $fade_start;
			
				if ($alpha_start < 1 || $alpha_start > 127)
				{
					$alpha_start = 80;
				}
			}
		}
		else
		{
			$alpha_start = 80;
		}
	
		if ($fade_end)
		{
			if (strpos($fade_end, '%') !== false)
			{
				$alpha_end = str_replace('%', '', $fade_end);
				$alpha_end = (int) (127 * $alpha_end / 100);
			}
			else
			{
				$alpha_end = (int) $fade_end;
			
				if ($alpha_end < 1 || $alpha_end > 0)
				{
					$alpha_end = 0;
				}
			}
		}
		else
		{
			$alpha_end = 0;
		}
	
		/*
		 * place l'image en cache ou lire en cache si existante
		 */
		$effet = "reflet-".$width."-".$dest_height."-".$alpha_start."-".$alpha_end."-"
			. $tintcolor['red']."-".$tintcolor['green']."-".$tintcolor['blue'] . "-"
			. ($bgcolor ? $bgcolor['red']."-".$bgcolor['green']."-".$bgcolor['blue'] : "nobgc")
			;
		$image = image_valeurs_trans($img, $effet, "png");
		if (!$image)
		{
			imageflow_log("Err: image image_valeurs_trans");
			return("");	
		}

		// L'image n'est pas en cache ? La créer.
		if($image["creer"]) {
			
			$im = $image["fichier"];
			$dest = $image["fichier_dest"];
			
			$source = $image["fonction_imagecreatefrom"]($im);
			
			/*
				----------------------------------------------------------------
				Build the reflection image
				----------------------------------------------------------------
			*/
		
			//	We'll store the final reflection in $output. $buffer is for internal use.
			$output = imagecreatetruecolor($width, $new_height);
			$buffer = imagecreatetruecolor($width, $new_height);
			
			//  Save any alpha data that might have existed in the source image and disable blending
			imagesavealpha($source, true);
		
			imagesavealpha($output, true);
			imagealphablending($output, false);
		
			imagesavealpha($buffer, true);
			imagealphablending($buffer, false);
		
			//	Copy the bottom-most part of the source image into the output
			imagecopy($output, $source, 0, 0, 0, $src_height - $new_height, $width, $new_height);

			//	Rotate and flip it (strip flip method)
			for ($y = 0; $y < $new_height; $y++)
			{
			   imagecopy($buffer, $output, 0, $y, 0, $new_height - $y - 1, $width, 1);
			}
		
			$output = $buffer;
		
			/*
				----------------------------------------------------------------
				Apply the fade effect
				----------------------------------------------------------------
			*/
			
			//	This is quite simple really. There are 127 available levels of alpha, so we just
			//	step-through the reflected image, drawing a box over the top, with a set alpha level.
			//	The end result? A cool fade.
		
			//	There are a maximum of 127 alpha fade steps we can use, so work out the alpha step rate
		
			$alpha_length = abs($alpha_start - $alpha_end);
		
			imagelayereffect($output, IMG_EFFECT_OVERLAY);
		
			for ($y = 0; $y <= $new_height; $y++)
			{
				//  Get % of reflection height
				$pct = $y / $new_height;
		
				//  Get % of alpha
				if ($alpha_start > $alpha_end)
				{
					$alpha = (int) ($alpha_start - ($pct * $alpha_length));
				}
				else
				{
					$alpha = (int) ($alpha_start + ($pct * $alpha_length));
				}
				
				//  Rejig it because of the way in which the image effect overlay works
				$final_alpha = 127 - $alpha;
		
				//imagefilledrectangle($output, 0, $y, $width, $y, imagecolorallocatealpha($output, 127, 127, 127, $final_alpha));
				imagefilledrectangle($output, 0, $y, $width, $y
					, imagecolorallocatealpha($output, $tintcolor['red'], $tintcolor['green'], $tintcolor['blue'], $final_alpha)
				);
			}
			
			/*
			 * Ajoute l'image source au résultat en respectant le canal alpha
			 */
			$finaloutput = imagecreatetruecolor($width, $dest_height);
			if(is_array($bgcolor))
			{
				imagefilledrectangle($finaloutput, 0, 0, $width, $dest_height
					, imagecolorallocatealpha($finaloutput, $bgcolor['red'], $bgcolor['green'], $bgcolor['blue'], 0)
				);
			}
			else {
				imagealphablending($finaloutput, false);
				imagesavealpha($finaloutput, true);
			}
			imagecopy($finaloutput, $output, 0, $src_height, 0, 0, $width, $new_height);
			imagecopy($finaloutput, $source, 0, 0, 0, 0, $width, $src_height);
			$output = $finaloutput;
	
			/*
			 * Enregistre le résultat en cache
			 */
			//$fichier = image_imagepng($output, $cache_path);
			$image["fonction_image"]($output, "$dest");
			
			imagedestroy($buffer);
			imagedestroy($output);
		}
		
		$tags = "";
		
		$src = $image['fichier_dest'];
		$class = $image['class'];
		$class = (!empty($class) ? $class." " : "")."spip_reflets";
		// $width // déjà calculé plus haut
		$height = $dest_height;
		// $name // donné en paramètre
		$alt = (!empty($alt) ? $alt : "'".$image["alt"]."'");
		// $title // en paramètre
		$longdesc = longdesc_propre($longdesc);
		//$style = $image['style'].$style;
		if($dest_width > $width) 
		{
			$height = ceil($height * ($dest_width / $width));
			$style = trim($style, ";");
			$style .= ";width:".$dest_width.";height:".$dest_height.";";
			$width = $dest_width;
		}
		
		foreach(array_merge($attributs, array('src', 'class', 'width', 'height')) as $key) 
		{
			if(!empty($$key)) {
				$tags .= $key."=\"".$$key."\" ";
			}
		}
		return("<img ".$tags."/>\n");
	}
}

/*
 * longdesc_propre ()
 * Complète l'attribut longdesc, brut si URL ou ancre, schéma data:text si texte
 * @author Christian Paulus (paladin@quesaco.org)
 * @param $longdesc URL absolue ou texte description
 * @return Longdesc complété ou ""
 * @see http://www.quesaco.org/Portfolio-ImageFlow-pour-SPIP#longdesc
 */
if(!function_exists('longdesc_propre')) {
	function longdesc_propre ($longdesc, $charset = "iso-8859-1") {
		$longdesc = trim($longdesc);
		if(empty($longdesc)) {
			return("");
		}
		$is_uri = (
			preg_match(';^(\w{3,7}://);', $longdesc) 
			|| preg_match(';^(#);', $longdesc)
			);
		if(!$is_uri) {
			// un peu basique comme filtre. 
			// A voir + tard (vérifier la présence du fichier ?)
			// ne prend pas en compte $type_urls = "propres"
			if(($u = parse_url($longdesc)) && ($u = $u['path'])
				&& preg_match(';\.(php|html)$;', $u)
			) {
				$is_uri = true;
			}
		}
		if(!$is_uri && ($charset != $GLOBALS['meta']['charset']))
		{
			include_spip('inc/charsets');
			$longdesc = charset2unicode($longdesc);
			$longdesc = unicode2charset($longdesc, $charset);
		}
		$longdesc = 
			($is_uri)
			? $longdesc
			: "data:text/plain;charset=".$charset.",".rawurlencode($longdesc)
			;
		return ($longdesc);
	}
}

/*
 * imageflow_get_src ()
 * Retourne le path propre de la balise image
 * @author Christian Paulus (paladin@quesaco.org)
 * @param $img Balise image calculée par SPIP
 * @return Chemin de l'image
 */
function imageflow_get_src ($img) {
	$src_img = extraire_attribut($img, 'src');
	$src_img = parse_url($src_img);
	$src_img = $src_img['path'];
	return ($src_img);		
}

?>