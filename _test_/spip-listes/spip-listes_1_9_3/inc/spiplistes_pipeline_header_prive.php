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

	if(in_array($exec, array(
		_SPIPLISTES_EXEC_COURRIER_EDIT
		, _SPIPLISTES_EXEC_COURRIER_REDAC
		)
		)
	) {
		
		$flux .="\n\n<!-- PLUGIN SPIPLISTES v.: ".__plugin_get_real_version()." -->\n";

		switch($exec) {
			case _SPIPLISTES_EXEC_COURRIER_EDIT:
			case _SPIPLISTES_EXEC_COURRIER_REDAC:
				$flux .= ""
					. "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "javascript/spiplistes_jQuery.js\"></script>\n"
					. "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />\n"
					. "<script src='".url_absolue(find_in_path('javascript/datepicker.js'))."' type='text/javascript'></script>\n"
					. "<script src='".url_absolue(find_in_path('javascript/jquery-dom.js'))."' type='text/javascript'></script>\n"
					;
				break;
		}

		$flux .="<!-- / PLUGIN SPIPLISTES -->\n\n";

	}
	return ($flux);
}

?>