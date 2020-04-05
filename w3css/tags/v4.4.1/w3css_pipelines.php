<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function w3css_insert_head_css($flux){

		if ($f = produire_fond_statique('css/w3.css', array('format'=>'css')) ) {
			$f = (lire_config('w3css/extend') == 'on') ? scss_css($f) : $f;
			$flux .= "\n".'<link rel="stylesheet" href="' . $f . '" type="text/css" />'."\n";
		}

    return $flux;
}
