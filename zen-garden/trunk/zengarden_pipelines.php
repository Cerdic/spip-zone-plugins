<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function zengarden_header_prive($flux){
	if (_request('exec') == 'zengarden')
		$flux .='
		<script src="'.find_in_path('javascript/jquery.qtip-1.0.0-rc3.js').'" type="text/javascript"></script>
		<script src="'.find_in_path('javascript/jquery.qtip.activate.js').'" type="text/javascript"></script>';

	return $flux;
}

?>
