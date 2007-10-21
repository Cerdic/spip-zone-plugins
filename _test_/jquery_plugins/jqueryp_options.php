<?php

/* liste des plugins de jquery */
$GLOBALS['jquery_plugins'] = array(
	//ui
	'ui' => array(
		'dir' => 'jquery.ui-1.0',
		'url' => 'http://jquery.com/plugins/project/ui',
		'files' => array(
			'ui.accordion' 		=> 'ui.accordion.js',
			'ui.calendar' 		=> 'ui.calendar.js',
			'ui.dialog' 		=> 'ui.dialog.js',
			'ui.dimensions' 	=> 'jquery.dimensions.js',
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

$GLOBALS['jquery_plugins_themes'] = array(
	//ui
	'ui' => array(
		'dir' => 'jquery.ui-1.0/themes',
		'themes' => array(
			'dark' 		=> 'dark',
			'flora' 	=> 'flora',
			'light' 	=> 'light'
		)
	)
);


?>
