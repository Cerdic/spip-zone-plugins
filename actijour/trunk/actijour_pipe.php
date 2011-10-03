<?php
/*
+--------------------------------------------+
| ACTIVITE DU JOUR v. 2.0 - 06/2009 - SPIP 2.x
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| Declare pipeline
+--------------------------------------------+
*/

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_ACTIJOUR',(_DIR_PLUGINS.end($p)));

	# style + js
	function actijour_header_prive($flux) {
		$exec = _request('exec');
		if(preg_match('@^(actijour_).*@i',$exec)) {
		$flux .= '<link rel="stylesheet" type="text/css" href="'._DIR_PLUGIN_ACTIJOUR.'actijour_styles.css" />'."\n";
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_ACTIJOUR.'func_js_acj.js"></script>'."\n";
		}
		return $flux;
	}
	

	# repertoire icones ACTIJOUR
	if (!defined("_DIR_IMG_ACJR")) {
		define('_DIR_IMG_ACJR', _DIR_PLUGIN_ACTIJOUR.'/img_pack/');
	}
?>
