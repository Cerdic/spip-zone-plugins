<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_GOOGLEPLUS1_dist ($p) {

	include_spip('inc/config');
	$googleplus1_taille = lire_config('googleplus1/googleplus1_taille');
	$p->code = "'<g:plusone size=\"$googleplus1_taille\"></g:plusone>'";
	return $p;

}

?> 
