<?php

function outils_lame_de_fond_config_dist() {
	
	add_outil(array(
		'id'          => "lame_de_fond",
		'nom'         => _T("blagoulames:de_fond_nom"),
		'description' => _T("blagoulames:de_fond_description"),
		'categorie'   => _T('blagoulames:categorie'),
		'code:jq'     => "
			jQuery('body').css('background-color', 'rgb(' +
				Math.floor(Math.random()*255) + ',' +
				Math.floor(Math.random()*255) + ',' +
				Math.floor(Math.random()*255) + ')');
			",
	));
	
}

?>
