<?php

include_spip('inc/config');


function cameras_affiche_enfants($flux) {
	return $flux;
}

function cameras_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les cameras
	if ($e['type'] == 'camera' AND !$e['edition']) {
		$texte = recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']],
			'editable'=>autoriser('associerauteurs',$e['type'],$e['id_objet'])?'oui':'non'
		));
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}
	
	return $flux;
}

/**
 * Ajout de liste sur la vue d'un auteur
 */
function cameras_affiche_auteurs_interventions($flux) {
	$id_auteur = intval($flux['args']['id_auteur']);
	//if ($id_auteur = intval($flux['args']['id_auteur'])) {
		$flux['data'] .= recuperer_fond('prive/objets/liste/cameras', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('camera:info_cameras_auteur')
		), array('ajax' => true));
	//}
	return $flux;
}

function cameras_accueil_encours($flux) {
	$lister_objets = charger_fonction('lister_objets','inc');
	$flux .= $lister_objets('camera', array('titre'=>_T('camera:info_cameras_valider'), 'statut'=> 'prop', 'par'=>'date',true));
	return $flux;
}


function cameras_post_edition($flux) {
	if (($flux['args']['table'] == 'cameras') AND (isset($flux['data']['apparence']))) {
		include_spip('cameras_fonctions');
		modifier_apparence_camera($flux['data']['apparence'], $flux['args']['id_objet']);
	}

	/* On supprime les tuiles lui correspondant pour qu'elles soient recalculées*/
	if (($flux['args']['table'] == 'cameras') ){
		invalider_tuiles_camera($flux['args']['id_objet']);
	}

	return $flux;
}

function cameras_post_insertion($flux) {
	if (($flux['args']['table'] == 'cameras') AND (isset($flux['data']['apparence']))) {
		include_spip('cameras_fonctions');
		modifier_apparence_camera($flux['data']['apparence'], $flux['args']['id_objet']);
	}
	if (($flux['args']['table'] == 'cameras')) {
		/* On met un titre automatique si l'option est cochée */
		if (lire_config('cameras/titre_auto')=='on')
			{ sql_updateq("cameras", array('titre' => 'Camera'.$flux['args']['id_objet']),'id_camera='.$flux['args']['id_objet']);}
		/* On supprime les tuiles lui correspondant pour qu'elles soient recalculées*/
		invalider_tuiles_camera($flux['args']['id_objet']);
		sql_updateq("cameras", array('statut' => 'prop'),'id_camera='.$flux['args']['id_objet']);
		/*On renseigne la zone*/
		$zone = explode(".",$GLOBALS['domaine_site']);
		sql_updateq("cameras", array('zone' => $zone[0]),'id_camera='.$flux['args']['id_objet']);
	}
	
	return $flux;
}


function cameras_header_prive($flux){
		$flux .= "<!-- code issu du pipeline header_prive -->\n";
		$flux .= "<script type='text/javascript' src='https://maps.google.com/maps/api/js?v=3.2&sensor=false'></script>\n";
		$headers = Array(
			"carto/lib/jquery/js/jquery-ui-1.8.16.custom.min.js",
			"carto/lib/jquery/js/jquery.cookie.js",
			"carto/lib/jquery/css/ui-lightness/jquery-ui-1.8.16.custom.css",
			"carto/lib/Leaflet/0.4.x/leaflet.js",
			"carto/lib/Leaflet/leafclusterer.min.js",
			"carto/lib/osm2geo.js",
			"carto/lib/Leaflet/google.min.js",
			"carto/lib/Leaflet/tile.stamen.js",
			"carto/lib/Leaflet/0.4.x/leaflet.css",
			"carto/js/carto.js",
			"carto/js/carto.leaflet.js",
			"carto/js/carto.forms.js",
			"carto/js/carto.formulaire_prive.js",
			"carto/style/editer_camera.css",
			"carto/style/carto_cartes.css"
		);
		
		foreach ($headers as $i => $file) {
			$path = url_absolue(find_in_path($file));
			if ( pathinfo($file, PATHINFO_EXTENSION) == 'css'){
				$flux .= "<link rel='stylesheet' href='".$path."' type='text/css' media='all' />\n";
			}else{
				$flux .= "<script type='text/javascript' src='".$path."'></script>\n";
			}
		}
			
        return $flux;
}

function cameras_insert_head($flux){
        $flux .= "<!-- code issu du pipeline insert_head -->";
		$flux .= "<script type='text/javascript' src='https://maps.google.com/maps/api/js?v=3.2&sensor=false'></script>\n";
		$headers = Array(
			"carto/lib/jquery/js/jquery-ui-1.8.16.custom.min.js",
			"carto/lib/jquery/js/jquery.cookie.js",
			"carto/lib/jquery/css/ui-lightness/jquery-ui-1.8.16.custom.css",
			"carto/lib/Leaflet/0.4.x/leaflet.js",
			"carto/lib/Leaflet/leafclusterer.min.js",
			"carto/lib/osm2geo.js",
			"carto/lib/Leaflet/google.min.js",
			"carto/lib/Leaflet/tile.stamen.js",
			"carto/lib/Leaflet/0.4.x/leaflet.css",
			"carto/js/carto.js",
			"carto/js/carto.leaflet.js",
			"carto/style/editer_camera.css",
			"carto/style/carto_cartes.css"
		);
		
		foreach ($headers as $i => $file) {
			$path = url_absolue(find_in_path($file));
			if ( pathinfo($file, PATHINFO_EXTENSION) == 'css'){
				$flux .= "<link rel='stylesheet' href='".$path."' type='text/css' media='all' />\n";
			}else{
				$flux .= "<script type='text/javascript' src='".$path."'></script>\n";
			}
		}

        return $flux;
}


?>