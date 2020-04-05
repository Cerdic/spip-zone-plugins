<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function lazyload_insert_head($flux){
	if (function_exists('lire_config')) {
		
		$selecteur = lire_config("lazyload/selecteur","img");
		$distance = lire_config("lazyload/distance","0");
		$event = lire_config("lazyload/event");
		if ($event != "")
			$event = 'event : "'.$event.'",';
		$effect = lire_config("lazyload/effect");
		if ($effect != "")
			$effect = 'effect : "'.$effect.'",';
		$flux .= '<style>.lazy { display: none }</style>';
		$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_LAZYLOAD.'javascript/jquery.lazyload.js"></script>';
		$flux .= '<script type="text/javascript">
					$(function(){
						$("'.$selecteur.'.lazy").show().lazyload({
							threshold : '.$distance.',
							placeholder : "'._DIR_PLUGIN_LAZYLOAD.'images/grey.gif",
							'.$event.'
							'.$effect.'
						});
					});
				</script>';
	}
	return $flux;
}
function lazyload_insert_head_prive($flux){
	$flux .= '<style>.lazy { display: none; }</style>';
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_LAZYLOAD.'javascript/jquery.lazyload.js"></script>';
	$flux .= '<script type="text/javascript">$(function(){ $("img.lazy").show().lazyload(); });</script>';
	return $flux;
}

?>
