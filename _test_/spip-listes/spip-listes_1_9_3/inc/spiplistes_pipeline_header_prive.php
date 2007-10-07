<?php 
/*
	SPIP-Listes pipeline
	inc/spiplistes_pipeline_header_prive.php
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut réactiver le plugin (config/plugin: désactiver/activer)
	
*/


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

function spiplistes_header_prive($flux) {
	$exec = _request('exec');
	$flux .="\n\n<!-- PLUGIN SPIPLISTES -->\n";
	$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "javascript/spiplistes_jQuery.js\"></script>\n";
	if ($exec=="sl_courrier_rediger") {
//		. "<!-- ".__plugin_get_real_prefix(true)." v.: ".__plugin_meta_version(__plugin_get_real_prefix(true))." -->\n"
	/*
	$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "js/datePicker.js\"></script>\n";
	$flux .= "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "js/datePicker_myScripts.js\"></script>\n";
	$flux .= "<link rel=\"stylesheet\" href=\"" ._DIR_PLUGIN_SPIPLISTES . "css/datePicker.css\" type=\"text/css\" />\n";
	*/
	}
	$flux .="<!-- / PLUGIN SPIPLISTES -->\n\n";
	return ($flux);
}

?>