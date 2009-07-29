<?php




function assoc_affiche_droite($flux){
	
	if (_request('exec') == 'articles' || _request('exec') == 'articles_edit') {	
		if (_request('id_article') > 0) {
			
			$id = intval(_request('id_article'));
			$flux['data'] .="<p>salut admin</p>";
			
		}
	}
	
	return $flux;
}

function assoc_header_prive($flux){
	
	/*
		// rajout de la css
		$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_OMM."css/omm_admin.css' type='text/css' />";
		$flux .= "<link rel='stylesheet' type='text/css' href='"._DIR_PLUGIN_ASSOC."css/assoc_admin.css' />";
		$flux .= '<link rel="stylesheet" href="../css/ui.theme.css" type="text/css"  />';
		$flux .= '<link rel="stylesheet" href="../css/ui.datepicker.css" type="text/css"  />';
			
		// et des js
		$flux .= "<script type='text/javascript' src='../js/jquery-ui-1.7.js'></script>";
		$flux .= "<script type='text/javascript' src='../js/ui.datepicker-fr.js'></script>";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_OMM."js/omm.js'></script>";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/assoc.js'></script>";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/forms.js'></script>";
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/assoc_admin.js'></script>";


		// on renvoie le plugin forms quoi qu'il arrive
		$flux .= "<script type='text/javascript' src='"._DIR_PLUGIN_ASSOC."js/forms.js'></script>";*/
		return $flux;
}

?>