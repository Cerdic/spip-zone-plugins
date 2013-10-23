<?php
function seminaire_insert_head($flux){
    $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('inc-css-seminaire.css.html').'" type="text/css" media="all" />'."\n";
	$flux .="\n".'<link rel="stylesheet" href="'._DIR_LIB_SEMINAIRE.'fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="all" />'."\n";
    return $flux;
}
?>