<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des noisettes disponibles
function balise_NOIZETIER_LISTE_NOISETTES_dist($p)
{
	$type = interprete_argument_balise(1, $p);
	if (is_null($type)) {
		$p->code = 'noizetier_lister_noisettes()';
	} else {
		$p->code = "noizetier_lister_noisettes($type)";
	}

	return $p;
}
