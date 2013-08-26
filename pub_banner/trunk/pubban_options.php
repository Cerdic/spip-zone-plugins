<?php
/**
 * Gestionnaire de bannieres publicitaires
 *
 * Le statut des pub peut �tre :
 * - 0cree	=> cas exceptionnel de cr�ation via ADDS
 * - 1inactif
 * - 2actif
 * - 3obsolete
 * - 4rompu
 * - 5poubelle
 *
 * <b>Tailles classiques des banni�res publicitaires</b>
 * - banniere : 468x60 px | 35 Ko
 * - skyscraper : 120x600 px | 50 Ko
 * - pave : 300x250 px | 50 Ko
 * - carre : 250x250 px | 50 Ko
 * - bouton (logos ...) : jusqu'� 120 px (120x60 px)
 * - pour les animations, recommander des gif anim�s de 15 secondes max
 *
 * <b>Les tarifs</b>
 * - CPM : co�t pour mille affichages
 * - CPC : co�t par clic
 *
 * @name 		Options
 * @author 		Piero Wbmstr <piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.1 (09/2010)
 * @package		Pub Banner
 * @todo Faire une page 'ADDS' autonome, qui ne depende ni de SPIP (entete et pied) ni d'un article
 * @todo Il reste un probleme de lien (au clic) sur les bannieres flash ...
 * @todo inclure les prix par defaut pour les nouvelles bannieres
 */
if (!defined("_ECRIRE_INC_VERSION")) return;
//ini_set('display_errors','1'); error_reporting(E_ALL);

// -----------------------
// Definitions
// -----------------------

/**
 * Pour forcer l'effacement des tables lors de la desinstall, meme s'ils sont pleins 
 * => desinstall impossible si des champs sont !=0
 * => define('PUBBAN_FORCE_UNINSTALL',1); pour forcer l'effacement des tables
 * => utilitaire de dev ou test
 */
define('PUBBAN_FORCE_UNINSTALL', 0);

/**
 * Pour forcer l'utilisation d'une fonction Javascript pour ouvrir les popups (retrait de l'attribut "target")
 */
define('PUBBAN_FORCE_JAVASCRIPT_ONCLICK', 0);

/**
 * Adresse du cliqueur
 */
define('_PUBBAN_ADDS_CLICKER', 'clic');

/**
 * Chemin vers les images PUB
 */
define('_DIR_IMGPUB', str_replace('../', $GLOBALS['meta']['adresse_site'].'/', _DIR_PLUGIN_PUBBAN).'img_pub' );

/**
 * Definition des puces selon le statut des pubs
 */
$GLOBALS['_PUBBAN_PUCES_STATUTS'] = array(
	'img' => array(
		'name' => 'Image',
		'value' => 'img',
		'icon' => find_in_path("prive/themes/spip/images/image.png"),
	),
	'swf' => array(
		'name' => 'SWF object',
		'value' => 'swf',
		'icon' => find_in_path("prive/themes/spip/images/application_flash.gif"),
	),
	'flash' => array(
		'name' => 'Flash object',
		'value' => 'flash',
		'icon' => find_in_path("prive/themes/spip/images/application_flash.gif"),
	),
	'banniere' => array(
		'name' => 'Banner',
		'icon' => find_in_path("prive/themes/spip/images/insert-image-16.png"),
	),
);

/**
 * Definition des icones utilisees
 */
$GLOBALS['pubban_pub_icons'] = array(
	'default' => find_in_path("prive/themes/spip/images/gnome-text-x-readme.png"),
	'bmp' => find_in_path("prive/themes/spip/images/gnome-image-bmp.png"),
	'gif' => find_in_path("prive/themes/spip/images/gnome-image-gif.png"),
	'jpeg' => find_in_path("prive/themes/spip/images/gnome-image-jpeg.png"),
	'jpg' => find_in_path("prive/themes/spip/images/gnome-image-jpeg.png"),
	'png' => find_in_path("prive/themes/spip/images/gnome-image-png.png"),
	'swf' => find_in_path("prive/themes/spip/images/gnome-flash.png"),
	'flash' => find_in_path("prive/themes/spip/images/gnome-flash.png"),
);

/**
 * URL de documentation/information
 */
define('_PUBBAN_URL', 'http://contrib.spip.net/?article3637');
/**
 * URL de telechargement des mises a jour
 */
define('_PUBBAN_UPDATE', 'http://files.spip.org/spip-zone/pub_banner_spip3.zip');
/**
 * Traceur de dev.
 */
define('_PUBBAN_TRAC', 'http://zone.spip.org/trac/spip-zone/browser/_plugins_/pub_banner');

// Si admin, lib pubban_prive
if(test_espace_prive()) include_spip('inc/pubban_prive');

?>
