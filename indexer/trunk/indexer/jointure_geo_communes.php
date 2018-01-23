<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajoute les informations de communes liées
 *
 * @param string $objet
 * @param int $id_objet
 * @param \Indexer\Sources\Document $doc
 * @return \Indexer\Sources\Document
 */
function indexer_jointure_geo_communes_dist($objet, $id_objet, $doc) {
	// On va chercher tous les geo_communes de cet objet
	if (defined('_DIR_PLUGIN_GEOGRAPHIE') and $geo_communes = sql_allfetsel(
		'c.id_commune, nom',
		'spip_geo_communes as c join spip_geo_communes_liens as l on c.id_commune=l.id_commune',
		array('l.objet='.sql_quote($objet), 'l.id_objet='.intval($id_objet))
	)) {
		foreach ($geo_communes as $geo_commune) {
			$id_geo_commune = intval($geo_commune['id_commune']);
			$doc->properties['geo_communes']['noms'][$id_geo_commune] = $geo_commune['nom'];
			$doc->properties['geo_communes']['ids'][] = $id_geo_commune;
		}

		// On ajoute le nom des geo_communes en fulltext à la fin
		$doc->content .= "\n\n".join(' / ', $doc->properties['geo_communes']['noms']);
	}

	return $doc;
}
