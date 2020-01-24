<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_calculer_taille_cache_responsive() {

		include_spip("inc/calculer_cache_responsive");
		$retour = image_responsive_calculer_cache();

}

