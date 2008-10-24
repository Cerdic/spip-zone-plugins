<?php
function ancres_douces_insert_head($flux){

// Inclusion des scripts jquery
$flux .= '<script src="'.url_absolue(find_in_path("js/jquery.ancresdouces.js")).'" type="text/javascript"></script>';

// Code d'init
$flux .= '<script type="text/javascript"><!--
if(window.jQuery)jQuery(document).ready(function(){
if(typeof jQuery.fn.ancresdouces=="function") jQuery("a[href*=#]").ancresdouces();
});
// --></script>';

return $flux;
}
?>

