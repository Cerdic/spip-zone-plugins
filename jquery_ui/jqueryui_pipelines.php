<?php

function jqueryui_jquery_plugins($plugins){
	$config = @unserialize($GLOBALS['meta']['jqueryui']);
	
	if (!is_array($config) OR !is_array($config['plugins']))
		$config['plugins'] = array();
	
	$config['plugins'] = array_unique(array_merge(sinon(pipeline('jqueryui_forcer'),array()),$config['plugins']));
	
	foreach ($config['plugins'] as $val) {
		$plugins[] = _DIR_JQUERYUI_JS.$val.".js";
	}
	
	return $plugins;
}

?>
