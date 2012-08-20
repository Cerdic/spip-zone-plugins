<?php
function seminaire_insert_head_css($flux) {
    $css = find_in_path('styles/calendrier-seminaire.css');
    $flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
    return $flux;
}
?>