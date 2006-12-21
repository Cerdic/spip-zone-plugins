<?php

function JQuery_insert_head($flux){
		if (!_request('jqdb'))
			$js = '<script src="'.find_in_path('jquery-1.0.4.pack.js').'" type="text/javascript"></script>';
		else
			$js = '<script src="'.find_in_path('jquery-1.0.4.js').'" type="text/javascript"></script>';
			$js .= '<script src="'.find_in_path('img_pack/layer.js').'" type="text/javascript"></script>';
			$js .= '<script src="'.find_in_path('ajaxCallback.js').'" type="text/javascript"></script>';

			$js .= '<script src="'.find_in_path('form.js').'" type="text/javascript"></script>';
			
			$js .= '<script src="'.find_in_path('jq-corner.js').'" type="text/javascript"></script>';

		if (strpos($flux,'<head')!==FALSE)
			return preg_replace('/(<head[^>]*>)/i', "\n\$1".$js, $flux, 1);
		else 
			return $js.$flux;
	}

?>
