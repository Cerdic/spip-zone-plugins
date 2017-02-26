<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Cette balise renvoie le tableau de la liste des pages
function balise_NOIZETIER_PAGE_INFOS_dist($p) {
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

// Cette balise renvoie le tableau de la liste des objets possédant des noisettes configurées
function balise_NOIZETIER_OBJET_INFOS_dist($p) {
	$objet = interprete_argument_balise(1, $p);
	if (isset($objet)) {
		$objet = str_replace('\'', '"', $objet);
		$id_objet = interprete_argument_balise(2, $p);
		$id_objet = isset($id_objet) ? $id_objet : '0';
		$information = interprete_argument_balise(3, $p);
		$information = isset($information) ? str_replace('\'', '"', $information) : '""';
		$p->code = "noizetier_objet_informer($objet, $id_objet, $information)";
	} else {
		$p->code = "noizetier_objet_informer()";
	}

	return $p;
}

// Cette balise renvoie le tableau de la liste des blocs
function balise_NOIZETIER_BLOC_INFOS_dist($p) {
	$bloc = interprete_argument_balise(1, $p);
	if (isset($bloc)) {
		$bloc = str_replace('\'', '"', $bloc);
		$information = interprete_argument_balise(2, $p);
		$information = isset($information) ? str_replace('\'', '"', $information) : '""';
		$p->code = "noizetier_bloc_informer($bloc, $information)";
	} else {
		$p->code = "noizetier_bloc_repertorier()";
	}

	return $p;
}
