<?php
/**
 * Options du plugin Responsive Images
 *
 * @plugin     Responsive Images
 * @copyright  2013
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Respim\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 *
 * @param string $img
 * @param array $rwd_images
 *   tableau
 *     width => file
 * @param int $width
 * @param int $height
 * @return string
 */
function respim_markup($img, $rwd_images, $width, $height){
	$class = extraire_attribut($img,"class");
	if (strpos($class,"respim")!==false) return $img;
	ksort($rwd_images);
	$cid = "c".crc32(serialize($rwd_images));
	$style =
"img.respim{opacity:0.01;max-width:100%;height:auto;}"
."b.respwrapper{background-size:100%;background-repeat:no-repeat;display:inline-block;max-width:100%}";
	if ($class) $class = " $class";
	$class = "$cid$class";
	$img = inserer_attribut($img,"class","respim-fallback $class");

	// image de fallback fournie ?
	if (isset($rwd_images['fallback'])){
		$fallback_file = $rwd_images['fallback'];
		unset($rwd_images['fallback']);
	}
	// sinon on affiche la plus petite image
	if (!$fallback_file)
		$fallback_file = reset($rwd_images);
	// embarquer le fallback en DATA URI si moins de 32ko (eviter une page trop grosse)
	$fallback_file = filtre_embarque_fichier($fallback_file,"",32000);

	$prev_width = 0;
	$medias = array();
	foreach ($rwd_images as $w=>$file){
		$mw = ($prev_width?"(min-width:{$prev_width}px)":"(max-width:{$w}px)");
		$mw20 = ($prev_width?"(min-width:".round($prev_width/2)."px)":"(max-width:".round($w/2)."px)");
		$mw15 = ($prev_width?"(min-width:".round($prev_width/1.5)."px)":"(max-width:".round($w/1.5)."px)");
		// ?rwd pour le debug (etre sur qu'on charge bien l'image issue des medias queries)
		$medias[$w] = "@media screen and $mw,screen and (-webkit-min-device-pixel-ratio: 2) and $mw20,screen and (-webkit-min-device-pixel-ratio: 1.5) and $mw15,screen and (min--moz-device-pixel-ratio: 2) and $mw20,screen and (min--moz-device-pixel-ratio: 1.5) and $mw15{b.$cid{background-image:url($file?rwd);}}";
		$prev_width = $w+1;
	}
	$style .= "b.$cid{background-image:url($file);}";
	$style .= implode("",$medias);


	$out = "<!--[if IE]>$img<![endif]-->\n";
	$img = inserer_attribut($img,"src",$fallback_file);
	$img = inserer_attribut($img,"class","respim $class");
	$img = inserer_attribut($img,"onmousedown","var i=window.getComputedStyle(this.parentNode).backgroundImage.replace(/\W?\)$/,'').replace(/^url\(\W?|/,'');this.src=(i&&i!='none'?i:this.src);");
	$out .= "<!--[if !IE]--><b class=\"respwrapper $cid\">$img</b>\n<style>$style</style><!--[endif]-->";

	return $out;
}

/**
 * extrait les infos d'une image,
 * calcule les variantes en fonction des breakpoints
 * si l'image est de taille superieure au plus petit breakpoint
 * et renvoi un markup responsive si il y a lieu
 *
 * @param string $img
 * @param array $bkpt
 * @return string
 */
function respim_image($img, $bkpt = array(320,480,780)){
	if (!$img) return $img;
	if (strpos($img,"respim")!==false
	  OR strpos($img,"spip_logos")!==false)
		return $img;

	if (!function_exists("taille_image"))
		include_spip("inc/filtres");
	if (!function_exists("image_reduire"))
		include_spip("inc/filtres_images_mini");
	if (!function_exists("image_aplatir"))
		include_spip("filtres/images_transforme");

	list($h,$w) = taille_image($img);
	if (!$w OR $w<=reset($bkpt)) return $img;

	$src = trim(extraire_attribut($img, 'src'));
	if (strlen($src) < 1){
		$src = $img;
		$img = "<img src='$src' />";
	}
	// on ne touche pas aux data:uri
	if (strncmp($src,"data:",5)==0)
		return $img;

	$images = array($w=>$src);
	$src=preg_replace(',[?][0-9]+$,','',$src);

	// si on arrive pas a le lire, on ne fait rien
	if (!file_exists($src))
		return $img;

	// calculer les variantes d'image sur les breakpoints
	$large = "";
	foreach($bkpt as $wk){
		if ($wk>$w) break;
		$i = image_reduire($img,$wk,10000);
		$large = $images[$wk] = extraire_attribut($i,"src");
	}

	// l'image de fallback en jpg tres compresse
	if (function_exists("image_aplatir")){
		// image de fallback : la plus petite en jpg compresse
		$fallback = image_aplatir($large,'jpg','ffffff',15);
		$images["fallback"] = extraire_attribut($fallback,"src");
	}

	// generer le markup
	return respim_markup($img,$images,$w,$h);
}

/**
 * Traiter les images de la page et les passer en responsive
 * si besoin
 *
 * @param $texte
 * @return mixed
 */
function respim_affichage_final($texte){
	if ($GLOBALS['html']){
		#spip_timer();
		$replace = array();
		preg_match_all(",<img\s[^>]*>,Uims",$texte,$matches,PREG_SET_ORDER);
		foreach($matches as $m){
			$ri = respim_image($m[0]);
			if ($ri!==$m[0]){
				$replace[$m[0]] = $ri;
			}
		}
		if (count($replace))
			$texte = str_replace(array_keys($replace),array_values($replace),$texte);
		#var_dump(spip_timer());
	}
	return $texte;
}

?>