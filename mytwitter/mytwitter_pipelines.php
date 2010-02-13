<?php

function mytwitter_insert_head($flux){
	$flux	.= '<link rel="stylesheet" type="text/css" href="'.generer_url_public('mytwitter.css').'" media="all" />'."\n"

		.  "<script type='text/javascript' src='http://twitter-friends-widget.googlecode.com/files/jquery.twitter-friends-1.0.min.js'></script>\n";
	return $flux;

}



function mytwitter_ajouter_boutons($flux) {
	// si on est admin
	if (autoriser('configurer','mytwitter')) {
		$menu = "configuration";
		$icone = "twitter-32.png";

		// on voit le bouton dans la barre "configuration"
		$flux[$menu]->sousmenu['cfg&cfg=mytwitter']= new Bouton(
		_DIR_PLUGIN_MYTWITTER.$icone,  // icone
		_T('mytwitter:my twitter'));
	}
	return $flux;
}
?>




