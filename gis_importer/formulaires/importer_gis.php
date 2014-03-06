<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_importer_gis_charger_dist() {
	$valeurs['_etapes'] = 2;
	$valeurs['url_fichier_gis'] = _request('url_fichier_gis');
	if(_request('_etape')==2){
		if($url = _request('url_fichier_gis')){
			include_spip('inc/distant');
			$fichier = copie_locale($url);
			
			$contenu = '';
			lire_fichier(find_in_path($fichier),$contenu);
			$valeurs['gis_json'] = json_decode($contenu,true);
			$valeurs['_hidden'] .= "<input type='hidden' name='url_fichier_gis' id='url_fichier_gis' value='"._request('url_fichier_gis')."' />";
		}
	}
	return $valeurs;
}

function formulaires_importer_gis_verifier_1_dist() {
	if(_request('_etape')==1){
		if(!_request('url_fichier_gis') && !_request('fichier_gis'))
			$erreurs['url_fichier_gis'] = _T('gis_importer:erreur_fournir_fichier');
		
		if($url = _request('url_fichier_gis')){
			include_spip('inc/distant');
			$fichier = copie_locale($url);
			$contenu = '';
			if(!$fichier || $fichier == '' || $fichier == $url)
				$erreurs['url_fichier_gis'] = _T('gis_importer:erreur_url_fichier_non_recuperable',array('url'=>$url));
			else if(!lire_fichier(find_in_path($fichier),$contenu) || !($json = json_decode($contenu,true)) || $json == NULL){
				$erreurs['url_fichier_gis'] = _T('gis_importer:erreur_url_fichier_non_json',array('url'=>$url));
			}
			if(!isset($json['features']))
				$erreurs['url_fichier_gis'] = _T('gis_importer:erreur_url_fichier_non_geojson',array('url'=>$url));
		}
	}
	return $erreurs;
}

function formulaires_importer_gis_verifier_2_dist() {
	if(!_request('champ_titre'))
		$erreurs['champ_titre'] = _T('info_obligatoire');
	return $erreurs;
}

function formulaires_importer_gis_traiter_dist() {
	if($url = _request('url_fichier_gis')){
		include_spip('inc/distant');
		include_spip('action/editer_gis');
		include_spip('inc/config');
		$config = lire_config('gis_importer',array());
		$fichier = copie_locale($url);
		
		$contenu = '';
		lire_fichier(find_in_path($fichier),$contenu);
		$contenu = json_decode($contenu,true);
		if(isset($contenu['features']))
			$contenu = $contenu['features'];
		
		$nb = 0;
		$nb_update = 0;
		$champs_editables = objet_info('gis','champs_editables');
		unset($champs_editables['lon']);
		unset($champs_editables['lat']);
		foreach($contenu as $point){
			$point_add = array();
			if(isset($point['geometry']['type']) && $point['geometry']['type'] == 'Point' && isset($point['geometry']['coordinates']) && count($point['geometry']['coordinates']) == 2){
				$point_add['lat'] = $point['geometry']['coordinates'][0];
				$point_add['lon'] = $point['geometry']['coordinates'][1];
				foreach($champs_editables as $champ){
					if(_request('champ_'.$champ) && isset($point['properties'][_request('champ_'.$champ)]) && $point['properties'][_request('champ_'.$champ)] != NULL)
						$point_add[$champ] = $point['properties'][_request('champ_'.$champ)];
				}
				
				if($config['unicite_titre'] == 'on' && $id_gis = sql_getfetsel('id_gis','spip_gis','titre = '.sql_quote($point_add['titre']))){
					gis_modifier($id_gis,$point_add);
					$nb_update++;
				}
				else if($config['unicite_latlon'] == 'on' && $id_gis = sql_getfetsel('id_gis','spip_gis','lat = '.sql_quote($point_add['lat']).' AND lon = '.sql_quote($point_add['lon']))){
					gis_modifier($id_gis,$point_add);
					$nb_update++;
				}
				else if($config['unicite_descriptif'] == 'on' && $id_gis = sql_getfetsel('id_gis','spip_gis','descriptif = '.sql_quote($point_add['descriptif']))){
					gis_modifier($id_gis,$point_add);
					$nb_update++;
				}
				else{
					$id_gis = gis_inserer();
					gis_modifier($id_gis,$point_add);
					$nb++;
				}
			}
		}
		$nb = count($contenu);
		$res['message_ok'] = '';
		if($nb > 0)
			$res['message_ok'] .= "$nb points importés";
		if($nb_update > 0)
			$res['message_ok'] .= "<br />$nb_update points mis à jour";
	}
	return $res;
}
?>