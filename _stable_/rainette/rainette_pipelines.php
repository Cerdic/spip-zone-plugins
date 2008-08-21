<?php
// Reserve pour une utilisation future si besoin. Pour l'instant, pas de besoin en prive, donc pas de declaration dans plugin.xml
function rainette_header_prive($flux){
$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_RAINETTE.'styles/rainette.css" type="text/css" media="all" />';
return $flux;
}

// Insertion des css de Rainette
function rainette_insert_head($flux){
$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_RAINETTE.'styles/rainette.css" type="text/css" media="all" />';
return $flux;
}
?>
