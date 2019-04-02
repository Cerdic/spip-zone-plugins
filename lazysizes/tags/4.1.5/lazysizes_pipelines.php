<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/*
 * function lazysizes_ieconfig_metas
 * @url http://contrib.spip.net/Importeur-Exporteur-de-configurations-documentation
 */

function lazysizes_ieconfig_metas($table){
        $table['lazysizes']['titre'] = _T('lazysizes:lazysizes_titre');
        $table['lazysizes']['icone'] = 'lazysizes-16.png';
        $table['lazysizes']['metas_serialize'] = 'lazysizes';
        return $table;
}
/**
 * Inserer les styles
 *
 * @param $head
 *
 * @return string
 */
function lazysizes_insert_head_css($head) {
	include_spip('inc/config');
	if (lire_config('lazysizes/options/css', 0)) {
		$head .= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/lazysizes.css') . '" />' . "\n";
	}

	return $head;
}
/*
 * function lazysizes_header_prive
 * @param $flux
 */
function lazysizes_header_prive($flux){
	
	$flux .= '<link rel="stylesheet" type="text/css" href="' . find_in_path('css/lazysizes.css') . '" />' . "\n";
	
	$flux .= lazysizes_insertion_js();
	
	return $flux;
}


/*
 * function lazysizes_insert_head_public
 * @param $flux
 */
function lazysizes_insert_head_public($flux) {
	return lazysizes_insertion_js($flux);
}