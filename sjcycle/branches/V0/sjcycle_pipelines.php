<?php
function sjcycle_insert_head($flux){
	$conf_jcycle = lire_config('sjcycle');
	$javascript = find_in_path('javascript/jquery.cycle.all.min.js');
	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.cycle.all.min.js')).'" type="text/javascript"></script>';
	$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/sjcycle.css')).'" type="text/css" media="screen" />';
	$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('javascript/jquery.tooltip.css')).'" type="text/css" media="all" />';
	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.tooltip.js')).'" type="text/javascript" charset="utf-8"></script>';
	$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('javascript/jquery.fancybox/jquery.fancybox.css')).'" type="text/css" media="all" />';
	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.fancybox/jquery.fancybox-1.2.1.js')).'" type="text/javascript"></script>';
//	$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.fancybox/fancybox.js')).'" type="text/javascript"></script>';
//	$flux .='<script src="'.url_absolue(find_in_path('javascript/jquery.fancybox/jquery.easing.1.3.js')).'" type="text/javascript" charset="utf-8"></script>';
//	$flux .='<script src="'.url_absolue(find_in_path('javascript/jquery.fancybox/jquery.fancybox-1.2.1.pack.js')).'" type="text/javascript" charset="utf-8"></script>';
	/* options */
	$flux .= "\n".'<style type="text/css" media="all">
.'.$conf_jcycle["sjcycle_class"].'{
padding:0px;
margin:auto;
background:#'.$conf_jcycle["sjcycle_background"].';/* diaporama documents images cycle */
width:'.$conf_jcycle["sjcycle_width"].'px;
height:'.$conf_jcycle["sjcycle_height"].'px;
display:block;
}
</style>
<script type="text/javascript">
<!--
jQuery(document).ready(function(){
	$(".dsjcycle").cycle({ 
		fx:     "'.$conf_jcycle["sjcycle_fx"].'", // effect
	    timeout:       '.$conf_jcycle["sjcycle_timeout"].',  // milliseconds between slide transitions (0 to disable auto advance) 
	    speed:        '.$conf_jcycle["sjcycle_speed"].',  // speed of the transition (any valid fx speed value) 
	    sync:          '.$conf_jcycle["sjcycle_sync"].',     // true if in/out transitions should occur simultaneously 
	    pause:        '.$conf_jcycle["sjcycle_pause"].',     // true to enable "pause on hover" 
	    cleartype:  1 // enable cleartype corrections 
	    });
});
-->		
</script>
'; 
	
	return $flux;
}

?>