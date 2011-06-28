<?php
/**
 * @name 		Defaults / Chargeur
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */

/**
 * Defintion des emplacements du site
 */
$GLOBALS['emplacements_site'] = array(
	'1' => array(
		'titre' => 'Skyscraper', 
		'width' => '160', 
		'height' => '600', 
		'statut' => '2actif',
		'ratio_pages' => '100',
	),
	'2' => array(
		'titre' => 'Banner', 
		'width' => '468', 
		'height' => '60', 
		'statut' => '2actif',
		'ratio_pages' => '100',
	),
	'3' => array(
		'titre' => 'Leaderboard', 
		'width' => '728', 
		'height' => '90', 
		'statut' => '2actif',
		'ratio_pages' => '75',
	),
	'4' => array(
		'titre' => 'Cube', 
		'width' => '250', 
		'height' => '250', 
		'statut' => '2actif',
		'ratio_pages' => '33',
	),
);

/**
 * Exemples de pubs
 */
$GLOBALS['publicites_site'] = array(
	'1' => array(
		'id_empl' => '1',
		'titre' => 'Skyscraper : exemple 1',
		'url' => $GLOBALS['meta']['adresse_site'].'/?page=adds',
		'objet' => _DIR_IMGPUB.'/pubbantest_skyscraper.png',
		'type' => 'img',
		'statut' => '2actif',
		'illimite' => 'oui',
		'date_add' => date("Y-m-d H:i:s"),
	),
	'2' => array(
		'id_empl' => '2',
		'titre' => 'Banner : exemple 1', 
		'url' => 'http://www.example.com/',
		'objet' => _DIR_IMGPUB.'/pubbantest_banner.gif',
		'type' => 'img',
		'statut' => '2actif',
		'clics_restant' => '1000000', 
		'date_add' => date("Y-m-d H:i:s"),
	),
	'3' => array(
		'id_empl' => '2',
		'titre' => 'Banner : exemple 2', 
		'url' => $GLOBALS['meta']['adresse_site'].'/?page=adds',
		'objet' => _DIR_IMGPUB.'/bannierewipub468x60.gif',
		'type' => 'img',
		'statut' => '2actif',
		'clics_restant' => '1000000', 
		'date_add' => date("Y-m-d H:i:s"),
	),
	'4' => array(
		'id_empl' => '3',
		'titre' => 'Leaderboard : exemple 1', 
		'url' => 'http://www.example.com/',
		'objet' => _DIR_IMGPUB.'/pubbantest_leaderboard.gif',
		'type' => 'img',
		'statut' => '2actif',
		'clics_restant' => '1000000', 
		'date_add' => date("Y-m-d H:i:s"),
	),
	'5' => array(
		'id_empl' => '4',
		'titre' => 'Cube : exemple 1', 
		'url' => '',
		'objet' => _DIR_IMGPUB.'/pubbantest_cube.gif',
		'type' => 'img',
		'statut' => '2actif',
		'date_debut' => date("Y-m-d"),
		'date_add' => date("Y-m-d H:i:s"),
	),
	'6' => array(
		'id_empl' => '4',
		'titre' => 'Cube : exemple flash', 
		'url' => 'http://www.example.com/',
		'objet' => "<object onClick='clic();' type='application/x-shockwave-flash' data='"._DIR_IMGPUB."/dewslider.swf?xml=index.php?page=pubban_demo_flash' width='250' height='250'><param name='movie' value='"._DIR_IMGPUB."/dewslider.swf?xml=index.php?page=pubban_demo_flash' /></object>",
		'type' => 'flash',
		'statut' => '2actif',
		'date_debut' => date("Y-m-d"),
		'date_fin' => date("Y-m")."-".(date("d")+1),
		'date_add' => date("Y-m-d H:i:s"),
	),
	'7' => array(
		'id_empl' => '4',
		'titre' => 'Cube : exemple SPIP', 
		'url' => 'http://www.spip.net/',
		'objet' => _DIR_IMGPUB.'/ad_home.png',
		'type' => 'img',
		'statut' => '2actif',
		'date_debut' => date("Y-m-d"),
		'date_fin' => date("Y-m")."-".(date("d")+1),
		'date_add' => date("Y-m-d H:i:s"),
	),
);

/**
 * Demo flash
 */
$GLOBALS['flash_demo'] = array(
	'1' => array(
		'titre' => 'Votre pub ici ...', 
		'width' => '250', 
		'height' => '250', 
		'src' => _DIR_IMGPUB.'/pub-defaut-250x250.gif',
		'href' => $GLOBALS['meta']['adresse_site'].'/?page=adds',
	),
	'2' => array(
		'titre' => 'Demo pub ...', 
		'width' => '250', 
		'height' => '250', 
		'src' => _DIR_IMGPUB.'/20081219_studiob_250x250.jpg',
		'href' => $GLOBALS['meta']['adresse_site'].'/?page=adds',
	),
);
?>