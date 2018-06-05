<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_insert_head_css($flux) {
	$flux .= "\n".'<link rel="stylesheet" href="'. find_in_path('lib/leaflet-draw/leaflet.draw.css') .'" />';
	return $flux;
}

/**
 * Insertion des scripts et css du plugin dans les pages de l'espace privé
 * @param $flux
 * @return mixed
 */
function gisgeom_header_prive($flux) {
	$flux .= gisgeom_insert_head_css('');
	return $flux;
}

/**
 * Ajouter les inputs des champs type et geo au formulaire editer_gis
 * Ajouter le script leaflet draw au script gis.js appelé par les cartes
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_recuperer_fond($flux) {
	if ($flux['args']['fond'] == 'saisies/carte') {
		$saisie = recuperer_fond('formulaires/inc-editer_gis-geom', $flux['data']['contexte']);
		$flux['data']['texte'] = preg_replace('%<!--extragis-->%is', '$0'.$saisie, $flux['data']['texte']);
	}
	if ($flux['args']['fond'] == 'javascript/gis.js') {
		if (!function_exists('lire_config')) {
			include_spip('inc/config');
		}
		if (lire_config('auto_compress_js') == 'oui' && function_exists('compacte')) {
			$ajouts = "\n". compacte(spip_file_get_contents(find_in_path('lib/leaflet-draw/leaflet.draw-src.js')), 'js');
		} else {
			$ajouts = "\n". spip_file_get_contents(find_in_path('lib/leaflet-draw/leaflet.draw-src.js'));
		}
		$flux['data']['texte'] .= $ajouts;
	}
	return $flux;
}

/**
 * Récupérer la valeur du champ geo au format WKT pour la passer au formulaire editer_gis
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_formulaire_charger($flux) {
	if ($flux['args']['form'] == 'editer_gis') {
		$id_gis = $flux['data']['id_gis'];
		if (intval($id_gis)) {
			$wkt = sql_getfetsel('AsText(geo)', 'spip_gis', "id_gis = $id_gis");
			include_spip('gisgeom_fonctions');
			$flux['data']['geo'] = $wkt;
			$flux['data']['geojson'] = wkt_to_json($wkt);
		} elseif (_request('geojson')) {
			$flux['data']['geo'] = json_to_wkt(_request('geojson'));
			$flux['data']['geojson'] = _request('geojson');
		} elseif (isset($_FILES['import']) and $_FILES['import']['error'] != 4) {
			include_spip('action/ajouter_documents');
			$infos_doc = verifier_upload_autorise($_FILES['import']['name']);
			$fichier = $_FILES['import']['tmp_name'];
			$import = '';
			lire_fichier($fichier, $donnees);
			if ($donnees) {
				find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
				$geometry = geoPHP::load($donnees, $infos_doc['extension']);
				$flux['data']['geojson'] = $geometry->out('json');
				set_request('geojson', $geometry->out('json'));
				// renseigner les coordonnées de l'objet à partir de son centroid
				$centroid = $geometry->getCentroid();
				set_request('lat', $centroid->getY());
				set_request('lon', $centroid->getX());
			}
		}
	}
	return $flux;
}

/**
 * Outrepasser les champs obligatoires du formulaire editer_gis si on importe un fichier lors de la création
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_formulaire_verifier($flux) {
	if ($flux['args']['form'] == 'editer_gis' and isset($_FILES['import']) and $_FILES['import']['error'] != 4) {
		include_spip('action/ajouter_documents');
		$infos_doc = verifier_upload_autorise($_FILES['import']['name']);
		if (in_array($infos_doc['extension'], array('gpx', 'kml'))) {
			unset($flux['data']['titre']);
			unset($flux['data']['zoom']);
		} elseif ($infos_doc['extension'] != 'json') {
			$flux['data']['import'] = _T('medias:erreur_upload_type_interdit', array('nom'=>$_FILES['import']['name']));
		}
	}
	return $flux;
}

/**
 * Gestion de l'import de fichiers GPX et KML
 * Passer la valeur du champ geo lors de l'insertion d'un objet
 * (un champ GEOMETRY ne peut être nul si la table comporte un index spatial basé sur celui-ci)
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_pre_insertion($flux) {
	if ($flux['args']['table'] == 'spip_gis' and !_request('geojson') and isset($_FILES['import']) and $_FILES['import']['error'] != 4) {
		include_spip('action/ajouter_documents');
		$infos_doc = verifier_upload_autorise($_FILES['import']['name']);
		$fichier = $_FILES['import']['tmp_name'];
		$import = '';
		lire_fichier($fichier, $donnees);
		if ($donnees) {
			find_in_path(_DIR_LIB_GEOPHP.'geoPHP.inc', '', true);
			$geometry = geoPHP::load($donnees, $infos_doc['extension']);
			set_request('geojson', $geometry->out('json'));
			$wkt = $geometry->out('wkt');
			$flux['data']['geo'] = sql_getfetsel("GeomFromText('$wkt')");
			// titre et descriptif du gis à partir des infos du fichier si pas de titre posté
			if (!_request('titre')) {
				if ($infos_doc['extension'] == 'gpx') {
					$infos['titre'] = textebrut(extraire_balise($donnees, 'name'));
					$infos['descriptif'] = textebrut(extraire_balise($donnees, 'desc'));
				}
				if ($infos_doc['extension'] == 'kml') {
					include_spip('inc/xml');
					$arbre = spip_xml_parse($donnees);
					spip_xml_match_nodes(',^Document,', $arbre, $documents);
					foreach ($documents as $document => $info) {
						$infos['titre'] = preg_replace('/<!\[cdata\[(.*?)\]\]>/is', '$1', $info[0]['name'][0]);
						$infos['descriptif'] = preg_replace('/<!\[cdata\[(.*?)\]\]>/is', '$1', $info[0]['description'][0]);
					}
				}
				set_request('titre', $infos['titre']);
				set_request('descriptif', $infos['descriptif']);
			}
			// renseigner les coordonnées de l'objet à partir de son centroid
			$centroid = $geometry->getCentroid();
			set_request('lat', $centroid->getY());
			set_request('lon', $centroid->getX());
		}
	} elseif ($flux['args']['table'] == 'spip_gis') {
		if (_request('geojson')) {
			$json = _request('geojson');
		} else {
			/**
			 * Cas où on utilise la fonction gis_inserer() depuis une application tierce
			 * On doit fournir un 'geo' valide pour récupérer notre point
			 * Ex: CRUD, xmlrpc...
			 */
			$point = array('type' => 'Feature','geometry' => array('type'=> 'Point','coordinates' => array(_request('lon')?_request('lon'):0,_request('lat')?_request('lat'):0)));
			$json = json_encode($point);
		}
		include_spip('gisgeom_fonctions');
		$wkt = json_to_wkt($json);
		// convertir le WKT en binaire avant l'insertion
		$binary = sql_getfetsel("GeomFromText('$wkt')");
		$flux['data']['geo'] = $binary;
	}
	return $flux;
}

/**
 * Passer les valeurs des champs geo et type lors de la modification d'un objet
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_post_edition($flux) {
	if (_request('geojson')
		and $flux['args']['type'] == 'gis'
		and $flux['args']['action'] == 'modifier') {
		$id_gis = $flux['args']['id_objet'];
		include_spip('gisgeom_fonctions');
		$wkt = json_to_wkt(_request('geojson'));
		// TODO : renseigner les valeurs de lat et lon à partir du centroid de l'objet si ce n'est pas un point
		sql_update(
			'spip_gis',
			array(
				'geo' => "GeomFromText('$wkt')",
				'type' => sql_quote(_request('type'))
			),
			'id_gis = '.intval($id_gis)
		);
	}

	if (!_request('geojson')
		and isset($flux['data']['lon'])
		and isset($flux['data']['lat'])
		and $flux['args']['type'] == 'gis'
		and $flux['args']['action'] == 'modifier') {
		// générer automatiquemebt le champ geo à partir de lat et lon quand on passe par l'API et gis_modifier
		$id_gis = $flux['args']['id_objet'];
		$point = array('type' => 'Feature', 'geometry' => array('type' => 'Point', 'coordinates' => array($flux['data']['lon'], $flux['data']['lat'])));
		$json  = json_encode($point);
		include_spip('gisgeom_fonctions');
		$wkt = json_to_wkt($json);
		sql_update(
			'spip_gis',
			array(
				'geo' => "GeomFromText('$wkt')",
				'type' => sql_quote($type)
			),
			'id_gis = '.intval($id_gis)
		);
	}
	
	return $flux;
}

/**
 * Surcharger les boucles GIS et celles qui comportent le critère gis
 * pour permettre d'accéder à la valeur du champ geo au format WKT (voir balise #GEOMETRY)
 * et aux valeurs des styles concaténées (voir balise #GEOMETRY_STYLES)
 *
 * @param $boucle
 * @return mixed
 */
function gisgeom_pre_boucle($boucle) {
	if ($boucle->type_requete == 'gis' or in_array('gis', $boucle->jointures)) {
		$boucle->select[]= 'AsText(gis.geo) AS geometry';
		$boucle->select[]= "CONCAT_WS(',', gis.color, gis.weight, gis.opacity, gis.fillcolor, gis.fillopacity) AS geometry_styles";
	}
	return $boucle;
}
