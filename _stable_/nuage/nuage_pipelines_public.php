<?php

function nuage_insert_head($flux) {
	
	include find_in_path('nuage_options.php');
	
	echo $nuage_mode;
	
	if ($nuage_mode == 'accessible') {
		$contact_css .= "\n<style>span.nuage_frequence{ display: block; float: left; height: 0; overflow: auto; width: 0;}</style>\n";
		if (strpos($flux,'<head')!==FALSE) return preg_replace('/(<head[^>]*>)/i', "\n\$1".$contact_css, $flux, 1);
		else return $contact_css.$flux;
	}
	else return $flux;
}

?>