<?php

function simplec_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/simplec.css') . '" type="text/css" media="all" />' . "\n";
	$flux .= '<script type="text/javascript" src="' . find_in_path('js/clipboard.min.js') . '"></script>' . "\n";

	// produire le js depuis un squelette pour pouvoir traduire les libellés
	$js   = produire_fond_statique('js/simplec.js');
	$flux .= '<script type="text/javascript" src="' . $js . '"></script>' . "\n";

	return $flux;
}

