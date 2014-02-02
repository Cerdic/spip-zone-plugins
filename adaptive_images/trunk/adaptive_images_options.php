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
	// qualite des images lowsrc
	if (isset($settings['lowsrc_jpg_quality']) AND $q=intval($settings['lowsrc_jpg_quality']))
		$AdaptiveImages->lowsrcJpgQuality= $q;
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

	// on ne traite pas les images de largeur inferieure a min_width_1x px
	if (isset($settings['min_width_1x']) AND $v=intval($settings['min_width_1x']))
		$AdaptiveImages->minWidth1x = $v;
	// on ne traite pas les images de poids inferieur a min_filesize ko
	if (isset($settings['min_filesize']) AND $v=intval($settings['min_filesize']))
		$AdaptiveImages->minFileSize = $v*1024;


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


/** Filtre  ***********************************************************************************************************/

/**
 * Rendre les images d'un texte adaptatives, en permettant de preciser la largeur maxi a afficher en 1x
 * [(#TEXTE|adaptive_images{1024})]
 * ou passer la liste des breakpoints (le dernier est la largeur maxi 1x)
 * [(#TEXTE|adaptive_images{160/320/480/640/1024})]
 * @param string $texte
 * @param null|int $max_width_1x
 * @return mixed
 */
function adaptive_images($texte,$max_width_1x=null){
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
	$AdaptiveImages = SPIPAdaptiveImages::getInstance();
	return $AdaptiveImages->adaptHTMLPart($texte, $max_width_1x, $bkpt);
}

/**
 * nommage alternatif
 * @param $texte
 * @param int $max_width_1x
 * @return mixed
 */
function adaptative_images($texte,$max_width_1x=null){
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
	if ($GLOBALS['html']){
		#spip_timer();
		$AdaptiveImages = SPIPAdaptiveImages::getInstance();
		$texte = $AdaptiveImages->adaptHTMLPage($texte);
		#var_dump(spip_timer());
	}
	return $texte;
}

?>