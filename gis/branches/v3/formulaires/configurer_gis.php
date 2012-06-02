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
	
	// S'il n'y a pas d'erreur on va chercher l'ancienne valeur de l'API pour voir si elle a changé
	if (empty($erreurs)){
		include_spip('inc/config');
		$ancienne_api = lire_config('gis/api');
		// On la garde en mémoire dans le hit pour une utilisation plus loin
		set_request('ancienne_api', $ancienne_api);
		// Si on change d'API, le formulaire ne doit pas etre traiter en ajax car on a besoin que la nouvelle API soit chargee dans gis_inserer_javascript
		if (_request('api') != $ancienne_api)
			refuser_traiter_formulaire_ajax();
	}
	
	return $erreurs;
}

?>
