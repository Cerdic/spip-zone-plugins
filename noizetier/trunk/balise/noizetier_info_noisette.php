<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de description d'une noisette particulière
function balise_NOIZETIER_INFO_NOISETTE_dist($p)
{
	$noisette = interprete_argument_balise(1, $p);
	$p->code = "noizetier_info_noisette($noisette)";

	return $p;
}
