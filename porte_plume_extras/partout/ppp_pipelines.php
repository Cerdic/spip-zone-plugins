<?php

// Recalculer le cache si la config du site change
$GLOBALS['marqueur'] .= ":".md5($GLOBALS['meta']['ppp']); // Sur un conseil de Cedric : http://permalink.gmane.org/gmane.comp.web.spip.zone/6258

//Appel du pipeline qui ajoute des barres de porte-plume partout dans Descriptif, chapo, PS, Bio et Descriptif du site
function ppp_insert_head_prive($flux){
	$js = generer_url_public('barre_generalisee.js');
	$flux .= "<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}

?>