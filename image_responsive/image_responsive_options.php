<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

if (!defined("_IMAGE_RESPONSIVE_CALCULER")) define("_IMAGE_RESPONSIVE_CALCULER", false);
if (!defined("_SPIP_LIER_RESSOURCES")) define("_SPIP_LIER_RESSOURCES", false);


function _findSharp($intOrig, $intFinal) {
  $intFinal = $intFinal * (750.0 / $intOrig);
  $intA     = 52;
  $intB     = -0.27810650887573124;
  $intC     = .00047337278106508946;
  $intRes   = $intA + $intB * $intFinal + $intC * $intFinal * $intFinal;
  return max(round($intRes), 0);
}


function image_reduire_net($source, $taille = 0, $taille_y=0, $dpr=0) {
	// ordre de preference des formats graphiques pour creer les vignettes
	// le premier format disponible, selon la methode demandee, est utilise

	// initialisations
	$force = false;
	$test_cache_only = false;

	//$force = true;
	include_spip('inc/filtres_images_lib_mini');
	
	if ($dpr > 1) {
		$taille = $taille * $dpr;
		$taille_y = $taille_y * $dpr;	
	}

	if ($taille == 0 AND $taille_y > 0)
		$taille = 10000; # {0,300} -> c'est 300 qui compte
	elseif ($taille > 0 AND $taille_y == 0)
		$taille_y = 10000; # {300,0} -> c'est 300 qui compte
	elseif ($taille == 0 AND $taille_y == 0)
		return '';

	$valeurs = _image_valeurs_trans($source, "reduire_net-{$taille}-{$taille_y}-{$dpr}", false);
	$image = $valeurs['fichier'];
	$format = $valeurs['format_source'];

	$destdir = dirname($valeurs['fichier_dest']);
	$destfile = basename($valeurs['fichier_dest'],".".$valeurs["format_dest"]);


	
	$format_sortie = $valeurs['format_dest'];
	
	// liste des formats qu'on sait lire
	$img = isset($GLOBALS['meta']['formats_graphiques'])
	  ? (strpos($GLOBALS['meta']['formats_graphiques'], $format)!==false)
	  : false;

	// si le doc n'est pas une image, refuser
	if (!$force AND !$img) return;
	$destination = "$destdir/$destfile";


	// chercher un cache
	$vignette = '';
	if ($test_cache_only AND !$vignette) return;

	// utiliser le cache ?
	if (!$test_cache_only)
	if ($force OR !$vignette OR (@filemtime($vignette) < @filemtime($image))) {

		$creation = true;
		// calculer la taille
		if (($srcWidth=$valeurs['largeur']) && ($srcHeight=$valeurs['hauteur'])){
			if (!($destWidth=$valeurs['largeur_dest']) || !($destHeight=$valeurs['hauteur_dest'])) {
				list ($destWidth, $destHeight) = _image_ratio($valeurs['largeur'], $valeurs['hauteur'], $maxWidth, $maxHeight);
			}
		}
		else {
			$destWidth = $maxWidth;
			$destHeight = $maxHeight;
		}

		// Si l'image est de la taille demandee (ou plus petite), simplement
		// la retourner
		if ($srcWidth
		AND $srcWidth <= $maxWidth AND $srcHeight <= $maxHeight) {
			$vignette = $destination.'.'.$format;
			@copy($image, $vignette);
		}
		else {
			if (_IMG_GD_MAX_PIXELS && $srcWidth*$srcHeight>_IMG_GD_MAX_PIXELS){
				spip_log("vignette gd1/gd2 impossible : ".$srcWidth*$srcHeight."pixels");
				return $image;
			}
			$destFormat = $format_sortie;
			if (!$destFormat) {
				spip_log("pas de format pour $image");
				return;
			}
			
			$fonction_imagecreatefrom = $valeurs['fonction_imagecreatefrom'];
			if (!function_exists($fonction_imagecreatefrom))
				return '';
			$srcImage = @$fonction_imagecreatefrom($image);
			if (!$srcImage) { 
				spip_log("echec gd1/gd2"); 
				return $image; 
			} 
			
			// Initialisation de l'image destination 
			if ($destFormat != "gif") 
				$destImage = ImageCreateTrueColor($destWidth, $destHeight); 
			if (!$destImage) 
				$destImage = ImageCreate($destWidth, $destHeight); 

			// Recopie de l'image d'origine avec adaptation de la taille 
			$ok = false; 
			if (function_exists('ImageCopyResampled')) { 
				if ($format == "gif") { 
					// Si un GIF est transparent, 
					// fabriquer un PNG transparent  
					$transp = imagecolortransparent($srcImage); 
					if ($transp > 0) $destFormat = "png"; 
				}
				if ($destFormat == "png") { 
					// Conserver la transparence 
					if (function_exists("imageAntiAlias")) imageAntiAlias($destImage,true); 
					@imagealphablending($destImage, false); 
					@imagesavealpha($destImage,true); 
				}
				$ok = @ImageCopyResampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
			}
			if (!$ok)
				$ok = ImageCopyResized($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
			
			//die (phpversion());
			
			if($destFormat == "jpg" && function_exists('imageconvolution')) {
				$intSharpness = _findSharp($srcWidth, $destWidth);
				$arrMatrix = array(
					array(-1, -2, -1),
					array(-2, $intSharpness + 12, -2),
					array(-1, -2, -1)
				);
				
				$div = array_sum(array_map('array_sum', $arrMatrix));  
				//die ("div: ".$div);
				
				// On s'arrange maintenant pour que le divisor soit 1
				// parce qu'imageconvoluion en PHP 5.5.9 semble ne prendre en compte
				// que cette valeur
				$arrMatrix = array(
					array(-1/$div, -2/$div, -1/$div),
					array(-2/$div, ($intSharpness + 12)/$div, -2/$div),
					array(-1/$div, -2/$div, -1/$div)
				);
				$divisor = array_sum(array_map('array_sum', $arrMatrix));            
				//die ("divisor: ".$divisor);
								

				imageconvolution($destImage, $arrMatrix, $divisor, 0);
			}
			// Sauvegarde de l'image destination
			$valeurs['fichier_dest'] = $vignette = "$destination.$destFormat";
			$valeurs['format_dest'] = $format = $destFormat;
			
			if ($dpr > 1.5) $qualite = 40;
			else $qualite=_IMG_GD_QUALITE;
			_image_gd_output($destImage,$valeurs, $qualite);

			if ($srcImage)
				ImageDestroy($srcImage);
			ImageDestroy($destImage);
		}
	}
	$size = @getimagesize($vignette);
	// Gaffe: en safe mode, pas d'acces a la vignette,
	// donc risque de balancer "width='0'", ce qui masque l'image sous MSIE
	if ($size[0] < 1) $size[0] = $destWidth;
	if ($size[1] < 1) $size[1] = $destHeight;
	
	$largeur = $size[0];
	$hauteur = $size[1];
	$date = @filemtime($vignette);
	

	// dans l'espace prive mettre un timestamp sur l'adresse 
	// de l'image, de facon a tromper le cache du navigateur
	// quand on fait supprimer/reuploader un logo
	// (pas de filemtime si SAFE MODE)
	$date = test_espace_prive() ? ('?'.$date) : '';

	return _image_ecrire_tag(
		$valeurs,
		array('src'=>"$vignette",
		'width'=>$largeur,
		'height'=>$hauteur)
	);

}



function retour_image_responsive($img, $taille, $dpr, $xsendfile, $retour="http"){
	if (!preg_match(',\.(gif|jpe?g|png)$,i', $img)
	OR !preg_match(',^\d+v?$,', $taille)
	OR !preg_match(',^[\d\.]*$,', $dpr)
	OR !file_exists($img)) {
		if($retour == "http") {
			header('HTTP/1.1 500 Internal Server Error');
			die( "Erreur" );
		} else {
			return find_in_path("rien.gif");
		}
	} else {

		include_spip("inc/filtres");
		if (preg_match("/([0-9]+)v$/", $taille, $regs)) {
			$taille = $regs[1];
			$v = true;
			$taille = min($taille, hauteur($img));
		} else {
			$v = false;
			$taille = min($taille, largeur($img));
		}

	
		$terminaison = substr($img, strlen($img)-3, 3);
		$base = sous_repertoire(_DIR_VAR, "cache-responsive");
		$base = sous_repertoire($base, "cache-".$taille);
		$dest = md5($img);
		if ($dpr > 1) $dest .= "$dest-$dpr";
		else $dpr = false;
		
		$dest = $base.$dest.".".$terminaison;

		if (file_exists($dest)) {
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
				strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($dest))
			{
				if($retour == "http") {
					header('HTTP/1.0 304 Not Modified');
					exit;
				} else {
					return $img;
				}
			}
		}
		
		if (!file_exists($dest) OR filemtime($dest) < filemtime($img) ) {
			// Là on fabrique l'image
			// et on la recopie vers $dest
			//
			//cette méthode permet d'accélérer par rapport à SPIP
			// parce qu'on connait le nom du fichier à l'avance
			// et on fait donc les tests sans déclencher la cavalerie
			spip_log("fabrication de l'image responsive $dest","image_responsive");
			if ($v) {	
				$img_new = image_reduire_net ($img, 0, $taille, $dpr);
			} else {
				$img_new = image_reduire_net ($img, $taille, 0, $dpr);
			}
			$img_new = extraire_attribut($img_new, "src");
			
			copy($img_new, $dest);
			if ($img_new != $img) unlink ($img_new);
		}

		if($retour == "http") {
			$extension = str_replace("jpg", "jpeg", $terminaison);
			$expires = 60*60*24*14;
		
			if ($xsendfile == 1) {	
				$dest = realpath("$dest");
				//die($dest);
				header("X-Sendfile: $dest");
				header("Content-Type: image/".$extension);
				exit;
			} else {
				ob_get_clean();
				header("Content-Type: image/".$extension);
				header("Pragma: public");
				header("Cache-Control: max-age=".$expires);
				header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
				header('Content-Length: '.filesize($dest));
		
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($dest)).' GMT', true, 200);
				readfile($dest);
				if (ob_get_level()) {
					ob_end_flush();
				}
			}
		} else {
			return timestamp($dest);
		}

	}
}
