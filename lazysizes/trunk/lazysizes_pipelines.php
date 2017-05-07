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

/*
 * function lazysizes_insert_head_public
 * @param $flux
 */

function lazysizes_insert_head_public($flux) {
	include_spip('inc/config');
	// Addons
	$active_addons = lire_config('lazysizes/addons');
	$ls_addons = lazysizes_addons();
	
	if (is_array($active_addons)) {
		foreach($active_addons as $addon => $state){
			if(array_key_exists($addon, $ls_addons)){
				$file = timestamp(find_in_path('javascript/addons/'.$addon.'/'.$ls_addons[$addon].'.js'));
				$flux .= "<script type='text/javascript' src='$file' ></script>\n"; 
			}
		}
	}
			
	$lazysizes = timestamp(find_in_path('javascript/lazysizes.js'));	
	$flux .= "<script type='text/javascript' src='$lazysizes' ></script>\n";

	return $flux;
}