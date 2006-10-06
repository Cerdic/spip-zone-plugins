<?php

function personnalisation_insert_head($flux) {

	if(isset($_COOKIE['spip_personnalisation_use']) && $_COOKIE['spip_personnalisation_use'] !='') { // si un cookie est posé
		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/zoomlayout.php" />'. "\n";
	}
	if(isset($_COOKIE['spip_personnalisation_navigationmode']) && $_COOKIE['spip_personnalisation_navigationmode'] =='autolink') {
		$flux .= '<script src="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/js/autolink.js" type="text/javascript"></script>'. "\n";
	}else if(isset($_COOKIE['spip_personnalisation_navigationmode']) && $_COOKIE['spip_personnalisation_navigationmode'] =='autofocus') {
		$flux .= '<script src="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/js/autofocus.js" type="text/javascript"></script>'. "\n";
	}

	return $flux;
}

?>