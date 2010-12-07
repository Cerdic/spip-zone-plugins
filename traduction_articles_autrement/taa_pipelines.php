<?php

function taa_affiche_gauche($flux) {


    return $flux;
}

function taa_header_prive($flux){

    $flux .= '<link rel="stylesheet" href="'.chemin('css/taa_styles.css').'" type="text/css" media="all" />';
 	return $flux;	

 }
?>
