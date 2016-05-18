<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Balise GEOMETRY_MAP pour afficher le champ bounds de la table spip_cartes au format WKT
 *
 * @param $p
 * @return mixed
 */
function balise_geometry_map_dist($p) {
	$p->code = '$Pile[$SP][\'geometry_map\']';
	return $p;
}
