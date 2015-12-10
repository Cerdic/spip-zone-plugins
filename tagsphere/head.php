<?php

function tagsphere_insert_head($flux) {
	// Voir pour l'utilisation de https://github.com/dynamicguy/tagcloud/ qui semble être son nouveau nom.
	$flux .= '<script type="text/javascript" src="'._DIR_PLUGIN_TAGSPHERE.'js/jquery.tagSphere.js"></script>';

	return $flux;
}
