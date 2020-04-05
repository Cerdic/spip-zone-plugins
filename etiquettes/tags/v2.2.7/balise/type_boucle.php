<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_TYPE_BOUCLE_dist($p) {
	$type = $p->boucles[$p->id_boucle]->id_table;
	$p->code = $type ? "objet_type('$type')" : "'balise_hors_boucle'";
	return $p;
}

