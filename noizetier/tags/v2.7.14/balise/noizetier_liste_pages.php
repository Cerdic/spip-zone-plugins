<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des pages
function balise_NOIZETIER_LISTE_PAGES_dist($p)
{
	$page_specifique = interprete_argument_balise(1, $p);
	$p->code = "noizetier_lister_pages($page_specifique)";

	return $p;
}
