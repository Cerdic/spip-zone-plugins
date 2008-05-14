<?php

function template_insert_head($flux){'
	<script type="text/javascript" src="'.find_in_path('plugin_interface/js/interface.js').'"></script>
	<script type="text/javascript" src="'.find_in_path('plugin_interface/js/iutil.js').'"></script>
	<script type="text/javascript" src="'.find_in_path('plugin_interface/js/fisheye.js').'"></script>
	<script type="text/javascript" src="'.find_in_path('plugin_interface/js/carousel.js').'"></script>
	
	<link rel="stylesheet" href="'.find_in_path('interface.css').'" type="text/css" media="all" />';

	return $flux;
}


?>