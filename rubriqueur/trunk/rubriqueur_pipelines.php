<?php

function rubriqueur_header_prive($flux = '') {
	$css  = produire_fond_statique('css/rubriqueur.css');
	$flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";

	return $flux;
}