<?php
/*
 * GeoForms
 * Geolocalistion dans les tables et les formulaires
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function geoforms_header_prive($flux){
	$flux .= "<link rel='stylesheet' href='"._DIR_PLUGIN_GEOFORMS."geoforms.css' type='text/css' media='all' />\n";
	return $flux;
}


function geoforms_ajouter_boutons($boutons_admin) {
	if (autoriser('administrer','geoforms')) {
	    $boutons_admin['configuration']->sousmenu['geoforms_config']= new Bouton(
		    _DIR_PLUGIN_GEOFORMS.'img_pack/geoforms.png', _T('geoforms:configuration'));
	}
	return $boutons_admin;
}


/* 
	Inserer les scripts dans le public
	
	INFO : Fonction récupérée du plugin "googlemap_api" (googlemap_api/inc/geomap_pipeline.php)
	et modifiée pour faire afficher la carte GoogleMap dans la partie public du site
	La fonction appelle un script du plugin "googlemap_api" qui inclue le JavaScript
	"geomap.js" dans le header de la page, ce qui permet d'afficher la carte GoogleMap
	dans la partie public (à la place d'un cadre vide!)
*/
function geoforms_affichage_final($flux){
	
	// SI on trouve le mot "geomap" dans la page (class CSS du div contenant la carte GoogleMap)
	// ET que la clé API est définie...
    if (
		(strpos($flux, 'geomap') == true) 
		&& (lire_config('geomap/cle_api'))
	){
		$incHead = '';
		$geomap_script_init = charger_fonction('geomap_script_init','inc');
		$incHead .= $geomap_script_init();
		
        return substr_replace($flux, $incHead, strpos($flux, '</head>'), 0);
		
    } else {
		return $flux;
	}
}

?>