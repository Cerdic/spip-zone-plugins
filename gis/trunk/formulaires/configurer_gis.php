<?php 

/**
 * Fonction de verification du formulaire de configuration
 * - On vérifie si dans les cas de cloudmade, de google (v2) ou de yandex, une clé a 
 * été fournie
 */
function formulaires_configurer_gis_verifier_dist(){
	$erreurs = array();
	
	if(in_array(_request('api'), array('cloudmade','google','yandex'))){
		$obligatoire = "api_key_"._request('api');
		if(!_request($obligatoire)){
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}
	
	// S'il n'y a pas d'erreur on va chercher l'ancienne couche par défaut pour voir si elle a changé
	if (empty($erreurs)){
		include_spip('inc/config');
		$layer_defaut = lire_config('gis/layer_defaut');
		// Si on change la couche par défaut ou si une couche google est présente dans la conf, le formulaire ne doit pas etre traiter en ajax
		if ((_request('layer_defaut') != $layer_defaut)
			OR (count(array_intersect(array('google_roadmap', 'google_satellite', 'google_terrain'), _request('layers'))) > 0))
			refuser_traiter_formulaire_ajax();
	}
	
	return $erreurs;
}

?>
