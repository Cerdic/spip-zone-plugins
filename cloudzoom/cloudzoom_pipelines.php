<?php

function cloudzoom_insert_head($flux){
	$js = find_in_path('js/cloud-zoom.1.0.2.min.js');
	$flux	.= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('cloudzoom.css').'" media="all" />'."\n"

		.  "<script type='text/javascript' src='$js'></script>\n";
	return $flux;

}



function cloudzoom_ajouter_boutons($flux) {
	// si on est admin
	if (autoriser('configurer','cloudzoom')) {
		$menu = "configuration";
		$icone = "cloudzoom-32.png";

		// on voit le bouton dans la barre "configuration"
		$flux[$menu]->sousmenu['cfg&cfg=cloudzoom']= new Bouton(
		_DIR_PLUGIN_CLOUDZOOM.$icone,  // icone
		_T('cloudzoom:cloudzoom'));
	}
	return $flux;
}
?>




