<?php

function personnalisation_insert_head($flux) {

	if(isset($_COOKIE['spip_personnalisation_use']) && $_COOKIE['spip_personnalisation_use'] !='') { // si un cookie est posé
		$flux .= '<link rel="stylesheet" type="text/css" media="screen" href="' ._DIR_PLUGIN_FORMULAIRE_PERSONNALISATION . '/css/zoomlayout.php" />'. "\n";
	}
	return $flux;
}

?>