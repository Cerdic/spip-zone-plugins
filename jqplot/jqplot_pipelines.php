<?php
function jqplot_header_prive($flux){
 $flux .= "<script type='text/javascript' src='" . _DIR_JQPLOT_JS . "jquery.jqplot.js'></script>\n";
 $flux .= "<link rel='stylesheet' type='text/css' media='all' href='" . _DIR_JQPLOT_JS . "jquery.jqplot.css' />\n";

 return $flux;
}

function jqplot_insert_head($flux){
 $flux = jqplot_header_prive($flux) ;

 return $flux;
}
?>