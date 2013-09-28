<?php
/**
 * JAZ - Joindre Automatiquement une Zone
 * Cyril MARION (c)2012 GPL
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chercher les zones définies dans mes_options et ajouter l'auteur à ces zones
 * @param $auteur
 */
function jaz_ajouter_auteur_zones($auteur)
{

	// On cherche les Zones Auto Jointes :
	$define = (defined('_ZONES_AUTO_JOINTES')) ? _ZONES_AUTO_JOINTES : '';
	$zones  = explode(':', $define);

	// Pour chacune de ces zones
	while (list(, $l) = @each($zones)) {

		// On ajoute l'auteur à la zone
		sql_insertq("spip_zones_auteurs", array("id_zone" => $l, "id_auteur" => $auteur));
		spip_log('Auteur ' . $auteur . ' ajouté à la zone ' . $l, jaz);
	}

}

?>
