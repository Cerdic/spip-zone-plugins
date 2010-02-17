<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_FORM_UNIFORM',(_DIR_PLUGINS.end($p)));

function form_uniform_charger_script($flux) {



	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_FORM_UNIFORM.'css/uniform.default.css" type="text/css" media="all" />';
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_FORM_UNIFORM.'jquery.uniform.js"></script>';
	
	$flux .= '<script type="text/javascript">
		$(document).ready(function() {
			$("input:checkbox, input:radio, input:file").uniform();
		});
	</script>';
	
	return $flux;
}

?>