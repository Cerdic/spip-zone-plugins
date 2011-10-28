<?php 

/**
 * Fonction de verification du formulaire de configuration
 * - On vérifie si dans les cas de cloudmade ou de google (v2), une clé a 
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
	
	// S'il n'y a pas d'erreur on va chercher l'ancienne valeur de l'API pour voir si elle a changé
	if (empty($erreurs)){
		include_spip('inc/config');
		$ancienne_api = lire_config('gis/api');
		// On la garde en mémoire dans le hit pour une utilisation plus loin
		set_request('ancienne_api', $ancienne_api);
	}
	
	return $erreurs;
}

function gis_configurer_gis_post_traitement($flux){
	// Si on est dans le formulaire de config de GIS et qu'on change d'API
	if (
		$flux['args']['form'] == 'configurer_gis'
		and _request('api') != _request('ancienne_api')
	){
		// On invalide le cache du site
		include_spip('inc/invalideur');
		suivre_invalideur('1');
		
		// Et on recharge entièrement la page
		$flux['data']['redirect'] = self();
	}
	
	return $flux;
}

?>
