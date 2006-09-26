<?php

function ajaxcallback_insert_head($flux){
			return $flux.
			'<script src="'._DIR_JAVASCRIPT.'layer.js" type="text/javascript"></script>'.
			'<script src="'.find_in_path('ajaxCallback.js').'" type="text/javascript"></script>';
	}

?>
