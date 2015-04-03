<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function accesrestreintvideos_accesrestreint_afficher_document($flux) {
	if ($flux['data']['mime_type'] == 'text/html') {
		$flux['data']['inclus'] = 'embed';
	}
	
	return $flux;
}
