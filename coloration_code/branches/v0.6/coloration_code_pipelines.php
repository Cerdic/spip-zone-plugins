<?php

function coloration_code_header_prive_css($css){
	$css2=find_in_path('prive/themes/spip/coloration_code.css');
	return $css.="\n<link rel='stylesheet' type='text/css' href='$css2' id='csscoloration_code'> \n";
}
?>