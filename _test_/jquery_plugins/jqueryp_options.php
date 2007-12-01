<?php
if (!defined('_DIR_LIB')) define('_DIR_LIB', 'lib/');
	

/* liste des plugins de jquery */
$GLOBALS['jquery_plugins'] = array(

	//autocomplete
	'autocomplete' => array(
		'dir' => 'jquery.autocomplete',
		'url' => 'http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/',
		//'install_zip' => 'http://dev.jquery.com/view/trunk/plugins/autocomplete/jquery.autocomplete.zip',
		'files' => array(
			//'autocomplete.autocomplete'		=> 'jquery.autocomplete.js', // alias
			'autocomplete' 					=> 'jquery.autocomplete.js', 
			'autocomplete.min'				=> 'jquery.autocomplete.min.js',
			'autocomplete.pack' 			=> 'jquery.autocomplete.pack.js',
			'autocomplete.bundle' 			=> 'jquery.autocomplete-bundle.js',
			'autocomplete.bundle.pack' 		=> 'jquery.autocomplete-bundle.pack.js'
		)
	),
	
	//datepicker
	'datepicker' => array(
		'dir' => 'jquery.datepicker',
		'url' => 'http://jquery.com/plugins/project/datepicker',
		'install' => array(
			'jquery.datePicker.js' => 'http://jquery.com/plugins/files/jquery.datePicker.js_1.txt'
		),
		'files' => array(
			'datepicker' 		=> 'jquery.datePicker.js'
		)
	),
	
	//easing
	'easing' => array(
		'dir' => 'jquery.easing',
		'url' => 'http://jquery.com/plugins/project/Easing',
		'install' => array(
			'jquery.easing.1.2.js' => 'http://jquery.com/plugins/files/jquery.easing.1.2.js.txt'
		),
		'files' => array(
			'easing' => 'jquery.easing.1.2.js'
		)
	),
		
	
	//syncHeight
	'syncheight' => array(
		'dir' => 'jquery.syncheight',
		'url' => 'http://ginader.devjavu.com/browser/trunk/jquery/plugins/syncHeight/',
		'install' => array(
			'jquery.syncheight.js' => 'http://ginader.devjavu.com/browser/trunk/jquery/plugins/syncHeight/jquery.syncheight.js?format=txt'
		),
		'files' => array(
			'syncheight' => 'jquery.syncheight.js'
		)
	),	
		
	//ui
	'ui' => array(
		'dir' => 'jquery.ui-1.0',
		'dir_themes' => 'themes',
		'url' => 'http://jquery.com/plugins/project/ui',
		//'install_zip' => 'http://jqueryjs.googlecode.com/files/jquery.ui-1.0.zip',
		'files' => array(
			'ui.accordion' 		=> 'ui.accordion.js',
			'ui.calendar' 		=> 'ui.calendar.js',
			'ui.dialog' 		=> 'ui.dialog.js',
			'ui.dimensions' 	=> 'jquery.dimensions.js',
			//'jquery.dimensions' => 'jquery.dimensions.js', // au cas ou quelqu'un l'appelle comme cela
			'ui.draggable' 		=> 'ui.draggable.js',
			'ui.draggable.ext' 	=> 'ui.draggable.ext.js',
			'ui.droppable' 		=> 'ui.droppable.js',
			'ui.droppable.ext' 	=> 'ui.droppable.ext.js',
			'ui.magnifier' 		=> 'ui.magnifier.js',
			'ui.mouse' 			=> 'ui.mouse.js',
			'ui.resizable' 		=> 'ui.resizable.js',
			'ui.selectable' 	=> 'ui.selectable.js',
			'ui.shadow' 		=> 'ui.shadow.js',
			'ui.slider' 		=> 'ui.slider.js',
			'ui.sortable' 		=> 'ui.sortable.js',
			'ui.tablesorter' 	=> 'ui.tablesorter.js',
			'ui.tabs' 			=> 'ui.tabs.js'	
		),	
		'themes' => array(
			'dark' 		=> 'dark',
			'flora' 	=> 'flora',
			'light' 	=> 'light'
		)
	),
	
	//validate
	'validate' => array(
		'dir' => 'jquery.validate_7',
		'url' => 'http://jquery.com/plugins/project/validate',
		//'install_zip' => 'http://jquery.com/plugins/files/jquery.validate_7.zip',
		'files' => array(
			'validate.additional-methods' 	=> 'additional-methods.js',
			'validate.ajaxQueue'			=> 'jquery.ajaxQueue.js',
			'validate.metadata' 			=> 'jquery.metadata.js',
			//'validate.validate' 			=> 'jquery.validate.js',
			'validate' 						=> 'jquery.validate.js', // alias
			'validate.min'					=> 'jquery.validate.min.js',
			'validate.pack' 				=> 'jquery.validate.pack.js'
			
		)
	),
		
	//yav
	'yav' => array(
		'dir' => 'jquery.yav1.1.1',
		'url' => 'http://jquery.com/plugins/project/jquery_yav',
		//'install_zip' => 'http://jquery.com/plugins/files/jquery.yav1.1.1.zip',
		'files' => array(
			'yav' => 'jquery.yav.js'
		)
	)
	
);


// signaler le pipeline d'ajout de plugins jquery
$GLOBALS['spip_pipeline']['insert_jquery_plugins'] = "";

?>
