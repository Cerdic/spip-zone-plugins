<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des pages
function balise_NOIZETIER_LISTE_PAGES_dist($p) {
	$page = interprete_argument_balise(1, $p);
	if (isset($page)) {
		$page = str_replace('\'', '"', $page);
		$information = interprete_argument_balise(2, $p);
		$information = isset($information) ? str_replace('\'', '"', $information) : '""';
		$p->code = "noizetier_page_informer($page, $information)";
	} else {
		$p->code = "noizetier_page_repertorier()";
	}

	return $p;
}
