<?php

function balise_GOOGLEPLUS1 ($p) {

	$googleplus1_taille = lire_config('googleplus1/googleplus1_taille');
	$p->code = "'<g:plusone size=\"$googleplus1_taille\"></g:plusone>'";
	return $p;

}

?> 
