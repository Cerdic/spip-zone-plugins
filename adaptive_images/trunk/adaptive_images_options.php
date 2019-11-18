<?php
/**
 * Options du plugin Adaptive Images
 *
 * @plugin     Adaptive Images
 * @copyright  2013-2019
 * @author     Cedric
 * @licence    GNU/GPL
 * @package    SPIP\Adaptive_Images\Options
 */


if (!defined('_ECRIRE_INC_VERSION')) return;

include_once(_DIR_PLUGIN_ADAPTIVE_IMAGES."lib/AdaptiveImages/AdaptiveImages.php");
class SPIPAdaptiveImages extends AdaptiveImages {
	protected function URL2filepath($url){
		$url = parent::URL2filepath($url);
		// URL absolue en URL relative
		if (preg_match(",^https?://,",$url)){
			$base = url_de_base();
			if (strncmp($url,$base,strlen($base))==0)
				$url = _DIR_RACINE . substr($url,strlen($base));
			elseif (defined('_ADAPTIVE_IMAGES_DOMAIN')
			  AND strncmp($url,_ADAPTIVE_IMAGES_DOMAIN,strlen(_ADAPTIVE_IMAGES_DOMAIN))==0){
				$url = _DIR_RACINE . substr($url,strlen(_ADAPTIVE_IMAGES_DOMAIN));
			}
		}
		return $url;
	}

	protected function filepath2URL($filepath, $relative=false){
		$filepath = parent::filepath2URL($filepath, $relative);
		if (!$relative AND defined('_ADAPTIVE_IMAGES_DOMAIN')){
			$filepath = rtrim(_ADAPTIVE_IMAGES_DOMAIN,"/")."/".$filepath;
		}
		return $filepath;
	}
}
$AdaptiveImages = SPIPAdaptiveImages::getInstance();


// utiliser le progressive rendering sur PNG et GIF si pas de JS
if (defined('_ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING'))
	$AdaptiveImages->nojsPngGifProgressiveRendering = _ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING;

$settings = (isset($GLOBALS['meta']['adaptive_images'])?unserialize($GLOBALS['meta']['adaptive_images']):false);
if ($settings){
	// couleur de background pour les images lowsrc
	if (isset($settings['lowsrc_jpg_bg_color']) AND $settings['lowsrc_jpg_bg_color'])
		$AdaptiveImages->lowsrcJpgBgColor = $settings['lowsrc_jpg_bg_color'];
	// qualite de compression JPG pour les images 1x
	if (isset($settings['10x_jpg_quality']) AND $q=intval($settings['10x_jpg_quality']))
		$AdaptiveImages->x10JpgQuality = $q;
	// qualite de compression JPG pour les images 1.5x
	if (isset($settings['15x_jpg_quality']) AND $q=intval($settings['15x_jpg_quality']))
		$AdaptiveImages->x15JpgQuality = $q;
	// qualite de compression JPG pour les images 2x
	if (isset($settings['20x_jpg_quality']) AND $q=intval($settings['20x_jpg_quality']))
		$AdaptiveImages->x20JpgQuality = $q;
	// Breakpoints de taille d'ecran pour lesquels on generent des images
	if (isset($settings['default_bkpts']) AND $settings['default_bkpts'])
		$AdaptiveImages->defaultBkpts = explode(",",$settings['default_bkpts']);
	// les images 1x sont au maximum en max_width_1x px de large dans la page
	if (isset($settings['max_width_1x']) AND $v=intval($settings['max_width_1x']))
		$AdaptiveImages->maxWidth1x = $v;
	// Pour les ecrans plus petits, c'est la version mobile qui est fournie (recadree)
	if (isset($settings['max_width_mobile_version']) AND $v=intval($settings['max_width_mobile_version']))
		$AdaptiveImages->maxWidthMobileVersion = $v;


	// qualite des images lowsrc
	if (isset($settings['lowsrc_jpg_quality']) AND $q=intval($settings['lowsrc_jpg_quality']))
		$AdaptiveImages->lowsrcJpgQuality= $q;
	if (isset($settings['lowsrc_width']) AND $v=intval($settings['lowsrc_width'])){
		$AdaptiveImages->lowsrcWidth = $v;
	}
	else {
		// fine tuning automatique si pas de config (up depuis une version existante) :
		// on genere une miniature toute petite en ameliorant un peu la qualite de sortie,
		// ce qui donne une taille beaucoup plus reduite
		$AdaptiveImages->lowsrcWidth = 128;
		$AdaptiveImages->lowsrcJpgQuality = min($AdaptiveImages->lowsrcJpgQuality + 30, 90);
	}

	// on ne traite pas les images de largeur inferieure a min_width_1x px
	if (isset($settings['min_width_1x']) AND $v=intval($settings['min_width_1x']))
		$AdaptiveImages->minWidth1x = $v;
	// on ne traite pas les images de poids inferieur a min_filesize ko
	if (isset($settings['min_filesize']) AND $v=intval($settings['min_filesize']))
		$AdaptiveImages->minFileSize = $v*1024;

	// Experimental
	if (isset($settings['markup_method']) and $settings['markup_method'] === 'srcset') {
		$AdaptiveImages->markupMethod = "srcset";
	}

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
	if (isset($settings['on_demand_production']) AND $settings['on_demand_production'])
		$AdaptiveImages->onDemandImages = true;

	if (isset($settings['lazy_load']) AND $settings['lazy_load'])
		$AdaptiveImages->lazyload = true;

	if (isset($settings['thumbnail_method'])
		and function_exists($f = "adaptive_images_preview_" . $settings['thumbnail_method'])) {
		$AdaptiveImages->thumbnailGeneratorCallback = $f;
	}

	if (isset($settings['thumbnail_debug']) and intval($settings['thumbnail_debug'])) {
		define('_ADAPTIVE_IMAGES_DEBUG_PREVIEW', true);
	}
}



// GD memory limit
if (defined('_IMG_GD_MAX_PIXELS'))
	$AdaptiveImages->maxImagePxGDMemoryLimit = intval(_IMG_GD_MAX_PIXELS);

// dossier de stockage des images adaptatives
$AdaptiveImages->destDirectory = _DIR_VAR . "adapt-img/";



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

	$AdaptiveImages = SPIPAdaptiveImages::getInstance();
	try {
		$AdaptiveImages->deliverBkptImage(_request('arg'));
	}
	catch (Exception $e){
		http_status(404);
		include_spip('inc/minipres');
		echo minipres(_T('erreur').' 404',
			_T('medias:info_document_indisponible')."<br />".$e->getMessage(),"",true);
	}
	exit;
}

/**
 * Convertir un tableau de taille ecran, taille image en media queries
 * #ARRAY{768px,100vw,900px,50vw,*,33vw} => (max-width: 768px) 100vw, (max-width: 900px) 50vw, 33vw
 *
 * @param array $sizes
 * @return array
 */
function adaptive_images_convert_sizes_array($sizes) {
	$mq = array();
	foreach ($sizes as $screen_width => $image_width) {
		if (is_numeric($image_width)) {
			$image_width .= "px";
		}
		if ($screen_width === '*') {
			$mq[] = $image_width;
		}
		else {
			if (is_numeric($screen_width)) {
				$screen_width .= "px";
			}
			$mq[] = "(max-width: {$screen_width}) {$image_width}";
		}
	}
	return $mq;
}

/**
 * Fonction de base pour les filtres, ne pas utiliser directement
 * @protected
 *
 * @param $texte
 * @param $max_width_1x
 * @param string|array $sizes
 * @param bool $background_only
 * @return mixed|string
 */
function adaptive_images_base($texte, $max_width_1x, $sizes=null, $background_only = false){
	$bkpt = null;
	// plusieurs valeurs separees par un / : ce sont les breakpoints, max_width_1x est la derniere valeur
	if (strpos($max_width_1x,"/")!==false){
		$bkpt = explode("/",$max_width_1x);
		$bkpt = array_map("intval",$bkpt);
		$max_width_1x = end($bkpt);
		if (!$max_width_1x){
			$max_width_1x = null;
			$bkpt = null;
		}
	}
	// on peut passer une valeur chaine genre '-' ou '*' si on veut rien preciser mais fournir l'argument size suivant
	if ($max_width_1x and !intval($max_width_1x)) {
		$max_width_1x = null;
	}
	// preparer le tableau de media queries pour les sizes si besoin
	if (is_array($sizes)) {
		$sizes = adaptive_images_convert_sizes_array($sizes);
	}

	$AdaptiveImages = SPIPAdaptiveImages::getInstance();
	try {
		$res = $AdaptiveImages->adaptHTMLPart($texte, $max_width_1x, $bkpt, $background_only ? true : $sizes);
	}
	catch (Exception $e) {
		erreur_squelette($e->getMessage(),'SPIPAdaptiveImages:adaptHTMLPart');
		return $texte;
	}

	if (!$background_only) {
		// injecter la class filtre_inactif sur les balises img pour ne pas repasser un filtre image dessus
		$imgs = extraire_balises($res, 'img');
		foreach ($imgs as $img) {
			$class = extraire_attribut($img, "class");
			if (strpos($class, 'filtre_inactif') !== false) {
				$class = str_replace('adapt-img', 'no_image_filtrer filtre_inactif adapt-img', $class);
				$img2 = inserer_attribut($img, 'class', $class);
				if (strlen($img2) !== strlen($img)) {
					$res = str_replace($img, $img2, $res);
				}
			}
		}
	}
	return $res;
}

function adaptive_images_preview_gradients($image, $options) {
	$gradients = charger_fonction("image_gradients", "preview");
	spip_timer($m = 'GRADIENTS');
	if ($thumbnail = $gradients($image, $options)) {
		spip_log("$m: $thumbnail t=".spip_timer($m)." length:" . filesize($thumbnail), 'ai_preview' . _LOG_DEBUG);
		return array($thumbnail, 'gradients');
	}
	return false;
}

function adaptive_images_preview_potrace($image, $options) {
	$gradients = charger_fonction("image_potrace", "preview");
	spip_timer($m = 'POTRACE');
	if ($thumbnail = $gradients($image, $options)) {
		spip_log("$m: $thumbnail t=".spip_timer($m)." length:" . filesize($thumbnail), 'ai_preview' . _LOG_DEBUG);
		return array($thumbnail, 'potrace');
	}
	//spip_timer('potrace');
	return false;
}

function adaptive_images_preview_geometrize($image, $options) {
	$geometrize = charger_fonction("image_geometrize", "preview");
	spip_timer($m = 'GEOMETRIZE');
	if ($thumbnail = $geometrize($image, $options)) {
		spip_log("$m: $thumbnail t=".spip_timer($m)." length:" . filesize($thumbnail), 'ai_preview' . _LOG_DEBUG);
		return array($thumbnail, 'geometrize');
	}
	//spip_timer('geometrize');
	return false;
}

/** Filtres  ***********************************************************************************************************/


/**
 * Rendre les images d'un texte adaptatives, en permettant de preciser la largeur maxi a afficher en 1x
 * [(#TEXTE|adaptive_images{1024})]
 * ou passer la liste des breakpoints (le dernier est la largeur maxi 1x)
 * [(#TEXTE|adaptive_images{160/320/480/640/1024})]
 * ou passer un argument de sizes pour indiquer le comportement des images de ce bloc
 *   - au format media queries (en string)
 *    (la taille par defaut 100vw ou 100% en dernier argument peut etre omise, elle sera ajoutee automatiquement si besoin)
 * [(#TEXTE|adaptive_images{1024,'(min-width: 40em) 80vw, 100vw'})]
 * [(#TEXTE|adaptive_images{1024,'(max-width: 768px) 100vw, (max-width: 900px) 50vw, 33vw'})]
 *   - au format tableau, plus rapide a ecrire (l'unite px peut etre omise, elle est ajoutee aux nombres sans unites)
 * [(#TEXTE|adaptive_images{1024,#ARRAY{768px,100vw,900px,50vw,*,33vw}})]
 *
 * @param string $texte
 * @param null|int|string $max_width_1x
 * @param null|string|array $sizes
 * @return mixed
 */
function adaptive_images($texte, $max_width_1x=null, $sizes=null){
	return adaptive_images_base($texte, $max_width_1x, $sizes);
}

/**
 * Rendre les images d'un texte adaptatives mais en background sur des span seulement (pas de balise <img>)
 * @param string $texte
 * @param null|int $max_width_1x
 * @param string $class
 * @param string $bgcolor
 * @return mixed
 */
function adaptive_images_background($texte, $max_width_1x=null, $class = '', $bgcolor=''){
	$res = adaptive_images_base($texte, !empty($max_width_1x) ? $max_width_1x : null, null, true);
	if ($class or $bgcolor) {
		// injecter la class sur les balises span.adapt-img-background
		$spans = extraire_balises($res, 'span');
		foreach ($spans as $span) {
			if (strpos($span,'adapt-img-background') !== false) {
				$span = explode('>', $span, 2);
				$s = $span[0] . '>';
				$c = extraire_attribut($s, "class");
				if (strpos($c, 'adapt-img-background') !== false) {
					$s2 = $s;
					if ($class) {
						$c = rtrim($c) . ' '. $class;
						$s2 = inserer_attribut($s2, 'class', $c);
					}
					if ($bgcolor) {
						$style = trim(extraire_attribut($s2, "style"));
						if ($style) {
							$style = rtrim($style, ';') . ';';
						}
						$style .= "background-color:$bgcolor";
						$s2 = inserer_attribut($s2, 'style', $style);
					}
					if ($s2 !== $s) {
						$res = str_replace($s, $s2, $res);
					}
				}
			}
		}
	}
	return $res;
}

/**
 * nommage alternatif
 * @param string $texte
 * @param null|int|string $max_width_1x
 * @param null|string|array $sizes
 * @return mixed
 */
function adaptative_images($texte, $max_width_1x=null, $sizes=null){
	return adaptive_images($texte, $max_width_1x, $sizes);
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
	if ($GLOBALS['html']){
		#spip_timer();
		$AdaptiveImages = SPIPAdaptiveImages::getInstance();
		$texte = $AdaptiveImages->adaptHTMLPage($texte);
		#var_dump(spip_timer());
	}
	return $texte;
}

/**
 * Inserer jquery.lazyload.js si besoin
 * @param $texte
 * @return string
 */
function adaptive_images_insert_head($texte){
	$AdaptiveImages = SPIPAdaptiveImages::getInstance();
	if ($AdaptiveImages->lazyload){
		if ($js = find_in_path("javascript/jquery.lazyload.js"))
			$texte .= "<script type='text/javascript' src='$js'></script>\n";
		if ($js = find_in_path("javascript/adaptive.lazyload.js"))
			$texte .= "<script type='text/javascript' src='$js'></script>\n";
	}
	if (defined('_ADAPTIVE_IMAGES_DEBUG_PREVIEW') and _ADAPTIVE_IMAGES_DEBUG_PREVIEW) {
		$style_debug = ".adapt-img:hover {opacity: 1 !important;}";
		if ($AdaptiveImages->markupMethod === 'srcset') {
			$style_debug = ".adapt-img-wrapper:hover{background-size:cover !important;}.adapt-img-wrapper:hover .adapt-img {opacity: 0.01 !important;}";
		}
		$texte .= "<style type='text/css'>$style_debug</style>";
	}

	return $texte;
}
