<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function indexer_jointure_geo_communes_dist($objet, $id_objet, $infos) {
	// On va chercher tous les geo_communes de cet objet
	if (defined('_DIR_PLUGIN_GEOGRAPHIE') and $geo_communes = sql_allfetsel(
		'c.id_commune, nom',
		'spip_geo_communes as c join spip_geo_communes_liens as l on c.id_commune=l.id_commune',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		foreach ($geo_communes as $geo_commune) {
			$id_geo_commune = intval($geo_commune['id_commune']);
			$infos['properties']['geo_communes']['noms'][$id_geo_commune] = $geo_commune['nom'];
			$infos['properties']['geo_communes']['ids'][] = $id_geo_commune;
		}

		// On ajoute le nom des geo_communes en fulltext à la fin
		$infos['content'] .= "\n\n".join(' | ', $infos['properties']['geo_communes']['noms']);
	}
	
	return $infos;
}
