<?php

	function LoremIpsum_insert_head($flux){
			$js = '<script src=\''.url_absolue(find_in_path('jquery.lorem.js')).'\' type=\'text/javascript\'></script>';

		if (strpos($flux,'<head')!==FALSE)
			return preg_replace('/(<head[^>]*>)/i', "\n\$1".$js, $flux, 1);
		else 
			return $flux.$js;
	}

?>
