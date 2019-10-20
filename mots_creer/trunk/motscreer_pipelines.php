<?php

function motscreer_header_prive($flux = '') {
	$css  = produire_fond_statique('css/motscreer.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $flux;
}