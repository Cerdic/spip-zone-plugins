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

if (!defined('_RESPIM_NOJS_PNGGIF_PROGRESSIVE_RENDERING')) define('_RESPIM_NOJS_PNGGIF_PROGRESSIVE_RENDERING',false);
if (!defined('_RESPIM_LOWSRC_JPG_BG_COLOR')) define('_RESPIM_LOWSRC_JPG_BG_COLOR','ffffff');
if (!defined('_RESPIM_LOWSRC_JPG_QUALITY')) define('_RESPIM_LOWSRC_JPG_QUALITY',10);

/**
 *
 * @param string $img
 * @param array $rwd_images
 *   tableau
 *     width => file
 * @param int $width
 * @param int $height
 * @param string $extension
 * @return string
 */
function respim_markup($img, $rwd_images, $width, $height, $extension){
	$class = extraire_attribut($img,"class");
	if (strpos($class,"respim")!==false) return $img;
	ksort($rwd_images);
	$cid = "c".crc32(serialize($rwd_images));
	$style = "";
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
	$lastw = array_keys($rwd_images);
	$lastw = end($lastw);
	$hasmax = true;
	foreach ($rwd_images as $w=>$file){
		if ($w==$lastw) {$hasmax=false;$islast = true;}
		// $file = "filedelay.api/5/$file"; // debug : injecter une tempo dans le chargement de l'image pour tester l'enrichissement progressif
		$file = $file."?rwd"; // debug  : etre sur qu'on charge bien l'image issue des medias queries
		// il faut utiliser une clause min-width and max-width pour que les regles soient exlusives
		// sinon on a multiple load sous android 2.x
		$mw = ($prev_width?"and (min-width:{$prev_width}px)":"").($hasmax?" and (max-width:{$w}px)":"");
		$mw20 = $mw15 = "";
		if ($w>=640 OR $islast){
			$mw20 = ($prev_width?"and (min-width:".floor(($prev_width-1)/2+1)."px)":"").($hasmax?" and (max-width:".floor($w/2)."px)":"");
		}
		if ($w>=480 OR $islast){
			$mw15 = ($prev_width?"and (min-width:".floor(($prev_width-1)/1.5+1)."px)":"").($hasmax?" and (max-width:".floor($w/1.5)."px)":"");
		}
		$not = "html:not(.ahrdpi)";
		if ($prev_width<=800 AND ($w>=800 OR $islast))
			$not.=":not(.avp800)";
		$not .= " ";
		$medias[$w] = "@media screen $mw{{$not}b.$cid,{$not}b.$cid:after{background-image:url($file);}}";
		$mhr = array();
		if ($mw20)
			$mhr[] = "screen and (-webkit-min-device-pixel-ratio: 2) $mw20,screen and (min--moz-device-pixel-ratio: 2) $mw20";
		if ($mw15)
			$mhr[] = "screen and (-webkit-min-device-pixel-ratio: 1.5) $mw15,screen and (min--moz-device-pixel-ratio: 1.5) $mw15";
		if (count($mhr)){
			$mhr = implode(",",$mhr);
			$medias[$w."hr"] = "@media $mhr{html:not(.slow):not(.avp800) b.$cid,html:not(.slow):not(.avp800) b.$cid:after{background-image:url($file) !important;}}";
		}
		$prev_width = $w+1;
	}
	// Media Queries
	$style .= implode("",$medias);

	$out = "<!--[if IE]>$img<![endif]-->\n";
	$img = inserer_attribut($img,"src",$fallback_file);
	$img = inserer_attribut($img,"class","respim $class");
	$img = inserer_attribut($img,"onmousedown","var i=window.getComputedStyle(this.parentNode).backgroundImage.replace(/\W?\)$/,'').replace(/^url\(\W?|/,'');this.src=(i&&i!='none'?i:this.src);");
	$out .= "<!--[if !IE]--><b class=\"respwrapper $cid $extension\">$img</b>\n<style>$style</style><!--[endif]-->";

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
	if (strpos($img,"respim")!==false)
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

	$parts = pathinfo($src);
	$extension = $parts['extension'];

	// calculer les variantes d'image sur les breakpoints
	$large = "";
	foreach($bkpt as $wk){
		if ($wk>$w) break;
		$i = image_reduire($img,$wk,10000);
		$large = $images[$wk] = extraire_attribut($i,"src");
	}

	// l'image de fallback en jpg tres compresse
	if (function_exists("image_aplatir")){
		// image de fallback : la plus grande en jpg tres compresse
		$fallback = image_aplatir($large,'jpg',_RESPIM_LOWSRC_JPG_BG_COLOR,_RESPIM_LOWSRC_JPG_QUALITY);
		$images["fallback"] = extraire_attribut($fallback,"src");
	}

	// generer le markup
	return respim_markup($img,$images,$w,$h,$extension);
}

/**
 * Traiter les images de la page et les passer en responsive
 * si besoin
 *
 * @param $texte
 * @return mixed
 */
function respim_affichage_final($texte){
	$respim_ins = false;
	if ($GLOBALS['html']){
		#spip_timer();
		$replace = array();
		preg_match_all(",<img\s[^>]*>,Uims",$texte,$matches,PREG_SET_ORDER);
		if (count($matches)){
			foreach($matches as $m){
				$ri = respim_image($m[0]);
				if ($ri!==$m[0]){
					$replace[$m[0]] = $ri;
				}
			}
			if (count($replace)){
				$respim_ins = true;
				$texte = str_replace(array_keys($replace),array_values($replace),$texte);
			}
		}
		if ($respim_ins OR strpos($texte,"respwrapper")!==false){
			// les styles communs a toutes les images responsive en cours de chargement
			$ins = "<style type='text/css'>"."img.respim{opacity:0.70;max-width:100%;height:auto;}"
			."b.respwrapper{display:inline-block;max-width:100%;position:relative;background-size:100%;background-repeat:no-repeat;}"
			."b.respwrapper:after{position:absolute;top:0;left:0;right:0;bottom:0;background-size:100%;background-repeat:no-repeat;display:inline-block;max-width:100%;content:\"\"}"
			."</style>\n";
			// le script qui estime si la rapidite de connexion et pose une class slow sur <html> si connexion lente
			// et est appele post-chargement pour finir le rendu (rend les images enregistrables par clic-droit aussi)
			$async_style = "html img.respim{opacity:0.01}html b.respwrapper:after{display:none;}";
			$length = strlen($texte)+1900; // ~1500 pour le JS qu'on va inserer
			$ins .= "<script type='text/javascript'>/*<![CDATA[*/"
				."function hAC(c){(function(H){H.className=H.className+' '+c})(document.documentElement)}"
				// Android 2 media-queries bad support workaround
				// 1/ viewport 800px is first rendered, then, after ~1s real viewport : put .avp800 on html to avoid viewport 800px loading during first second
				// 2/ muliple rules = multiples downloads : put .ahrdpi on html to avoid lowres image loading if dpi>=1.5
				."var android2 = (screen.width==800) && (/android 2[.]/i.test(navigator.userAgent.toLowerCase()));"
				."if (android2) {"
				."hAC('avp800');setTimeout(function(){(function(H){H.className=H.className.replace(/\bavp800\b/,'')})(document.documentElement)},1000);"
				."if(window.devicePixelRatio !== undefined && window.devicePixelRatio>=1.5) hAC('ahrdpi');"
				."}\n"
				// slowConnection detection
				."var slowConnection = false;"
				."if (typeof window.performance!==\"undefined\"){"
				."var perfData = window.performance.timing;"
				."var speed = ~~($length/(perfData.responseEnd - perfData.connectStart));" // approx, *1000/1024 to be exact
				//."console.log(speed);"
				."slowConnection = (speed && speed<50);" // speed n'est pas seulement une bande passante car prend en compte la latence de connexion initiale
				."}else{"
				//https://github.com/Modernizr/Modernizr/blob/master/feature-detects/network/connection.js
				."var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;"
				."if (typeof connection!==\"undefined\") slowConnection = (connection.type == 3 || connection.type == 4 || /^[23]g$/.test(connection.type));"
				."}"
				//."console.log(slowConnection);"
				."if(slowConnection) hAC('slow');\n"
				// injecter un style async apres chargement des images
			  // pour masquer les couches superieures (fallback et chargement)
				."var respim_onload = function(){"
			  ."var sa = document.createElement('style'); sa.type = 'text/css';"
			  ."sa.innerHTML = '$async_style';"
			  ."var s = document.getElementsByTagName('style')[0]; s.parentNode.insertBefore(sa, s);};"
				."if (typeof jQuery!=='undefined') jQuery(function(){jQuery(window).load(respim_onload)}); else window.onload=respim_onload;/*]]>*/</script>\n";
			// le noscript alternatif si pas de js pour desactiver le rendu progressif qui ne rend pas bien les PNG transparents
			if (!_RESPIM_NOJS_PNGGIF_PROGRESSIVE_RENDERING)
				$ins .= "<noscript><style type='text/css'>.png img.respim,.gif img.respim{opacity:0.01}b.respwrapper.png:after,b.respwrapper.gif:after{display:none;}</style></noscript>";
			// inserer avant le premier <script> ou <link a defaut

			// regrouper tous les styles respim dans le head
			preg_match_all(",<!--\[if !IE\]-->.*(<style[^>]*>.*</style>).*<!--\[endif\]-->,Ums",$texte,$matches);
			if (count($matches[1])){
				$texte = str_replace($matches[1],"",$texte);
				$ins .= implode("\n",$matches[1]);
			}
			if ($p = strpos($texte,"<link") OR $p = strpos($texte,"<script") OR $p = strpos($texte,"</head"))
				$texte = substr_replace($texte,"<!--[if !IE]-->$ins\n<!--[endif]-->\n",$p,0);
		}
		#var_dump(spip_timer());
	}
	return $texte;
}

?>
