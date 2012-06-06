<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_insert_head_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path(_DIR_LIB_LEAFLETDRAW.'Control.Draw.css') .'" />';
	return $flux;
}

/**
 * Insertion des scripts du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_insert_head($flux){
	// insertion des scripts de gisgeom
	$flux .="\n".'<script type="text/javascript" src="'. find_in_path(_DIR_LIB_LEAFLETDRAW.'Control.Draw.js') .'"></script>';
	$flux .="\n".'<script type="text/javascript" src="'. find_in_path(_DIR_LIB_LEAFLETDRAW.'Map.Draw.js') .'"></script>';
	$flux .="\n".'<script type="text/javascript" src="'. find_in_path('javascript/gisgeom.js') .'"></script>';
	return $flux;
}

/**
 * Insertion des scripts et css du plugin dans les pages de l'espace privé
 * @param $flux
 * @return mixed
 */
function gisgeom_header_prive($flux){
	$flux .= gisgeom_insert_head_css('');
	$flux .= gisgeom_insert_head('');
	return $flux;
}

/**
 * Ajouter les inputs des champs type et geo au formulaire editer_gis
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'formulaires/editer_gis') {
		$saisie = recuperer_fond('formulaires/inc-editer_gis-geom',$flux['data']['contexte']);
		$flux['data']['texte'] = preg_replace('%<li class="editer editer_lat.*?</li>%is', $saisie.'$0', $flux['data']['texte']);
	}
	return $flux;
}

/**
 * Récupérer la valeur du champ geo au format WKT pour la passer au formulaire editer_gis
 *
 * @param $flux
 * @return mixed
 */
function gisgeom_formulaire_charger($flux){
	if ($flux['args']['form'] == 'editer_gis') {
		$id_gis = $flux['data']['id_gis'];
		if (intval($id_gis)) {
			$wkt = sql_getfetsel("AsText(geo)","spip_gis","id_gis = $id_gis");
			include_spip('gisgeom_fonctions');
			$flux['data']['geo'] = $wkt;
			$flux['data']['geojson'] = wkt_to_json($wkt);
		}
	}
	return $flux;
}

/**
 * Passer la valeur du champ geo lors de l'insertion d'un objet
 * (un champ GEOMETRY ne peut être nul si la table comporte un index spatial basé sur celui-ci)
 * 
 * @param $flux
 * @return mixed
 */
function gisgeom_pre_insertion($flux){
	if (_request('geojson') AND $flux['args']['table'] == 'spip_gis') {
		include_spip('gisgeom_fonctions');
		$wkt = json_to_wkt(_request('geojson'));
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
function gisgeom_post_edition($flux){
	if (_request('geojson')
		AND $flux['args']['type'] == 'gis'
		AND $flux['args']['action'] == 'modifier')
	{
		$id_gis = $flux['args']['id_objet'];
		include_spip('gisgeom_fonctions');
		$wkt = json_to_wkt(_request('geojson'));
		// TODO : renseigner les valeurs de lat et lon à partir du centroid de l'objet si ce n'est pas un point
		sql_update("spip_gis",
			array(
				"geo" => "GeomFromText('$wkt')",
				"type" => sql_quote(_request('type'))
			),
			"id_gis = $id_gis"
		);
	}
	return $flux;
}

/**
 * Surcharger les boucles GIS et celles qui comportent le critère gis 
 * pour permettre d'accéder à la valeur du champ geo au format WKT (voir balise #GEOMETRY)
 *
 * @param $boucle
 * @return mixed
 */
function gisgeom_pre_boucle($boucle){
	if ($boucle->type_requete == 'gis' OR in_array('gis',$boucle->jointures)) {
        $boucle->select[]= 'AsText(gis.geo) AS geometry';
	}
	return $boucle;
}

?>
