<?php

// --------------------------------
// partie privee
// --------------------------------

function googleajaxsearch_ajouterBoutons($boutons_admin) {
	// admin ?
	if ($GLOBALS['connect_statut'] == "0minirezo") {
	    $boutons_admin['configuration']->sousmenu['googleajaxsearch']= new Bouton(
		    _DIR_PLUGIN_GOOGLEAJAXSEARCH.'img_pack/google_but.png', _T('googleajaxsearch:config_plug'));
	}
	return $boutons_admin;
}


// --------------------------------
// partie publique
// --------------------------------
function googleajaxsearch_insert_head($flux){
    
    $adresse_site = $GLOBALS['meta']['adresse_site'];
    $google_key = $GLOBALS['meta']['google_key'];
		
		$flux.='
    <!-- google ajax api search -->
    <link href="http://www.google.com/uds/css/gsearch.css" type="text/css" rel="stylesheet"/>
    <script src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&key='.$google_key.'" type="text/javascript"></script>';
	
	return $flux;
}
	
?>
