<?php

function kconf_afficher_contenu_objet($flux) {
	$type = $flux['args']['type'];
	if ($type=='article' || $type=='rubrique') { // <>< pas compatible avec d'autres objets futur
		include_spip('exec/kconf_admin');
		$flux['data'] = exec_kconf_admin_dist().$flux['data'];
	}
	return $flux;
}

// insertion javascript pour color-picker et documentation
function kconf_header_prive($info) {
	$info .= '<script type="text/javascript" src="'._DIR_PLUGIN_KCONF.'farbtastic/farbtastic.js"></script>';
	$info .= '<link id="debug" rel="stylesheet" href="'._DIR_PLUGIN_KCONF.'kconf.css" type="text/css" />';
	$info .= '<link rel="stylesheet" href="'._DIR_PLUGIN_KCONF.'farbtastic/farbtastic.css" type="text/css" />';
// 	$info .= '<script type="application/javascript" src="'._DIR_PLUGIN_KCONF.'javascript/modal.js"></script>';
	return $info;
}

?>