<?php
// Reserve pour une utilisation future si besoin. Pour l'instant, pas de besoin en prive, donc pas de declaration dans plugin.xml
function slideshow_header_prive($flux){
$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_SLIDESHOW.'styles/imageflow.css" type="text/css" media="all" />';
return $flux;
}

// Insertion des css de slideshow

function slideshow_insert_head($flux){
$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_SLIDESHOW.'styles/imageflow.css" type="text/css" media="all" />';

// Insertion des js de slideshow

$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_SLIDESHOW.'js/imageflow.js"> </script>';
$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_SLIDESHOW.'js/jquery-ui-1.7.1.custom.min.js"> </script>';
$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_SLIDESHOW.'js/execute.js"> </script>';

return $flux;
}
?>
