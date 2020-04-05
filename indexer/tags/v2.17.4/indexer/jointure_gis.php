<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajoute les informations de points GIS liées
 *
 * @param string $objet
 * @param int $id_objet
 * @param \Indexer\Sources\Document $doc
 * @return \Indexer\Sources\Document
 */
function indexer_jointure_gis_dist($objet, $id_objet, $doc) {
	// On va chercher tous les gis de cet objet
	if (defined('_DIR_PLUGIN_GIS') and $gis = sql_allfetsel(
		'*',
		'spip_gis as g join spip_gis_liens as l on g.id_gis=l.id_gis',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		$doc->properties['gis'] = array();

		foreach ($gis as $point) {
			// Si c'est bien un point et pas un polygone
			if ($point['lat'] and $point['lon']) {
				$point['lat'] = floatval($point['lat']);
				$point['lon'] = floatval($point['lon']);
				// On enregistre tout le point entier, tant qu'à faire (ville, adresse, etc)
				$doc->properties['gis'][] = $point;
			}
		}
	}

	return $doc;
}
