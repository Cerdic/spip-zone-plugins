<?php

// S�curit�
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des icones
function balise_NOIZETIER_LISTE_ICONES_dist($p)
{
	$p->code = 'noizetier_lister_icones()';

	return $p;
}
