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

	$flux .= ""
		. "\n\n<!-- PLUGIN SPIPLISTES GADGETS v.: ".__plugin_real_version_get()." -->\n"
		. "<script src='".url_absolue(find_in_path('javascript/spiplistes_gadgets.js'))."' type='text/javascript'></script>\n"
	;

	if(in_array($exec, array(
		_SPIPLISTES_EXEC_ABONNES_LISTE
		, _SPIPLISTES_EXEC_COURRIER_EDIT
		, _SPIPLISTES_EXEC_COURRIERS_LISTE
		, _SPIPLISTES_EXEC_COURRIERS_LISTE
		, _SPIPLISTES_EXEC_LISTES_LISTE
		, _SPIPLISTES_EXEC_MAINTENANCE
		, 'auteur_infos' // liste-listes
		)
		)
	) {
		
		$flux .= "\n\n<!-- PLUGIN SPIPLISTES v.: ".__plugin_real_version_get()." -->\n"
					. "<link rel='stylesheet' href='"._DIR_PLUGIN_SPIPLISTES."spiplistes_style.css' type='text/css' media='all' />\n"
					;

		switch($exec) {
			case _SPIPLISTES_EXEC_COURRIER_EDIT:
				$flux .= ""
					. "<script type=\"text/javascript\" src=\"" ._DIR_PLUGIN_SPIPLISTES . "javascript/spiplistes_courrier_edit.js\"></script>\n"
					. "<link rel='stylesheet' href='".url_absolue(find_in_path('img_pack/date_picker.css'))."' type='text/css' media='all' />\n"
					. "<script src='".url_absolue(find_in_path('javascript/datepicker.js'))."' type='text/javascript'></script>\n"
					. "<script src='".url_absolue(find_in_path('javascript/jquery-dom.js'))."' type='text/javascript'></script>\n"
					. "<meta http-equiv='expires' content='0'>\n"
					. "<meta http-equiv='pragma' content='no-cache' />\n"
					. "<meta http-equiv='cache-control' content='no-cache' />\n"
					;
				break;
			case _SPIPLISTES_EXEC_COURRIERS_LISTE:
				break;
		}

		$flux .="<!-- / PLUGIN SPIPLISTES -->\n\n";

	}
	return ($flux);
}

?>