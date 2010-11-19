<?php

function jit_insert_head($flux){
	
	$flux .="\n".'<script src="'._DIR_LIB_JIT.'jit.js" type="text/javascript"></script>'."\n";
	$flux .="\n".'<!--[if IE]><script src="'._DIR_LIB_JIT.'Extras/excanvas.js" type="text/javascript"></script><![endif]-->'."\n";
	
	return $flux;
}

?>
