<?php

/*
 * Plugin Livrables
 * Licence GPL (c) 2011 Cyril Marion
 *
 */


function balise_TITRE_PROJET($p) {
	$id_projet = interprete_argument_balise (1, $p);
	$p->code = "trouve_titre(".$id_projet.")";
	$p->statut = 'php';
	return $p;
}
function trouve_titre($id_projet) {
	$titre = sql_getfetsel("titre","spip_projets", "id_projet=" . intval($id_projet));
	if (!empty($titre))
		return $titre;
	return '';
}


?>