<?php 

// inc/imageflow_pipeline_header_prive.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/*
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut reactiver le plugin (config/plugin: desactiver/activer)
	
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/imageflow_api_globales');
include_spip('inc/imageflow_api_prive');

function imageflow_header_prive ($flux) {

	$exec = _request('exec');

	if($exec)
	{
		if ($exec == "imageflow_configure")
		{
			$default = "";
			$preferences_default = unserialize(_IMAGEFLOW_PREFERENCES_DEFAULT);
			foreach($preferences_default as $key => $value)
			{
				if($key == "img") continue;
				$default .= $key.":\"".$value."\",";
			}
			$default = "var imageflow_default={".rtrim($default, ",")."};";
			
			$flux .= ""
				. "\n\n<!-- PLUGIN PORTFOLIO IMAGEFLOW -->\n"
				. "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/imageflow_prive.css'))."' />\n"
				. "<!--[if IE]>\n"
				. "<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/imageflow_prive_ie.css'))."' />\n"
				. "<![endif]-->\n"
				. "<script src='".url_absolue(find_in_path('javascript/imageflow_prive.js'))."' type='text/javascript'></script>\n"
				. "<script type='text/javascript'>".$default."</script>\n"
				;
		}
		elseif ($exec == "articles")
		{
			if($f = charger_fonction('imageflow_header', 'inc'))
			{
				$flux .= $f();
			}
		}
	}
	
	
	return ($flux);
}

?>