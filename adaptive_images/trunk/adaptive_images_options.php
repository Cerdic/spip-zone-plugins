<?php
/**
 * Options du plugin Adaptive Images
 *
 * @plugin     Adaptive Images
 * @copyright  2013
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Adaptive_Images\Options
 */


if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING')) define('_ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING',false);
if (!defined('_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR')) define('_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR','ffffff');
if (!defined('_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY')) define('_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY',10);

// qualite de compression JPG pour les images 1.5x et 2x (on peut comprimer plus)
if (!defined('_ADAPTIVE_IMAGES_15x_JPG_QUALITY')) define('_ADAPTIVE_IMAGES_15x_JPG_QUALITY',65);
if (!defined('_ADAPTIVE_IMAGES_20x_JPG_QUALITY')) define('_ADAPTIVE_IMAGES_20x_JPG_QUALITY',45);

// Breakpoints de taille d'ecran pour lesquels on generent des images
if (!defined('_ADAPTIVE_IMAGES_DEFAULT_BKPTS')) define('_ADAPTIVE_IMAGES_DEFAULT_BKPTS','160,320,480,640,960,1440');
// les images 1x sont au maximum en _ADAPTIVE_IMAGES_MAX_WIDTH_1x px de large dans la page
if (!defined('_ADAPTIVE_IMAGES_MAX_WIDTH_1x')) define('_ADAPTIVE_IMAGES_MAX_WIDTH_1x',640);
// on ne traite pas les images de largeur inferieure a _ADAPTIVE_IMAGES_MIN_WIDTH_1x px
if (!defined('_ADAPTIVE_IMAGES_MIN_WIDTH_1x')) define('_ADAPTIVE_IMAGES_MIN_WIDTH_1x',320);

/**
 *
 * @param string $img
 * @param array $rwd_images
 *   tableau
 *     width => file
 * @param int $width
 * @param int $height
 * @param string $extension
 * @param int $max_width_1x
 * @return string
 */
function adaptive_images_markup($img, $rwd_images, $width, $height, $extension, $max_width_1x=_ADAPTIVE_IMAGES_MAX_WIDTH_1x){
	$class = extraire_attribut($img,"class");
	if (strpos($class,"adaptimg")!==false) return $img;
	ksort($rwd_images);
	$cid = "c".crc32(serialize($rwd_images));
	$style = "";
	if ($class) $class = " $class";
	$class = "$cid$class";
	$img = inserer_attribut($img,"class","adaptimg-fallback $class");

	// image de fallback fournie ?
	if (isset($rwd_images['fallback'])){
		$fallback_file = $rwd_images['fallback'];
		unset($rwd_images['fallback']);
	}
	// sinon on affiche la plus petite image
	if (!$fallback_file){
		$fallback_file = reset($rwd_images);
		$fallback_file = $fallback_file['10x'];
	}
	// embarquer le fallback en DATA URI si moins de 32ko (eviter une page trop grosse)
	$fallback_file = filtre_embarque_fichier($fallback_file,"",32000);

	$prev_width = 0;
	$medias = array();
	$lastw = array_keys($rwd_images);
	$lastw = end($lastw);
	foreach ($rwd_images as $w=>$files){
		if ($w==$lastw) {$islast = true;}
		// il faut utiliser une clause min-width and max-width pour que les regles soient exlusives
		// sinon on a multiple download sous android 2.x
		if ($prev_width<$max_width_1x){
			$hasmax = (($islast OR $w>=$max_width_1x)?false:true);
			$mw = ($prev_width?"and (min-width:{$prev_width}px)":"").($hasmax?" and (max-width:{$w}px)":"");
			$htmlsel = "html";
			if ($prev_width<=800 AND ($w>=800 OR $islast))
				$htmlsel.=":not(.avp800)";
			$htmlsel = array(
				'10x' => "$htmlsel:not(.ahrdpi)",
				'15x' => "$htmlsel:not(.slow)",
				'20x' => "$htmlsel:not(.slow)",
			);
		}
		$mwdpi = array(
			'10x' => "screen $mw",
			'15x' => "screen and (-webkit-min-device-pixel-ratio: 1.5) and (-webkit-max-device-pixel-ratio: 1.99) $mw,screen and (min--moz-device-pixel-ratio: 1.5) and (max--moz-device-pixel-ratio: 1.99) $mw",
			'20x' => "screen and (-webkit-min-device-pixel-ratio: 2) $mw,screen and (min--moz-device-pixel-ratio: 2) $mw",
		);
		foreach($files as $kx=>$file){
			if (isset($mwdpi[$kx])){
				// $file = "filedelay.api/5/$file"; // debug : injecter une tempo dans le chargement de l'image pour tester l'enrichissement progressif
				//$file = $file."?rwd"; // debug  : etre sur qu'on charge bien l'image issue des medias queries
				$mw = $mwdpi[$kx];
				$not = $htmlsel[$kx];
				$medias[$mw] = "@media $mw{{$not} b.$cid,{$not} b.$cid:after{background-image:url($file);}}";
			}
		}
		$prev_width = $w+1;
	}

	// Media Queries
	$style .= implode("",$medias);

	$out = "<!--[if IE]>$img<![endif]-->\n";
	$img = inserer_attribut($img,"src",$fallback_file);
	$img = inserer_attribut($img,"class","adaptimg $class");
	$img = inserer_attribut($img,"onmousedown","adaptimgFix(this)");
	$out .= "<!--[if !IE]--><b class=\"adaptimg-wrapper $cid $extension\">$img</b>\n<style>$style</style><!--[endif]-->";

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
 * @param int $max_width_1x
 * @return string
 */
function adaptive_images_process_img($img, $bkpt = null, $max_width_1x=_ADAPTIVE_IMAGES_MAX_WIDTH_1x){
	if (!$img) return $img;
	if (strpos($img,"adaptimg")!==false)
		return $img;
	if (is_null($bkpt) OR !is_array($bkpt))
		$bkpt = explode(',',_ADAPTIVE_IMAGES_DEFAULT_BKPTS);

	if (!function_exists("taille_image"))
		include_spip("inc/filtres");
	if (!function_exists("image_reduire"))
		include_spip("inc/filtres_images_mini");
	if (!function_exists("image_aplatir"))
		include_spip("filtres/images_transforme");

	list($h,$w) = taille_image($img);
	if (!$w OR $w<=_ADAPTIVE_IMAGES_MIN_WIDTH_1x) return $img;

	$src = trim(extraire_attribut($img, 'src'));
	if (strlen($src) < 1){
		$src = $img;
		$img = "<img src='$src' />";
	}
	// on ne touche pas aux data:uri
	if (strncmp($src,"data:",5)==0)
		return $img;

	$images = array();
	if ($w<end($bkpt))
		$images[$w] = array(
			'10x'=>$src,
			'15x'=>$src,
			'20x'=>$src,
		);
	$src=preg_replace(',[?][0-9]+$,','',$src);

	// si on arrive pas a le lire, on ne fait rien
	if (!file_exists($src))
		return $img;

	$parts = pathinfo($src);
	$extension = $parts['extension'];

	// calculer les variantes d'image sur les breakpoints
	$large = "";
	$dpi = array('10x'=>1,'15x'=>1.5,'20x'=>2);
	foreach($bkpt as $wk){
		if ($wk>$w) break;
		foreach($dpi as $k=>$x){
			$wkx = intval(round($wk * $x));
			if ($wkx>$w)
				$images[$wk][$k] = $src;
			else {
				$i = image_reduire($img,$wkx,10000);
				if ($extension=='jpg' AND $k!='10x')
					$i = image_aplatir($i,'jpg',_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR,constant('_ADAPTIVE_IMAGES_'.$k.'_JPG_QUALITY'));
				$images[$wk][$k] = extraire_attribut($i,"src");
			}
		}
		if ($wk<=$max_width_1x) $large = $images[$wk]['10x'];
	}

	// l'image de fallback en jpg tres compresse
	if (function_exists("image_aplatir")){
		// image de fallback : la plus grande en jpg tres compresse
		// la qualite est reduite si la taille de l'image augmente, pour limiter le poids de l'image
		// regle de 3 au feeling, _ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY correspond a une image de 300kPx
		// et on varie dans +/-50% de _ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY
		$q = round(_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY-((min($max_width_1x,$w)*$h/$w*min($max_width_1x,$w))/100000-3));
		$q = min($q,round(_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY)*1.5);
		$q = max($q,round(_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY)*0.5);
		$fallback = image_aplatir($wk>$w&&$w<$max_width_1x?$images[$w]['10x']:$large,'jpg',_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR,$q);
		$images["fallback"] = extraire_attribut($fallback,"src");
	}

	// l'image est reduite a la taille maxi (version IE)
	$img = image_reduire($img,$max_width_1x,10000);
	// generer le markup
	return adaptive_images_markup($img,$images,$w, $h, $extension, $max_width_1x);
}

/**
 * Rendre les images d'un texte adaptatives, en permettant de preciser la largeur maxi a afficher en 1x
 * [(#TEXTE|adaptative_images{1024})]
 * @param string $texte
 * @param null|int $max_width_1x
 * @return mixed
 */
function adaptative_images($texte,$max_width_1x=_ADAPTIVE_IMAGES_MAX_WIDTH_1x){
	static $bkpts = array();
	if ($max_width_1x AND !isset($bkpts[$max_width_1x])){
		$b = explode(',',_ADAPTIVE_IMAGES_DEFAULT_BKPTS);
		while (count($b) AND end($b)>$max_width_1x) array_pop($b);
		// la largeur maxi affichee
		if (!count($b) OR end($b)<$max_width_1x) $b[] = $max_width_1x;
		$bkpts[$max_width_1x] = $b;
	}
	$bkpt = (isset($bkpts[$max_width_1x])?$bkpts[$max_width_1x]:null);

	$replace = array();
	preg_match_all(",<img\s[^>]*>,Uims",$texte,$matches,PREG_SET_ORDER);
	if (count($matches)){
		foreach($matches as $m){
			$ri = adaptive_images_process_img($m[0], $bkpt, $max_width_1x);
			if ($ri!==$m[0]){
				$replace[$m[0]] = $ri;
			}
		}
		if (count($replace)){
			$texte = str_replace(array_keys($replace),array_values($replace),$texte);
		}
	}

	return $texte;
}

/**
 * Traiter les images de la page et les passer en responsive
 * si besoin
 *
 * @param $texte
 * @return mixed
 */
function adaptive_images_affichage_final($texte){
	$adaptive_images_ins = false;
	if ($GLOBALS['html']){
		#spip_timer();
		$texte = adaptative_images($texte);
		if (strpos($texte,"adaptimg-wrapper")!==false){
			// les styles communs a toutes les images responsive en cours de chargement
			$ins = "<style type='text/css'>"."img.adaptimg{opacity:0.70;max-width:100%;height:auto;}"
			."b.adaptimg-wrapper,b.adaptimg-wrapper:after{display:inline-block;max-width:100%;position:relative;background-size:100%;background-repeat:no-repeat;}"
			."b.adaptimg-wrapper:after{position:absolute;top:0;left:0;right:0;bottom:0;content:\"\"}"
			."</style>\n";
			// le script qui estime si la rapidite de connexion et pose une class slow sur <html> si connexion lente
			// et est appele post-chargement pour finir le rendu (rend les images enregistrables par clic-droit aussi)
			$async_style = "html img.adaptimg{opacity:0.01}html b.adaptimg-wrapper:after{display:none;}";
			$length = strlen($texte)+1900; // ~1500 pour le JS qu'on va inserer
			$ins .= "<script type='text/javascript'>/*<![CDATA[*/"
				."function hAC(c){(function(H){H.className=H.className+' '+c})(document.documentElement)}"
				."function hRC(c){(function(H){H.className=H.className.replace(new RegExp('\\\\b'+c+'\\\\b'),'')})(document.documentElement)}"
				."function adaptimgFix(n){var i=window.getComputedStyle(n.parentNode).backgroundImage.replace(/\W?\)$/,'').replace(/^url\(\W?|/,'');n.src=(i&&i!='none'?i:n.src);}"
				// Android 2 media-queries bad support workaround
				// 1/ viewport 800px is first rendered, then, after ~1s real viewport : put .avp800 on html to avoid viewport 800px loading during first second
				// 2/ muliple rules = multiples downloads : put .ahrdpi on html to avoid lowres image loading if dpi>=1.5
				."var android2 = (screen.width==800) && (/android 2[.]/i.test(navigator.userAgent.toLowerCase()));"
				."if (android2) {"
				."hAC('avp800');setTimeout(function(){hRC('avp800')},1000);"
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
				."if(slowConnection) {hAC('slow');hRC('ahrdpi');}\n"
				// injecter un style async apres chargement des images
			  // pour masquer les couches superieures (fallback et chargement)
				."var adaptimg_onload = function(){"
			  ."var sa = document.createElement('style'); sa.type = 'text/css';"
			  ."sa.innerHTML = '$async_style';"
			  ."var s = document.getElementsByTagName('style')[0]; s.parentNode.insertBefore(sa, s);};"
				."if (typeof jQuery!=='undefined') jQuery(function(){jQuery(window).load(adaptimg_onload)}); else window.onload=adaptimg_onload;/*]]>*/</script>\n";
			// le noscript alternatif si pas de js pour desactiver le rendu progressif qui ne rend pas bien les PNG transparents
			if (!_ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING)
				$ins .= "<noscript><style type='text/css'>.png img.adaptimg,.gif img.adaptimg{opacity:0.01}b.adaptimg-wrapper.png:after,b.adaptimg-wrapper.gif:after{display:none;}</style></noscript>";
			// inserer avant le premier <script> ou <link a defaut

			// regrouper tous les styles adaptimg dans le head
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
