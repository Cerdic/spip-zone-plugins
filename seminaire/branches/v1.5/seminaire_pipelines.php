<?php
function seminaire_insert_head($flux){
    $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('inc-css-seminaire.css.html').'" type="text/css" media="all" />'."\n";
    return $flux;
}
?>