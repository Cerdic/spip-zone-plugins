<?php
function ancres_douces_insert_head($flux){

// Inclusion des scripts jquery
$flux .= '<script src="'.url_absolue(find_in_path("js/jquery.scrollto.js")).'" type="text/javascript"></script>';
$flux .= '<script src="'.url_absolue(find_in_path("js/jquery.localscroll.js")).'" type="text/javascript"></script>';

// Code d'init
$flux .= '<script type="text/javascript">/* <![CDATA[ */
function ancre_douce_init() {if(typeof jQuery.localScroll=="function")jQuery.localScroll({hash:true});}
if(window.jQuery)jQuery(document).ready(ancre_douce_init);
onAjaxLoad(ancre_douce_init);
/* ]]> */</script>';

return $flux;
}
?>

