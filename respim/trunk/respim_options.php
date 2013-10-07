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

	ksort($rwd_images);
	$cid = "c".crc32(serialize($rwd_images));
	$style =
"img.$cid{opacity:0.01;filter:alpha(opacity=1);width:{$width}px;max-width:100%;height:auto;}
b.$cid{background-size:100%;background-repeat:no-repeat;display:inline-block;max-width:100%}
";

	// image de fallback fournie ?
	if (isset($rwd_images['fallback'])){
		$fallback_file = $rwd_images['fallback'];
		unset($rwd_images['fallback']);
	}
	// sinon on affiche la plus petite image
	if (!$fallback_file)
		$fallback_file = reset($rwd_images);
	/* SI compat IE7 necessaire : pas de base64 dans le src, donc image externe avec un hit de plus... */
	if (!defined('_RESPIM_PRESERVE_IE7_COMPAT'))
		$fallback_file = filtre_embarque_fichier($fallback_file,"",32000);

	$prev_width = 0;
	$medias = array();
	foreach ($rwd_images as $w=>$file){
		$mw = ($prev_width?"(min-width:{$prev_width}px)":"(max-width:{$w}px)");
		$mw20 = ($prev_width?"(min-width:".round($prev_width/2)."px)":"(max-width:".round($w/2)."px)");
		$mw15 = ($prev_width?"(min-width:".round($prev_width/1.5)."px)":"(max-width:".round($w/1.5)."px)");
		$medias[$w] = "@media screen and $mw,screen and (-webkit-min-device-pixel-ratio: 2) and $mw20,screen and (-webkit-min-device-pixel-ratio: 1.5) and $mw15,screen and (min--moz-device-pixel-ratio: 2) and $mw20,screen and (min--moz-device-pixel-ratio: 1.5) and $mw15{b.$cid{background-image:url($file);}}";
		$prev_width = $w+1;
	}
	$style .= "b.$cid{background-image:url($file);}";
	$style .= implode("\n",$medias);

	if ($class) $class=" $class";
	$class=" class=\"responsive $cid$class\"";
	if ($alt) $alt=" alt=\"$alt\"";
	if ($id) $id=" id=\"$id\"";

	$out = "<b class=\"$cid\"><img
$alt
src=\"$fallback_file\"
width=\"$width\" height=\"$height\"
$class$id /></b>
<style>$style</style>";
	return $out;
}

/**
 * @param string $img
 * @param string $target
 *   navigateur cible
 *   mobile => <img> avec fallback base64 petite taille basse qualite et mediaquerie pour charger la "bonne" image
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
	if (!function_exists("image_aplatir"))
		include_spip("filtres/images_transforme");

	list($h,$w) = taille_image($img);
	if (!$w OR $w<=reset($bkpt)) return $img;

	static $ua_target = null;
	if (!in_array($target,array("mobile","desktop"))){
		if (is_null($ua_target)){
			include_spip("lib/mobile_detect");
			$detect = MobileDetect::getInstance();
			$ua_target = "desktop";
			if ($detect->isMobile()){
				$ua_target = "mobile";
			}
			if ($t = _request('var_respim') AND in_array($t,array("mobile","desktop")))
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

	$images = array($w=>$src);
	$src=preg_replace(',[?][0-9]+$,','',$src);

	// si on arrive pas a le lire, on ne fait rien
	if (!file_exists($src))
		return $img;

	foreach($bkpt as $wk){
		if ($wk>$w) break;
		$i = image_reduire($img,$wk,10000);
		$images[$wk] = extraire_attribut($i,"src");
	}

	if (function_exists("image_aplatir")){
		// image de fallback : la plus petite en jpg compresse
		$fallback = image_aplatir($images[reset($bkpt)],'jpg','ffffff',51);
		$images["fallback"] = extraire_attribut($fallback,"src");
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