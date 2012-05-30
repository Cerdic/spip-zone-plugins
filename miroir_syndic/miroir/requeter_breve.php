<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2012 Fil, Cedric
 * Distribue sous licence GPL
 *
 */

// un nouvel article : le creer
function miroir_requeter_breve_dist() {

	return array(
		// SELECT
		"s.*, o.id_breve AS id,o.id_rubrique AS rubrique_objet, src.nom_site as nom_site,
		src.id_rubrique AS id_rubrique, src.id_secteur AS id_secteur",
		// FROM
		"spip_syndic_articles AS s
		LEFT JOIN spip_breves AS o
		ON s.url = o.lien_url
		LEFT JOIN spip_syndic AS src
		ON s.id_syndic = src.id_syndic",
	);
}