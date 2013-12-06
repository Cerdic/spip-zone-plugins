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

	protected function filepath2URL($filepath){
		$filepath = parent::filepath2URL($filepath);
		if (defined('_ADAPTIVE_IMAGES_DOMAIN')){
			$filepath = rtrim(_ADAPTIVE_IMAGES_DOMAIN,"/")."/".$filepath;
		}
		return $filepath;
	}
}
$AdaptiveImages = SPIPAdaptiveImages::getInstance();


// utiliser le progressive rendering sur PNG et GIF si pas de JS
if (defined('_ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING'))
	$AdaptiveImages->nojsPngGifProgressiveRendering = _ADAPTIVE_IMAGES_NOJS_PNGGIF_PROGRESSIVE_RENDERING;
// couleur de background pour les images lowsrc
if (defined('_ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR'))
	$AdaptiveImages->lowsrcJpgBgColor = _ADAPTIVE_IMAGES_LOWSRC_JPG_BG_COLOR;
// qualite des images lowsrc
if (defined('_ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY'))
	$AdaptiveImages->lowsrcJpgQuality = _ADAPTIVE_IMAGES_LOWSRC_JPG_QUALITY;
// qualite de compression JPG pour les images 1.5x et 2x (on peut comprimer plus)
if (defined('_ADAPTIVE_IMAGES_15x_JPG_QUALITY'))
	$AdaptiveImages->x15JpgQuality = _ADAPTIVE_IMAGES_15x_JPG_QUALITY;
if (defined('_ADAPTIVE_IMAGES_20x_JPG_QUALITY'))
	$AdaptiveImages->x20JpgQuality = _ADAPTIVE_IMAGES_20x_JPG_QUALITY;
// Breakpoints de taille d'ecran pour lesquels on generent des images
if (defined('_ADAPTIVE_IMAGES_DEFAULT_BKPTS'))
	$AdaptiveImages->defaultBkpts = explode(",",_ADAPTIVE_IMAGES_DEFAULT_BKPTS);
// les images 1x sont au maximum en _ADAPTIVE_IMAGES_MAX_WIDTH_1x px de large dans la page
if (defined('_ADAPTIVE_IMAGES_MAX_WIDTH_1x'))
	$AdaptiveImages->maxWidth1x = _ADAPTIVE_IMAGES_MAX_WIDTH_1x;
// on ne traite pas les images de largeur inferieure a _ADAPTIVE_IMAGES_MIN_WIDTH_1x px
if (defined('_ADAPTIVE_IMAGES_MIN_WIDTH_1x'))
	$AdaptiveImages->minWidth1x = _ADAPTIVE_IMAGES_MIN_WIDTH_1x;
// Pour les ecrans plus petits, c'est la version mobile qui est fournie (recadree)
if (defined('_ADAPTIVE_IMAGES_MAX_WIDTH_MOBILE_VERSION'))
	$AdaptiveImages->maxWidthMobileVersion = _ADAPTIVE_IMAGES_MAX_WIDTH_MOBILE_VERSION;

// GD memory limit
if (defined('_IMG_GD_MAX_PIXELS'))
	$AdaptiveImages->maxImagePxGDMemoryLimit = _IMG_GD_MAX_PIXELS;

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
define('_ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION',true);
if (defined('_ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION'))
	$AdaptiveImages->onDemandImages = _ADAPTIVE_IMAGES_ON_DEMAND_PRODUCTION;

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
 * @param string $texte
 * @param null|int $max_width_1x
 * @return mixed
 */
function adaptive_images($texte,$max_width_1x=null){
	$AdaptiveImages = SPIPAdaptiveImages::getInstance();
	return $AdaptiveImages->adaptHTMLPart($texte, $max_width_1x);
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