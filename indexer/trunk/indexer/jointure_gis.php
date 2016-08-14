<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function indexer_jointure_gis_dist($objet, $id_objet, $infos) {
	// On va chercher tous les gis de cet objet
	if (defined('_DIR_PLUGIN_GIS') and $gis = sql_allfetsel(
		'*',
		'spip_gis as g join spip_gis_liens as l on g.id_gis=l.id_gis',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		$infos['properties']['gis'] = array();
		
		foreach ($gis as $point) {
			// Si c'est bien un point et pas un polygone
			if ($point['lat'] and $point['lon']) {
				// On enregistre tout le point entier, tant qu'à faire (ville, adresse, etc)
				$infos['properties']['gis'][] = $point;
			}
		}
	}
	
	return $infos;
}
