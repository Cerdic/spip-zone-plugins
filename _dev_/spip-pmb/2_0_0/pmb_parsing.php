<?php

function pmb_traitement_flux($tableau)
{
	
	return $tableau;
}

function pmb_tester_flux($rss)
{
	//teste si il sagit d'un flux en provenance de PMB

	// recherche de la balise <generator>
	return (preg_match( '`<generator>(.)*[Pp][Mm][Bb](.)*</generator>`', $rss));
}
	
?>