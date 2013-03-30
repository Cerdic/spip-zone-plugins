<?php
function seminaire_insert_head($texte){
    $texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('inc-css-seminaire.css.html').'" media="all" />'."\n";
    return $texte;
}
?>