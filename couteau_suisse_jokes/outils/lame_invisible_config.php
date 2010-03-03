<?php

function outils_lame_invisible_config_dist() {
	
	add_outil(array(
		'id'          => "lame_invisible",
		'nom'         => _T("blagoulames:invisible_nom"),
		'description' => _T("blagoulames:invisible_description"),
		'categorie'   => _T('blagoulames:categorie'),
		'code:jq'     => "
			jQuery('#lame_invisible').remove();
		",
	));
	
}
?>
