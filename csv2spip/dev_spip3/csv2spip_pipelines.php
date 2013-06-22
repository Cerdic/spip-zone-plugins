<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function csv2spip_header_prive($flux){
    $css   = find_in_path('csv2spip.css');
    $flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

    return $flux;
}


?>



