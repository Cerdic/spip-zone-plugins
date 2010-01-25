<?php

function outils_lame_en_table_config_dist() {
	
	add_outil(array(
		'id'          => "lame_en_table",
		'nom'         => _T("blagoulames:en_table_nom"),
		'description' => _T("blagoulames:en_table_description"),
		'categorie'   => 'blagoulames',
		'code:jq'     => "
			jQuery('table:not(.spip)').remove();
			",
	));
	
}
?>
