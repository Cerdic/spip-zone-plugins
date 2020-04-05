<?php

function balise_MULTIDOMAINE_SITE_PRINCIPAL_dist($p) {
	$p->code = 'lire_config("multidomaines/defaut/url")';

	return $p;
}


