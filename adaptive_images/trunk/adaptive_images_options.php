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

// Pour les ecrans plus petits, c'est la version mobile qui est fournie (recadree)
if (!defined('_ADAPTIVE_IMAGES_MAX_WIDTH_MOBILE_VERSION')) define('_ADAPTIVE_IMAGES_MAX_WIDTH_MOBILE_VERSION',320);

// Pour generer chaque variante d'image uniquement quand elle est demandee pour la premiere fois
// par defaut false : on genere toutes les images au calcul de la page (mais timeout possible)
// pour passer a true : ajouter la rewrite rule suivante dans .htaccess
/*
###
# Adaptive Images

RewriteRule \badapt-img/(\d+/\d\dx/.*)$ spip.php?action=adapt_img&arg=$1 [QSA,L]

# Fin des Adaptive Images
###
*/
if (!defined('_ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION')) define('_ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION',false);


/**
 * ?action=adapt_img
 * Production d'une image a la volee a partir de son URL
 * arg
 *   local/adapt-img/w/x/file
 *   ex : 320/20x/file
 *   w est la largeur affichee de l'image
 *   x est la resolution (10x => 1, 15x => 1.5, 20x => 2)
 *   file le chemin vers le fichier source
 */
function action_adapt_img_dist(){

	$arg = _request('arg');
	$mime = "";

	$file = adaptive_images_bkpt_image_from_path($arg, $mime);
	if (!$file
	  OR !$mime){
		http_status(404);
		include_spip('inc/minipres');
		echo minipres(_T('erreur').' 404',
			_T('medias:info_document_indisponible'),"",true);
		die();
	}

	header("Content-Type: ". $mime);
	#header("Expires: 3600"); // set expiration time

	if ($cl = filesize($file))
		header("Content-Length: ". $cl);

	readfile($file);
	exit;
}


/** Filtre  ***********************************************************************************************************/


/**
 * Rendre les images d'un texte adaptatives, en permettant de preciser la largeur maxi a afficher en 1x
 * [(#TEXTE|adaptive_images{1024})]
 * @param string $texte
 * @param null|int $max_width_1x
 * @return mixed
 */
function adaptive_images($texte,$max_width_1x=_ADAPTIVE_IMAGES_MAX_WIDTH_1x){
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
 * nommage alternatif
 * @param $texte
 * @param int $max_width_1x
 * @return mixed
 */
function adaptative_images($texte,$max_width_1x=_ADAPTIVE_IMAGES_MAX_WIDTH_1x){
	return adaptive_images($texte,$max_width_1x);
}

/** Pipelines  ********************************************************************************************************/

/**
 * Completer la page d'edition d'un document
 * pour joindre sous-titrage, audio-desc et transcript sur les videos
 *
 * @param array $flux
 * @return array
 */
function adaptive_images_affiche_milieu($flux){
	if (in_array($flux['args']['exec'],array('document_edit','documents_edit'))
	  AND $id_document=$flux['args']['id_document']){
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/adapter-image',array('id_document'=>$id_document));
	}
	return $flux;
}

/**
 * Injecter data-src-mobile="..." sur les modeles img/doc/emb quand une variante mobile existe
 *
 * @param array $flux
 * @return array
 */
function adaptive_images_recuperer_fond($flux){
	if (
		strncmp($flux['args']['fond'],"modeles/img",11)==0
		OR strncmp($flux['args']['fond'],"modeles/doc",11)==0
		OR strncmp($flux['args']['fond'],"modeles/emb",11)==0){
		if ($id_document = intval($flux['args']['contexte']['id_document'])){
			if ($mobileview = adaptive_images_variante($id_document,"mobileview")){
				$src_mobile = get_spip_doc($mobileview['fichier']);
				$imgs = extraire_balises($flux['data']['texte'],'img');
				foreach($imgs as $img){
					$src = extraire_attribut($img,"src");
					$src = set_spip_doc($src);
					if (sql_getfetsel("id_document","spip_documents","id_document=".intval($id_document)." AND fichier=".sql_quote($src))){
						$img2 = inserer_attribut($img,"data-src-mobile",$src_mobile);
						$flux['data']['texte'] = str_replace($img,$img2,$flux['data']['texte']);
					}
				}
			}
		}
	}
	return $flux;
}


/**
 * Trouver la variante $mode d'une image
 * @param int $id_document
 * @param string $mode
 *   mobileview
 * @return array
 */
function adaptive_images_variante($id_document,$mode){
	return sql_fetsel('*',
		'spip_documents as d JOIN spip_documents_liens as L on L.id_document=d.id_document',
		"L.objet='document' AND L.id_objet=".intval($id_document)." AND d.mode=".sql_quote($mode));
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
		if (strpos($texte,"adapt-img-wrapper")!==false){
			// les styles communs a toutes les images responsive en cours de chargement
			$ins = "<style type='text/css'>"."img.adapt-img{opacity:0.70;max-width:100%;height:auto;}"
			."span.adapt-img-wrapper,span.adapt-img-wrapper:after{display:inline-block;max-width:100%;position:relative;-webkit-background-size:100% auto;background-size:100% auto;background-repeat:no-repeat;line-height:1px;}"
			."span.adapt-img-wrapper:after{position:absolute;top:0;left:0;right:0;bottom:0;content:\"\"}"
			."</style>\n";
			// le script qui estime si la rapidite de connexion et pose une class aislow sur <html> si connexion lente
			// et est appele post-chargement pour finir le rendu (rend les images enregistrables par clic-droit aussi)
			$async_style = "html img.adapt-img{opacity:0.01}html span.adapt-img-wrapper:after{display:none;}";
			$length = strlen($texte)+1900; // ~1500 pour le JS qu'on va inserer
			$ins .= "<script type='text/javascript'>/*<![CDATA[*/"
				."function adaptImgFix(n){var i=window.getComputedStyle(n.parentNode).backgroundImage.replace(/\W?\)$/,'').replace(/^url\(\W?|/,'');n.src=(i&&i!='none'?i:n.src);}"
				."(function(){function hAC(c){(function(H){H.className=H.className+' '+c})(document.documentElement)}"
				."function hRC(c){(function(H){H.className=H.className.replace(new RegExp('\\\\b'+c+'\\\\b'),'')})(document.documentElement)}"
				// Android 2 media-queries bad support workaround
				// 1/ viewport 800px is first rendered, then, after ~1s real viewport : put .avp800 on html to avoid viewport 800px loading during first second
				// 2/ muliple rules = multiples downloads : put .aihrdpi on html to avoid lowres image loading if dpi>=1.5
				."var android2 = (screen.width==800) && (/android 2[.]/i.test(navigator.userAgent.toLowerCase()));"
				."if (android2) {"
				."hAC('avp800');setTimeout(function(){hRC('avp800')},1000);"
				."if(window.devicePixelRatio !== undefined && window.devicePixelRatio>=1.5) hAC('aihrdpi');"
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
				."if(slowConnection) {hAC('aislow');hRC('aihrdpi');}\n"
				// injecter un style async apres chargement des images
			  // pour masquer les couches superieures (fallback et chargement)
				."var adaptImg_onload = function(){"
			  ."var sa = document.createElement('style'); sa.type = 'text/css';"
			  ."sa.innerHTML = '$async_style';"
			  ."var s = document.getElementsByTagName('style')[0]; s.parentNode.insertBefore(sa, s);};"
				// http://www.webreference.com/programming/javascript/onloads/index.html
				."function addLoadEvent(func){var oldol=window.onload;if (typeof oldol != 'function'){window.onload=func;}else{window.onload=function(){if (oldol){oldol();} func();}}}"
				."if (typeof jQuery!=='undefined') jQuery(function(){jQuery(window).load(adaptImg_onload)}); else addLoadEvent(adaptImg_onload);"
			  ."})();/*]]>*/</script>\n";
			// le noscript alternatif si pas de js pour desactiver le rendu progressif qui ne rend pas bien les PNG transparents
			if (!_ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING)
				$ins .= "<noscript><style type='text/css'>.png img.adapt-img,.gif img.adapt-img{opacity:0.01}span.adapt-img-wrapper.png:after,span.adapt-img-wrapper.gif:after{display:none;}</style></noscript>";
			// inserer avant le premier <script> ou <link a defaut

			// regrouper tous les styles adapt-img dans le head
			preg_match_all(",<!--\[if !IE\]><!-->.*(<style[^>]*>.*</style>).*<!--<!\[endif\]-->,Ums",$texte,$matches);
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


/** Production des images  ********************************************************************************************/

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
	if (strpos($class,"adapt-img")!==false) return $img;
	ksort($rwd_images);
	$cid = "c".crc32(serialize($rwd_images));
	$style = "";
	if ($class) $class = " $class";
	$class = "$cid$class";
	$img = inserer_attribut($img,"class","adapt-img-ie $class");

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
				'10x' => "$htmlsel:not(.aihrdpi)",
				'15x' => "$htmlsel:not(.aislow)",
				'20x' => "$htmlsel:not(.aislow)",
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
				$medias[$mw] = "@media $mw{{$not} span.$cid,{$not} span.$cid:after{background-image:url($file);}}";
			}
		}
		$prev_width = $w+1;
	}

	// Media Queries
	$style .= implode("",$medias);

	$out = "<!--[if IE]>$img<![endif]-->\n";
	$img = inserer_attribut($img,"src",$fallback_file);
	$img = inserer_attribut($img,"class","adapt-img $class");
	$img = inserer_attribut($img,"onmousedown","adaptImgFix(this)");
	// $img = inserer_attribut($img,"onkeydown","adaptImgFix(this)"); // usefull ?
	$out .= "<!--[if !IE]><!--><span class=\"adapt-img-wrapper $cid $extension\">$img</span>\n<style>$style</style><!--<![endif]-->";

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
	if (strpos($img,"adapt-img")!==false)
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
	$src_mobile = extraire_attribut($img, 'data-src-mobile');

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

	// on ne touche pas aux GIF animes !
	if ($extension=="gif" AND adaptive_images_is_animated_gif($src))
		return $img;

	// calculer les variantes d'image sur les breakpoints
	$fallback = $src;
	$wfallback = $w;
	$dpi = array('10x'=>1,'15x'=>1.5,'20x'=>2);
	foreach($bkpt as $wk){
		if ($wk>$w) break;
		$is_mobile = (($src_mobile AND $wk<=_ADAPTIVE_IMAGES_MAX_WIDTH_MOBILE_VERSION)?true:false);
		foreach($dpi as $k=>$x){
			$wkx = intval(round($wk * $x));
			if ($wkx>$w)
				$images[$wk][$k] = $src;
			else {
				$images[$wk][$k] = adaptive_images_bkpt_image($is_mobile?$src_mobile:$src,$wk,$wkx,$k,$extension);
			}
		}
		if ($wk<=$max_width_1x AND ($is_mobile OR !$src_mobile)) {
			$fallback = $images[$wk]['10x'];
			$wfallback = $wk;
		}
	}

	// l'image de fallback en jpg tres compresse
	if (function_exists("image_aplatir")){
		// image de fallback : la plus grande en jpg tres compresse ou la version mobile en jpg tres compresse
		if ($wk>$w&&$w<$max_width_1x){
			$fallback = $images[$w]['10x'];
			$wfallback = $w;
		}

		// l'image n'a peut etre pas ete produite car _ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION = true
		// on la genere immediatement car on en a besoin
		if (!file_exists($fallback)){
			$mime = "";
			adaptive_images_bkpt_image_from_path($fallback, $mime);
		}
		// la qualite est reduite si la taille de l'image augmente, pour limiter le poids de l'image
		// regle de 3 au feeling, _ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY correspond a une image de 450kPx
		// et on varie dans +/-50% de _ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY
		$q = round(_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY-((min($max_width_1x,$wfallback)*$h/$w*min($max_width_1x,$wfallback))/75000-6));
		$q = min($q,round(_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY)*1.5);
		$q = max($q,round(_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY)*0.5);
		$fallback = image_aplatir($fallback,'jpg',_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR,$q);
		$images["fallback"] = extraire_attribut($fallback,"src");
	}

	// l'image est reduite a la taille maxi (version IE)
	$img = image_reduire($img,$max_width_1x,10000);
	// generer le markup
	return adaptive_images_markup($img,$images,$w, $h, $extension, $max_width_1x);
}

/**
 * Preparer une image pour un breakpoint/resolution
 * @param string $src
 *   image source
 * @param int $wkpt
 *   largeur du breakpoint (largeur d'affichage) pour lequel l'image est produite
 * @param int $wx
 *   largeur en px de l'image reelle
 * @param string $x
 *   resolution 10x 15x 20x
 * @param string $extension
 *   extenstion
 * @param bool $force
 *   produire l'image immediatement si elle n'existe pas ou est trop vieille
 * @return string
 *   nom du fichier resultat
 */
function adaptive_images_bkpt_image($src, $wkpt, $wx, $x, $extension, $force=false){
	$dest = _DIR_VAR."adapt-img/$wkpt/$x/$src";
	if (($exist=file_exists($dest)) AND filemtime($dest)>=filemtime($src))
		return $dest;

	$force = ($force?true:!_ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION);

	// si le fichier existe mais trop vieux et que l'on ne veut pas le produire immediatement : supprimer le vieux fichier
	// ainsi le hit passera par la regexp et tommbera sur l'action adapt_img qui le produira
	if ($exist AND !$force)
		@unlink($dest);

	if (!$force)
		return $dest;

	// creer l'arbo
	$dirs = explode("/",$dest);
	$d = "";
	while(count($dirs)>1
		AND (
		  is_dir($f="$d".($sd=array_shift($dirs)))
		  OR
		  $f = sous_repertoire($d,$sd)
		)
	) $d = $f;

	$i = image_reduire($src,$wx,10000);

	if (in_array($extension,array('jpg','jpeg')) AND $x!='10x')
		$i = image_aplatir($i,'jpg',_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR,constant('_ADAPTIVE_IMAGES_'.$x.'_JPG_QUALITY'));
	$i = extraire_attribut($i,"src");
	@copy($i,$dest);

	return file_exists($dest)?$dest:$src;
}

/**
 * Produire une image d'apres son URL
 * utilise par ?action=adapt_img pour la premiere production a la volee
 * ou depuis adaptive_images_process_img() si on a besoin de l'image tout de suite
 *
 * @param string $arg
 * @param string $mime
 * @return string
 */
function adaptive_images_bkpt_image_from_path($arg,&$mime){
	$base = _DIR_VAR."adapt-img/";
	if (strncmp($arg,$base,strlen($base))==0)
		$arg = substr($arg,strlen($base));

	$arg = explode("/",$arg);
	$wkpt = intval(array_shift($arg));
	$x = array_shift($arg);
	$src = implode("/",$arg);

	$parts = pathinfo($src);
	$extension = strtolower($parts['extension']);
	include_spip("base/typedoc");
	if ($extension=="jpeg") $extension = "jpg";
	$mime = (isset($GLOBALS['tables_mime'][$extension])?$GLOBALS['tables_mime'][$extension]:'');
	$dpi = array('10x'=>1,'15x'=>1.5,'20x'=>2);

	if (!$wkpt
	  OR !isset($dpi[$x])
	  OR !file_exists($src)
	  OR !$mime){
		return "";
	}
	$wx = intval(round($wkpt * $dpi[$x]));

	if (!function_exists("taille_image"))
		include_spip("inc/filtres");
	if (!function_exists("image_reduire"))
		include_spip("inc/filtres_images_mini");
	if (!function_exists("image_aplatir"))
		include_spip("filtres/images_transforme");

	$file = adaptive_images_bkpt_image($src, $wkpt, $wx, $x, $extension, true);
	return $file;
}

/**
 * Detecter un GIF anime
 * http://it.php.net/manual/en/function.imagecreatefromgif.php#59787
 *
 * @param string $filename
 * @return bool
 */
function adaptive_images_is_animated_gif($filename){
	$filecontents = file_get_contents($filename);

	$str_loc = 0;
	$count = 0;
	while ($count<2) # There is no point in continuing after we find a 2nd frame
	{

		$where1 = strpos($filecontents, "\x00\x21\xF9\x04", $str_loc);
		if ($where1===FALSE){
			break;
		} else {
			$str_loc = $where1+1;
			$where2 = strpos($filecontents, "\x00\x2C", $str_loc);
			if ($where2===FALSE){
				break;
			} else {
				if ($where1+8==$where2){
					$count++;
				}
				$str_loc = $where2+1;
			}
		}
	}

	if ($count>1){
		return (true);

	} else {
		return (false);
	}
}
?>
