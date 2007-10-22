<?php
if (!defined('_DIR_LIB')) define('_DIR_LIB', 'lib/');

/* liste des plugins de jquery */
$GLOBALS['jquery_plugins'] = array(
	//ui
	'ui' => array(
		'dir' => 'jquery.ui-1.0',
		'dir_themes' => 'themes',
		'url' => 'http://jquery.com/plugins/project/ui',
		'files' => array(
			'ui.accordion' 		=> 'ui.accordion.js',
			'ui.calendar' 		=> 'ui.calendar.js',
			'ui.dialog' 		=> 'ui.dialog.js',
			'ui.dimensions' 	=> 'jquery.dimensions.js',
			'jquery.dimensions' => 'jquery.dimensions.js', // au cas ou quelqu'un l'appelle comme cela
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
	
	//yav
	'yav' => array(
		'dir' => 'jquery.yav1.1.1',
		'url' => 'http://jquery.com/plugins/project/jquery_yav',
		'files' => array(
			'yav' => 'jquery.yav.js'
		)
	)
	
);


// signaler le pipeline d'ajout de plugins jquery
$GLOBALS['spip_pipeline']['insert_jquery_plugins'] = "";

?>
