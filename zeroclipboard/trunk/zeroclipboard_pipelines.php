<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function zeroclipboard_header_prive($flux){
	$flux .= '
<script type="text/javascript" src="'.produire_fond_statique('zeroclipboard.js').'"></script>
';
	return $flux;
}

function zeroclipboard_insert_head($flux){
	$flux .= '
<script type="text/javascript" src="'.produire_fond_statique('zeroclipboard.js').'"></script>
';
	return $flux;
}


function zeroclipboard_jquery_plugins($plugins){
	$plugins[] = find_in_path(_DIR_LIB_ZEROCLIPBOARD.'src/javascript/ZeroClipboard.js');
	$plugins[] = 'javascript/spip_zeroclipboard.js';

	return $plugins;
}
?>