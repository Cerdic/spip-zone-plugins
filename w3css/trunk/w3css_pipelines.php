<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function w3css_insert_head_css($flux){

		if ($f = produire_fond_statique('css/w3.css', array('format'=>'css')) ) {
			$flux .= "<link rel='stylesheet' href='" . $f . "' type='text/css' media='all' />\n";
		}

    return $flux;
}
