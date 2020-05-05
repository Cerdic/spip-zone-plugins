<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/json');

/**
 * Filtre dec_to_dms, http://www.statemaster.com/encyclopedia/Geographic-coordinate-conversion
 *
 * @param decimal $coord
 * @return string
 */
function dec_to_dms($coord) {
	return sprintf(
		'%0.0f° %2.3f',
		floor(abs($coord)),
		60*(abs($coord)-floor(abs($coord)))
	);
}

/**
 * Filtre dms_to_dec, http://www.statemaster.com/encyclopedia/Geographic-coordinate-conversion
 *
 * @param string $ref N, E, S, W
 * @param int $deg
 * @param int $min
 * @param int $sec
 * @return decimal
 */
function dms_to_dec($ref, $deg, $min, $sec) {

	$arrLatLong = array();
	$arrLatLong['N'] = 1;
	$arrLatLong['E'] = 1;
	$arrLatLong['S'] = -1;
	$arrLatLong['W'] = -1;

	return ($deg+((($min*60)+($sec))/3600)) * $arrLatLong[$ref];
}

/**
 * Filtre distance pour renvoyer la distance entre deux points
 * http://snipplr.com/view/2531/calculate-the-distance-between-two-coordinates-latitude-longitude/
 * sinon voir ici : https://zone.spip.org/trac/spip-zone/browser/_plugins_/forms/geoforms/inc/gPoint.php
 *
 * @param int|array $from
 *     id_gis du point de référence ou tableau de coordonnées
 * @param int|array $to
 *     id_gis du point distant ou tableau de coordonnées
 * @param bool $miles
 *     Renvoyer le résultat en miles (kilomètres par défaut)
 * @return float
 *     Retourne la distance en kilomètre ou en miles
 */
function distance($from, $to, $miles = false) {
	// On ne travaille que si on a toutes les infos
	if (((is_array($from) and isset($from['lat']) and isset($from['lon'])) // Le départ est soit un tableau soit un entier
		or ($from = intval($from) and $from > 0 and $from = sql_fetsel('lat,lon', 'spip_gis', "id_gis=$from")))
		and ((is_array($to) and isset($to['lat']) and isset($to['lon'])) or ($to = intval($to) and $to > 0 and $to = sql_fetsel('lat,lon', 'spip_gis', "id_gis=$to"))) // Le distant est soit un tableau soit un entier
	) {
		$pi80 = M_PI / 180;
		$from['lat'] *= $pi80;
		$from['lon'] *= $pi80;
		$to['lat'] *= $pi80;
		$to['lon'] *= $pi80;

		$r = 6372.797; // mean radius of Earth in km
		$dlat = $to['lat'] - $from['lat'];
		$dlng = $to['lon'] - $from['lon'];
		$a = sin($dlat / 2) * sin($dlat / 2) + cos($from['lat']) * cos($to['lat']) * sin($dlng / 2) * sin($dlng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$km = $r * $c;

		return ($miles ? ($km * 0.621371192) : $km);
	}

	return false;
}

/**
 * Afficher proprement une distance
 *
 * @param float $distance
 * 		Nombre indiquant une distance
 * @param int $precision
 * 		Précision des décimales du nombre final, par défaut 2
 * @param string $format_entree
 * 		Format de distance donnée en entrée : par défaut en kilomètres, sinon en mètres avec "m"
 * @return string
 * 		Retourne une chaine composée d'un nombre arrondi et d'une unité de mesure de distance
 **/
function distance_en_metres($distance, $precision = 2, $format_entree = 'km') {
	if ($distance) {
		// On passe toujours tout en kilomètres pour uniformiser
		if ($format_entree == 'm') {
			$distance = $distance / 1000;
		}

		// Si c'est supérieur à 1, on reste en kilomètres
		if ($distance > 1) {
			$unite = 'km';
		} elseif (($distance = $distance*1000) > 1) {
			$unite = 'm';
		}

		$distance = number_format($distance, $precision, ',', '') . ' ' . $unite;
	}

	return $distance;
}

/**
 * Compilation du critère {distancefrom}
 *
 * Critère {distancefrom} qui permet de ne sélectionner que les objets se trouvant à une distance comparée avec un point de repère.
 * On doit lui passer 3 paramètres obligatoires :
 * - le point de repère qui est un tableau avec les clés "lat" et "lon" ou un id_gis
 * - l'opérateur de comparaison
 * - la distance à comparer, en kilomètres
 * Cela donne par exemple :
 *   {distancefrom #ARRAY{lat,#LAT,lon,#LON},<,30}
 *   {distancefrom #ARRAY{lat,#ENV{lat},lon,#ENV{lon}},<=,#ENV{distance}}
 *
 * @param unknown $idb
 * @param unknown &$boucles
 * @param unknown $crit
 */
function critere_distancefrom_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table; // articles
	$primary = $boucle->primary; // id_article
	$objet = objet_type($id_table); // article

	if (($id_table == 'gis' or isset($boucle->join['gis'])) // Soit depuis une boucle (GIS) soit un autre objet mais avec {gis}
		and count($crit->param) == 3 // Il faut aussi qu'il y ait 3 critères obligatoires
	) {
		$point_reference = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		$operateur = calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent);
		$distance = calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent);

		// Si le point de référence est un entier, on essaye de récupérer les coordonnées du point GIS
		// Et si on a toujours pas de tableau correct, on met false
		$boucle->hierarchie .= '$point_reference = '.$point_reference.';';
		$boucle->hierarchie .= 'if (is_numeric($point_reference)){ $point_reference = sql_fetsel("lat,lon", "spip_gis", "id_gis = ".intval($point_reference)); }';
		$boucle->hierarchie .= 'if (!is_array($point_reference) or !isset($point_reference["lat"]) or !isset($point_reference["lon"])){ $point_reference = false; }';
		// L'opérateur doit exister dans une liste précise
		$boucle->hierarchie .= '$operateur_distance = trim('.$operateur.');';
		$boucle->hierarchie .= 'if (!in_array($operateur_distance, array("=","<",">","<=",">="))){ $operateur_distance = false; }';
		$boucle->hierarchie .= '$distance = '.$distance.';';

		$boucle->select[] = '".(!$point_reference ? "\'\' as distance" : "(6371 * acos( cos( radians(".$point_reference["lat"].") ) * cos( radians( gis.lat ) ) * cos( radians( gis.lon ) - radians(".$point_reference["lon"].") ) + sin( radians(".$point_reference["lat"].") ) * sin( radians( gis.lat ) ) ) ) AS distance")."';
		$boucle->having[] = '((!$point_reference or !$operateur_distance or !$distance) ? "1=1" : "distance $operateur_distance ".sql_quote($distance))';
	}
}

/**
 * Compile le critère `{gis}` qui permet de compléter la boucle avec les points GIS
 *
 * Usage
 * - `{gis}` Retourne les objets ayant des points (et ajoute les balises spéciales GIS tel que `#TITRE_GIS`)
 * - `{!gis}` Retourne les objets sans points
 * - `{gis distance<XX}`, sur une boucle `GIS`, filtre une liste de points par rapport à la distance du point de l'env
 *
 * @param string $idb
 * @param array $boucles
 * @param Critere $crit
 */
function critere_gis_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table; // articles
	$primary = $boucle->primary; // id_article
	$objet = objet_type($id_table); // article

	if ($id_table == 'gis') {
		// exclure l'élément en cours des résultats
		$id_gis = calculer_argument_precedent($idb, $primary, $boucles);
		$boucle->where[]= array("'!='", "'$boucle->id_table." . "$primary'", $id_gis);

		// récupérer les paramètres du critère
		$op='';
		$params = $crit->param;
		$type = array_shift($params);
		$type = $type[0]->texte;
		if (preg_match(',^(\w+)([<>=]+)([0-9]+)$,', $type, $r)) {
			$type=$r[1];
			$op=$r[2];
			$op_val=$r[3];
		}
		if ($op) {
			$boucle->having[]= array("'".$op."'", "'".$type."'",$op_val);
		}

		// récupérer lat/lon du point de la boucle englobante
		$lat = calculer_argument_precedent($idb, 'lat', $boucles);
		$lon = calculer_argument_precedent($idb, 'lon', $boucles);

		// http://www.awelty.fr/developpement-web/php/
		// http://www.movable-type.co.uk/scripts/latlong-db.html
		// http://code.google.com/intl/fr/apis/maps/articles/geospatial.html#geospatial
		$select = "(6371 * acos( cos( radians(\".$lat.\") ) * cos( radians( gis.lat ) ) * cos( radians( gis.lon ) - radians(\".$lon.\") ) + sin( radians(\".$lat.\") ) * sin( radians( gis.lat ) ) ) ) AS distance";
		$order = "'distance'";

		$boucle->select[]= $select;
		$boucle->order[]= $order;
	} else {
		/* Recherche d'objets SANS point */
		if ($crit->not) {
			$boucle->from['gis_liens'] = 'spip_gis_liens';
			$boucle->from_type['gis_liens'] = 'LEFT';
			$boucle->join['gis_liens'] = array("'$id_table'","'id_objet'","'$primary'","'gis_liens.objet='.sql_quote('$objet')");
			$boucle->where[] = "'gis_liens.id_gis IS NULL'";

		/* Recherche d'objets AVEC point + ajout des champs GIS */
		} else {
			// ajouter tous les champs du point au select
			// et les suffixer pour lever toute ambiguite avec des champs homonymes
			if (!function_exists('objet_info')) {
				include_spip('inc/filtres');
			}
			$champs = objet_info('gis', 'champs_critere_gis');
			foreach ($champs as $champ) {
				$boucle->select[] = $champ;
			}
			// jointure sur spip_gis_liens/spip_gis
			// cf plugin notation
			// $boucle->join["surnom (as) table de liaison"] = array("surnom de la table a lier", "cle primaire de la table de liaison", "identifiant a lier", "type d'objet de l'identifiant");
			$boucle->from['gis_liens'] = 'spip_gis_liens';
			$boucle->join['gis_liens']= array("'$id_table'","'id_objet'","'$primary'","'gis_liens.objet='.sql_quote('$objet')");
			$boucle->from['gis'] = 'spip_gis';
			$boucle->join['gis']= array("'gis_liens'","'id_gis'");
			// bien renvoyer tous les points qui son attachés à l'objet
			// mais attention, si on trouve en amont un groupement portant sur un champ *de GIS*,
			// alors cela signifie que la personne veut faire une opération de groupement sur les points donc là on n'ajoute pas id_gis
			$tous_les_points = true;
			foreach ($boucle->group as $champ) {
				if (in_array($champ, array('ville', 'code_postal', 'pays', 'code_pays', 'region','departement'))) {
					$tous_les_points = false;
				}
			}
			if ($tous_les_points) {
				$boucle->group[] = 'gis_liens.id_gis';
			}
			// ajouter gis aux jointures et spécifier les jointures explicites pour pouvoir utiliser les balises de la table de jointure
			// permet de passer dans trouver_champ_exterieur() depuis index_tables_en_pile()
			// cf http://article.gmane.org/gmane.comp.web.spip.zone/6628
			$boucle->jointures[] = 'gis';
			if (empty($boucle->jointures_explicites)) {
				$boucle->jointures_explicites = 'gis_liens gis';
			} else {
				$boucle->jointures_explicites .= ' gis_liens gis';
			}
		}
	}
}
function critere_gis_tout_dist($idb, &$boucles, $crit) {
	$crit->op = 'gis';
	$critere_gis = charger_fonction('gis', 'critere/');
	$critere_gis($idb, $boucles, $crit);
	$boucle = &$boucles[$idb];
	$boucle->from_type['gis_liens'] = 'LEFT';
	$boucle->from_type['gis'] = 'LEFT';
}

/**
 * Balise #DISTANCE issue du critère {gis distance<XX}
 * merci marcimant : http://formation.magraine.net/spip.php?article61
 *
 * @param unknown_type $p
 */
function balise_distance_dist($p) {
	return rindex_pile($p, 'distance', 'gis');
}

/**
 * Balise #TITRE_GIS : retourne le titre du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_titre_gis_dist($p) {
	return rindex_pile($p, 'titre_gis', 'gis');
}

/**
 * Balise #DESCRIPTIF_GIS : retourne le descriptif du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_descriptif_gis_dist($p) {
	return rindex_pile($p, 'descriptif_gis', 'gis');
}

/**
 * Balise #ADRESSE_GIS : retourne l'adresse du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_adresse_gis_dist($p) {
	return rindex_pile($p, 'adresse_gis', 'gis');
}

/**
 * Balise #PAYS_GIS : retourne le pays du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_pays_gis_dist($p) {
	return rindex_pile($p, 'pays_gis', 'gis');
}

/**
 * Balise #CODE_PAYS_GIS : retourne le code pays du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_code_pays_gis_dist($p) {
	return rindex_pile($p, 'code_pays_gis', 'gis');
}

/**
 * Balise #VILLE_GIS : retourne la ville du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_ville_gis_dist($p) {
	return rindex_pile($p, 'ville_gis', 'gis');
}

/**
 * Balise #REGION_GIS : retourne la région du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_region_gis_dist($p) {
	return rindex_pile($p, 'region_gis', 'gis');
}

/**
 * Balise #DEPARTEMENT_GIS : censé retourner le département du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_departement_gis_dist($p) {
	return rindex_pile($p, 'departement_gis', 'gis');
}

/**
 * Balise #CODE_POSTAL_GIS : retourne le code postal du point
 * Necessite le critere {gis} sur la boucle
 *
 * @param unknown_type $p
 */
function balise_code_postal_gis_dist($p) {
	return rindex_pile($p, 'code_postal_gis', 'gis');
}

/**
 * Définition du fond de carte à utiliser par défaut en prenant compte les defines
 */
function gis_layer_defaut() {
	$defaut = 'openstreetmap_mapnik';
	if (defined('_GIS_LAYER_DEFAUT_FORCE')) {
		return _GIS_LAYER_DEFAUT_FORCE;
	} else {
		if (defined('_GIS_LAYER_DEFAUT')) {
			$defaut = _GIS_LAYER_DEFAUT;
		}
		$config = lire_config('gis/layer_defaut');
		return $config ? $config : $defaut;
	}
}

/**
 * Recuperer les cles primaires du env pour l'appel a l'url json des points
 * @param $env
 * @return array
 */
function gis_modele_url_json_env($env) {
	$contexte = array();
	if (is_string($env)) {
		$env = unserialize($env);
	}
	if ($env) {
		// d'abord toutes les cles primaires connues
		$tables_sql = lister_tables_objets_sql();
		foreach (array_keys($tables_sql) as $table) {
			$primary = id_table_objet($table);
			if (isset($env[$primary])) {
				$contexte[$primary] = is_array($env[$primary]) ? $env[$primary] : trim($env[$primary]);
			}
		}
		// puis cas particuliers et ceux ajoutés par le pipeline
		$keys = pipeline('gis_modele_parametres_autorises', array('objet', 'id_objet', 'id_secteur', 'id_parent', 'media', 'recherche', 'mots', 'pays', 'code_pays', 'region', 'departement', 'ville', 'code_postal', 'adresse'));
		foreach ($keys as $key) {
			if (isset($env[$key])) {
				$contexte[$key] = is_array($env[$key]) ? $env[$key] : trim($env[$key]);
			}
		}
	}
	return $contexte;
}


/**
 * Transforme un paramètre d'entrée en tableau
 * s'il n'en est pas déjà un.
 *
 * Permet d'utiliser dans l'appel au modèle de carte gis
 * depuis un texte d'article des paramètres tabulaires,
 * tel que des identifiants de documents de tracés kml,
 * tel que `<carte_gis|kml=10,11,12>`
 *
 * @example `#ENV{kml}|gis_param_to_array`
 *
 * @param string|int|array $param
 *     Le paramètre à transformer en tableau
 * @param string $sep
 *     Le séparateur utilisé
 * @return array
**/
function gis_param_to_array($param, $sep = ',') {
	if (is_array($param)) {
		return $param;
	}
	// enlever les espaces et exploser
	$tab = explode($sep, trim((string)$param));
	// enlever les champs vides, les espaces sur chaques champs.
	return array_map('trim', array_filter($tab));
}

/**
 * Transformer le tableau de kml en tableau d'urls :
 *   si numerique c'est un id de document
 *   si chaine c'est une url qu'on rapatrie en local
 * @param array $kml
 * @return array
 */
function gis_kml_to_urls($kml) {
	if ($kml and count($kml)) {
		include_spip('inc/filtres_mini');
		include_spip('inc/distant');
		foreach ($kml as $k => $v) {
			if (is_numeric($v)) {
				$kml[$k] = url_absolue(generer_url_entite($v, 'document'));
			} else {
				$kml[$k] = _DIR_RACINE.copie_locale($kml[$k], 'modif');
			}
		}
	}
	return $kml;
}

/**
 * Retourne les propriétés JSON de l'icône d'un point
 *
 * @param string $img
 *     Balise HTML `<img ... />` ou chemin de l'image (qui peut être une URL distante).
 * @return string
 *     Les propriétés de l'icône
**/
function gis_icon_properties($img = '') {
	$props = $icon = '';

	if ($img) {
		if (largeur($img) >= 44) {
			$icon = extraire_attribut(filtrer('image_graver', filtrer('image_recadre', filtrer('image_passe_partout', $img, 32, 32), 32, 32, 'center', 'transparent')), 'src');
		} else {
			$icon = extraire_attribut($img, 'src') ? extraire_attribut($img, 'src') : $img;
		}
	} else {
		$icon = find_in_path('images/marker_defaut.png');
	}

	if ($icon) {
		$props .= ",\n\t\t\t\"icon\": ". json_encode(url_absolue($icon)).',';
		list($h,$w) = taille_image($icon);
		$props .= "\n\t\t\t\"icon_size\": ". json_encode(array($w,$h)).',';
		/**
		 * Si l'icone est carrée, on considère que c'est soit un point soit un carré qui pointe un lieu et non une "goutte"
		 * On centre donc au milieu de l'icone
		 */
		if ($w == $h) {
			$props .= "\n\t\t\t\"icon_anchor\": ". json_encode(array($w/2, $h/2)).',';
			$props .= "\n\t\t\t\"popup_anchor\": ". json_encode(array(0,0));
		} else {
			$props .= "\n\t\t\t\"icon_anchor\": ". json_encode(array($w/2, $h)).',';
			$props .= "\n\t\t\t\"popup_anchor\": ". json_encode(array(1, -round($h/1.2, 2)));
		}
	}

	if ($shadow = find_in_path('images/marker_defaut_shadow.png')) {
		$props .= ",\n\t\t\t\"shadow\": ". json_encode(url_absolue($shadow));
		list($h,$w) = taille_image($shadow);
		$props .= ",\n\t\t\t\"shadow_size\": ". json_encode(array($w,$h));
	}

	return $props;
}
