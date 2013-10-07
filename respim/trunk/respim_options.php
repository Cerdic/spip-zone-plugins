<?php
/**
 * Options du plugin Responsive Imagesau chargement
 *
 * @plugin     Responsive Images
 * @copyright  2013
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Respim\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * http://coding.smashingmagazine.com/2013/06/02/clown-car-technique-solving-for-adaptive-images-in-responsive-web-design/
 *
 * @param array $rwd_images
 *   tableau
 *     width => file
 * @param int $width
 * @param int $height
 * @param string $alt
 * @param string $class
 * @param string $id
 * @return string
 */
function respim_embed($rwd_images, $width, $height, $alt="", $class="", $id=""){
	$svg = <<<SVG
<svg viewBox='0 0 $width $height' preserveAspectRatio='xMidYMid meet' xmlns='http://www.w3.org/2000/svg'>
<title>$alt</title>
<style>
svg{background-size:100% 100%;background-repeat:no-repeat;}
SVG;

	ksort($rwd_images);
	$prev_width = 0;
	$medias = array();
	foreach ($rwd_images as $w=>$file){
		$mw = ($prev_width?"(min-width:{$prev_width}px)":"(max-width:{$w}px)");
		$medias[$w] = "@media screen and $mw{svg{background-image:url($file);}}";
		$prev_width = $w+1;
	}
	$svg .= implode("\n",$medias);
	$svg .= "
  </style>
</svg>";

	// echapper le $svg
	$svg = str_replace("\n","",$svg);
	$svg = rawurlencode($svg);

	if ($class) $class=" $class";
	$class=" class=\"img responsive$class\"";
	if ($alt) $alt=" aria-label=\"$alt\"";
	if ($id) $id=" id=\"$id\"";

	$out = "<object role=\"img\"$alt tab-index=\"0\"
data=\"data:image/svg+xml,$svg\" type=\"image/svg+xml\"
width=\"$width\" height=\"$height\" style=\"max-width: 100%;height:auto;\"
$class$id></object>";
	return $out;
}

/**
 * @param string $img
 * @param string $target
 *   navigateur cible
 *   android2 => image petit format
 *   mobile => conteneur svg
 *   dektop => image normale
 *   auto => determination en fonction du UA (oui c'est mal)
 * @param array $bkpt
 * @return string
 */
function respim_image($img, $target="auto", $bkpt = array(320,480,780)){
	if (!$img) return $img;
	if (!function_exists("taille_image"))
		include_spip("inc/filtres");
	if (!function_exists("image_reduire"))
		include_spip("inc/filtres_images_mini");

	list($h,$w) = taille_image($img);
	if (!$w OR $w<=reset($bkpt)) return $img;

	static $ua_target = null;
	if (!in_array($target,array("android2","mobile","desktop"))){
		if (is_null($ua_target)){
			include_spip("lib/mobile_detect");
			$detect = MobileDetect::getInstance();
			$ua_target = "desktop";
			if ($detect->isMobile()){
				$ua_target = "mobile";
				if (strpos($_SERVER['HTTP_USER_AGENT'],"Android 2.")!==false)
					$ua_target = "android2";
			}
			if ($t = _request('var_respim'))
				$ua_target = $t;
		}
		$target = $ua_target;
	}

	// sur les desktop on laisse l'image en grand format (oui c'est arbitraire)
	if ($target=="desktop")
		return $img;

	$src = trim(extraire_attribut($img, 'src'));
	if (strlen($src) < 1){
		$src = $img;
		$img = "<img src='$src' />";
	}
	// on ne touche pas aux data:uri
	if (strncmp($src,"data:",5)==0)
		return $img;

	$images = array($w=>url_absolue($src));
	$src=preg_replace(',[?][0-9]+$,','',$src);

	// si on arrive pas a le lire, on ne fait rien
	if (!file_exists($src))
		return $img;

	foreach($bkpt as $wk){
		if ($wk>$w) break;
		$i = image_reduire($img,$wk,10000);
		// sur les android2 on renvoie les images dans le plus petit breakpoint
		// (oui c'est arbitraire)
		if ($target=="android2")
			return $i;
		$images[$wk] = url_absolue(extraire_attribut($i,"src"));
	}

	// pour les autres (les mobiles ou autres trucs tactiles apparentes, sauf android2, donc)
  // on renvoie un conteneur svg

	$alt = extraire_attribut($img, 'alt');
	$class = extraire_attribut($img, 'class');
	$id = extraire_attribut($img, 'id');

	return respim_embed($images,$w,$h,$alt,$class,$id);
}

function respim_affichage_final($texte){
	if ($GLOBALS['html']){
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
	}
	return $texte;
}

?>