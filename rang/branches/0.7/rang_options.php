<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// pour compat avec l'ancienne meta rang_objets, maintenant dans le casier rang
include_spip('inc/config');
if ($objets = lire_config('rang_objets')) {
	ecrire_config('rang/rang_objets', $objets);
	effacer_config('rang_objets');
}