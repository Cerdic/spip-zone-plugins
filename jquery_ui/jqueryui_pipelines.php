<?php

function jqueryui_jquery_plugins($plugins){
	$config = @unserialize($GLOBALS['meta']['jqueryui']);
	
	if (!is_array($config))
		$config['plugins'] = array();
	
	foreach ($config['plugins'] as $val) {
		$plugins[] = _DIR_JQUERYUI_JS.$val.".js";
	}
	
	return $plugins;
}

?>
