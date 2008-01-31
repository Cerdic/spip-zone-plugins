<?php
if (!defined('_DIR_LIB')) define('_DIR_LIB', 'lib/');
	

/* liste des plugins de jquery */
$GLOBALS['jquery_plugins'] = array(

	//autocomplete
	'autocomplete' => array(
		'dir' => 'jquery.autocomplete',
		'url' => 'http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/',
		'install' => 'http://dev.jquery.com/view/trunk/plugins/autocomplete/jquery.autocomplete.zip',
	),
	
	//datepicker
	'datepicker' => array(
		'dir' => 'jquery.datepicker',
		'url' => 'http://jquery.com/plugins/project/datepicker',
		'install' => array(
			'jquery.datePicker.js' => 'http://jquery.com/plugins/files/jquery.datePicker.js_1.txt'
		)
	),
	
	//easing
	'easing' => array(
		'dir' => 'jquery.easing',
		'url' => 'http://gsgd.co.uk/sandbox/jquery/easing/',
		'install' => array(
			'jquery.easing.1.2.js' => 'http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.2.js',
			'jquery.easing.1.3.js' => 'http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js',
			'jquery.easing.compatibility.js' => 'http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.compatibility.js'
		)
	),
		
	
	
		
	//syncHeight
	'syncheight' => array(
		'dir' => 'jquery.syncheight',
		'url' => 'http://ginader.devjavu.com/browser/trunk/jquery/plugins/syncHeight/',
		'install' => array(
			'jquery.syncheight.js' => 'http://ginader.devjavu.com/browser/trunk/jquery/plugins/syncHeight/jquery.syncheight.js?format=txt'
		)
	),	
		
	//ui
	'ui' => array(
		'dir' => 'jquery.ui-1.0',
		'url' => 'http://jquery.com/plugins/project/ui',
		'install' => 'http://jqueryjs.googlecode.com/files/jquery.ui-1.0.zip',
		// declarer des dossiers de themes
		// ces dossiers contiennent des fichiers nom.css ; optionnellement nom.ext.css
		// par exemple le theme light pourra etre appele par :
		// #JQUERY_PLUGIN_THEME{light, light.tabs} ce qui chargera les css
		// 'themes/light/light.css' et 'themes/light/light.tabs.css'
		'themes' => array(
			'dark' 		=> 'themes/dark',
			'flora' 	=> 'themes/flora',
			'light' 	=> 'themes/light'
		)
	),
	
	//validate
	'validate' => array(
		'dir' => 'jquery.validate_8',
		'url' => 'http://jquery.com/plugins/project/validate',
		'install' => 'http://jquery.com/plugins/files/jquery.validate_8.zip',
	),
		
	//yav
	'yav' => array(
		'dir' => 'jquery.yav1.1.1',
		'url' => 'http://jquery.com/plugins/project/jquery_yav',
		'install' => 'http://jquery.com/plugins/files/jquery.yav1.1.1.zip'
	)
	
);


// signaler le pipeline d'ajout de plugins jquery
$GLOBALS['spip_pipeline']['insert_jquery_plugins'] = "";

?>
