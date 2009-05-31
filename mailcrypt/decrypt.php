<?php
	function cryptm_insert_head($flux){
		$flux .= "<script type='text/javascript' src='".find_in_path('decrypt.js')."'></script>";
		return $flux;
	}
?>
