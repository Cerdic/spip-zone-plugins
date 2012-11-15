<?php
/**
 * CF. http://www.tinymce.com/wiki.php/Configuration
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
//ini_set('display_errors','1'); error_reporting(E_ALL);

/**
 * Configuration par defaut
 */	
$GLOBALS['tinymce_config_def'] = array(
	'objets'=>array(
		'article', 'rubrique', 'breve', 'mot', 'groupe_mot'
	),
	'objets_barres'=>array(
		'article'=>'full_interface', 
		'rubrique'=>'advanced', 
		'breve'=>'simple',
		'groupe_mot'=>'simple',
		'mot'=>'advanced',
	),
	'content_css'=>array(
		'squelettes-dist/css/typo.css',
		'squelettes-dist/css/layout.css',
	),
	'body_id' => '',
	'body_class' => 'page content texte',
	'skin' => 'o2k7',
	'skin_variant' => 'silver',
);

/**
 * Entrées de config automatiques et obligatoires
 */	
$GLOBALS['tinymce_config_obligatoire'] = array(
	'content_css'=>array( 'css/tinymce_layout.css' ),
	'body_class' => 'spiptinymce',
);

/**
 * Skins et themes proposes par TinyMCE
 */	
$GLOBALS['tinymce_habillages'] = array(
	'default'=>array(
		'default',
	),
	'highcontrast'=>array(
		'default',
	),
	'o2k7'=>array(
		'default','black', 'silver'
	),
);

/**
 * Argument d'URL pour attitrer la barre
 */
$GLOBALS['tinymce_arg_barre'] = 'tmce_barre';

/**
 * Classe CSS pour protéger les codes SPIP
 */
$GLOBALS['tinymce_protect_class'] = 'spiptmceInsert';

// debug direct
//echo '<pre>'; var_export( unserialize( $GLOBALS['meta']['tinymce'] ) ); exit('yo');

?>