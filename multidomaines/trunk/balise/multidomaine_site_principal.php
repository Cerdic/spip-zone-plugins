<?php

function balise_MULTIDOMAINE_SITE_PRINCIPAL_dist($p) {
	$p->code = 'lire_config("multidomaines/editer_url")';
	return $p;
}


