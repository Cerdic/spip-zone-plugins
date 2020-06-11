<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Récupère la liste des points géolocalisés
 *
 * Arguments possibles :
 * -* login string
 * -* pass string
 * -* objet string : le type d'objets liés
 * -* id_objet int : l'identifiant numérique de l'objet lié
 * -* where array : conditions à ajouter dans la clause where du select
 * -* tri array : les éléments de tri
 * -** Si 'distance' dans le tri
 * -*** lat float : la latitude à partir de laquelle chercher
 * -*** lon float : la longitude à partir de laquelle chercher
 * -* limite int : le nombre d'éléments maximum à retourner
 */
function spip_liste_gis($args) {
	global $spip_xmlrpc_serveur;

	if (!$spip_xmlrpc_serveur) {
		return false;
	}

	$objet = 'gis';

	$what[] = 'gis.id_gis';
	$from = 'spip_gis as gis LEFT JOIN spip_gis_liens as lien ON gis.id_gis=lien.id_gis';
	$where = is_array($args['where']) ? $args['where'] : array();
	$order = is_array($args['tri']) ? $args['tri'] : array();
	if ((intval($args['id_objet']) > 0) && $args['objet']) {
		$where[] = 'lien.id_objet='.intval($args['id_objet']).' AND lien.objet='.sql_quote($args['objet']);
	}

	if (in_array('distance', $order) or in_array('!distance', $order)) {
		$distance = true;
		$lat = $args['lat'];
		$lon = $args['lon'];
		if (!is_numeric($lon) or !is_numeric($lat)) {
			$erreur = _T('gis:erreur_xmlrpc_lat_lon');
			return new IXR_Error(-32601, attribut_html($erreur));
		} else {
			$what[] = "(6371 * acos( cos( radians(\"$lat\") ) * cos( radians( gis.lat ) ) * cos( radians( gis.lon ) - radians(\"$lon\") ) + sin( radians(\"$lat\") ) * sin( radians( gis.lat ) ) ) ) AS distance";
		}
	}

	/**
	 * Une recherche
	 */
	if (is_string($args['recherche']) and strlen($args['recherche']) > 3) {
		$prepare_recherche = charger_fonction('prepare_recherche', 'inc');
		list($rech_select, $rech_where) = $prepare_recherche($args['recherche'], $objet.'s', $where);
		$what[] = $rech_select;
		$from .= ' INNER JOIN spip_resultats AS resultats ON ( resultats.id = gis.id_gis ) ';
		$where[] = 'resultats.'.$rech_where;
	}

	$points_struct = array();

	if ($points = sql_select($what, $from, $where, '', $order, $args['limite'])) {
		while ($point = sql_fetch($points)) {
			$struct=array();
			$args['id_gis'] = $point['id_gis'];
			/**
			 * On utilise la fonction geodiv_lire_media pour éviter de dupliquer trop de code
			 */
			$struct = spip_lire_gis($args);
			if ($distance) {
				$struct['distance'] = $point['distance'];
			}
			$points_struct[] = $struct;
		}
	}
	return $points_struct;
}

/**
 * Récupère le contenu d'un point géolocalisé
 *
 * Arguments possibles :
 * -* login
 * -* pass
 * -* id_gis (Obligatoire)
 * -* lat : si disponible avec lon, on ajoute la distance dans les infos
 * -* lon : si disponible avec lat, on ajoute la distance dans les infos
 */
function spip_lire_gis($args) {
	global $spip_xmlrpc_serveur;

	if (!$spip_xmlrpc_serveur) {
		return false;
	}

	if (!intval($args['id_gis']) > 0) {
		$erreur = _T('xmlrpc:erreur_identifiant', array('objet'=>'gis'));
		return new IXR_Error(-32601, attribut_html($erreur));
	}

	$args_gis = array('objet' => 'gis','id_objet' => $args['id_gis']);
	$res = $spip_xmlrpc_serveur->read($args_gis);
	if (!$res) {
		return $spip_xmlrpc_serveur->error;
	}

	if (isset($args['lat']) && is_numeric($args['lat']) && isset($args['lon']) && is_numeric($args['lon'])) {
		$lat = $args['lat'];
		$lon = $args['lon'];
		$what[] = 'gis.id_gis';
		$what[] = "(6371 * acos( cos( radians(\"$lat\") ) * cos( radians( gis.lat ) ) * cos( radians( gis.lon ) - radians(\"$lon\") ) + sin( radians(\"$lat\") ) * sin( radians( gis.lat ) ) ) ) AS distance";
		$distance = sql_fetsel($what, 'spip_gis AS gis', 'gis.id_gis='.intval($args['id_gis']));
		$res['result'][0]['distance'] = $distance['distance'];
	}

	if (autoriser('modifier', 'gis', $args['id_gis'], $GLOBALS['visiteur_session'])) {
		$res['result'][0]['modifiable'] = 1;
	} else {
		$res['result'][0]['modifiable'] = 0;
	}
	$logo = quete_logo('id_gis', 'on', $res['result'][0]['id_gis'], '', false);
	if (is_array($logo)) {
		$res['result'][0]['logo'] = url_absolue($logo[0]);
	}

	if (defined('_DIR_PLUGIN_GISGEOM')) {
		if (isset($res['result'][0]['geo'])) {
			include_spip('gisgeom_fonctions');
			$res['result'][0]['geo'] = wkt_to_json($wkt);
		}
	}

	$gis_struct = $res['result'][0];
	$gis_struct = array_map('texte_backend', $gis_struct);
	return $gis_struct;
}
