<?php

function sjcycle_insert_head($flux){
	include_spip('sjcycle_fonctions');
	// Modif Yffic : Si la config existe deja, on ne l'ecrase pas
	// Si evolution de la config, les nouveaux champs sont rajoutes
	$conf_jcycle = lire_config('sjcycle');
	if (!is_array($conf_jcycle)) {
		$conf_jcycle = array();
	}
	$conf_jcycle = array_merge(init_sjcycle_default('default_value_list'),$conf_jcycle) ;
	ecrire_config('sjcycle', serialize($conf_jcycle));

	$javascript = find_in_path('javascript/jquery.cycle.all.min.js');

	
	$flux .="\n".'<script src="'.url_absolue($javascript).'" type="text/javascript"></script>';
	$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('styles/sjcycle.css')).'" type="text/css" media="screen" />';
	if($conf_jcycle["sjcycle_tooltip"]) {/* Modif Yffic*/
		$flux .="\n".'<link rel="stylesheet" href="'.url_absolue(find_in_path('javascript/jquery.tooltip.css')).'" type="text/css" media="all" />';
		$flux .="\n".'<script src="'.url_absolue(find_in_path('javascript/jquery.tooltip.js')).'" type="text/javascript" charset="utf-8"></script>';
	}
	/* options */
	$flux .= "\n".'<style type="text/css" media="all">
.'.$conf_jcycle["sjcycle_class"].'{
padding:0px;
margin:auto;
background:#'.$conf_jcycle["sjcycle_background"].';/* diaporama documents images cycle */
background:'.($conf_jcycle["sjcycle_background"]=="transparent"?"":"#").$conf_jcycle["sjcycle_background"].';
width:'.$conf_jcycle["sjcycle_width"].'px;
height:'.$conf_jcycle["sjcycle_height"].'px;
display:block;
/*overflow: hidden;*/ /* Modif Yffic*/
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