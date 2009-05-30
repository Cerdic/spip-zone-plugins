<?php
if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');
	

/* liste des plugins de jquery */
$GLOBALS['jquery_plugins'] = array(

	//autocomplete (test avec version 1.0.2)
	'autocomplete' => array(
		'dir' => 'jquery-autocomplete',
		'url' => 'http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/',
		'install' => 'http://jquery.bassistance.de/autocomplete/jquery.autocomplete.zip',
	),
	
	//datepicker (test avec version 2.1.1)
	// NOTE : la librairie UI a aussi un datepicker
	'datepicker' => array(
		'dir' => 'jquery.datepicker',
		'url' => 'http://jquery.com/plugins/project/datepicker',
		'install' => array(
			'jquery.datePicker.js' => 'http://plugins.jquery.com/files/jquery.datePicker.js_3.txt'
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
		
	// farbtastic (color picker)
	'farbtastic' => array(
		'dir' => 'farbtastic12/farbtastic',
		'url' => 'http://acko.net/dev/farbtastic',
		'install' => 'http://acko.net/files/farbtastic_/farbtastic12.zip'
	),
	
		
	//syncHeight (maintenu ? utile ?)
	'syncheight' => array(
		'dir' => 'jquery.syncheight',
		'url' => 'http://ginader.devjavu.com/browser/trunk/jquery/plugins/syncHeight/',
		'install' => array(
			'jquery.syncheight.js' => 'http://ginader.devjavu.com/browser/trunk/jquery/plugins/syncHeight/jquery.syncheight.js?format=txt'
		)
	),	
		
	//ui
	'ui' => array(
		'dir' => 'jquery.ui-1.6rc2/ui',
		'dir_themes' => 'jquery.ui-1.6rc2/themes',
		'url' => 'http://ui.jquery.com/',
		'install' => 'http://jquery-ui.googlecode.com/files/jquery.ui-1.6rc2.zip',
		// declarer des dossiers de themes
		// ces dossiers contiennent des fichiers nom.css ; optionnellement nom.ext.css
		// par exemple le theme light pourra etre appele par :
		// #JQUERY_PLUGIN_THEME{flora, flora.tabs} ce qui chargera les css
		// 'themes/flora/flora.css' et 'themes/flora/flora.tabs.css'
		'themes' => array(
			'ui' 	=> 'default',
			'flora' 	=> 'flora'
		)
	),
	
	//validate (test avec version 1.4)
	'validate' => array(
		'dir' => 'jquery-validate',
		'url' => 'http://bassistance.de/jquery-plugins/jquery-plugin-validation/',
		'install' => 'http://jquery.bassistance.de/validate/jquery.validate.zip',
	),
		
	//yav
	'yav' => array(
		'dir' => 'jquery.yav.1.2.0',
		'url' => 'http://jquery.com/plugins/project/jquery_yav',
		'install' => 'http://plugins.jquery.com/files/jquery.yav.1.2.0.zip'
	)
	
);


?>
