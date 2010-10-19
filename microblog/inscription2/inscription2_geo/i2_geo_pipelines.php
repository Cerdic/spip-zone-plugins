<?php

/**
 * 
 * Pipeline i2_cfg_form
 * Ajoute la partie de configuration dans le formulaire CFG d'inscription2
 * 
 * @return array Retourne le $flux de départ complété
 * @param array $flux Paramètres passés dans l'environnement du pipeline
 */
function i2_geo_i2_cfg_form($flux){
	$flux .= recuperer_fond('fonds/inscription2_geo');
	return $flux;
}

/**
 * 
 * Pipeline i2_form_fin
 * Ajoute les nouveaux champs dans le formulaire d'inscription publique
 * ou dans le formulaire de modification de profil
 * 
 * @return array Retourne le $flux de départ complété 
 * @param array $flux Paramètres passés dans l'environnement du pipeline
 */	
function i2_geo_i2_form_fin($flux){
	if(is_numeric($flux['args']['id_auteur'])){
		$flux['data'] .= recuperer_fond('formulaires/inscription2_modif_form_geo',$flux['args']);
	}else{
		$flux['data'] .= recuperer_fond('formulaires/inscription2_form_geo',$flux['args']);
	}
	return $flux;
}

/**
 * 
 * Pipeline i2_validation_methods
 * Ajoute des directives aux validations js utilisées dans les crayons
 * 
 * @return array Retourne le $flux de départ complété
 * @param array $flux Paramètres passés dans l'environnement du pipeline
 */	
function i2_geo_i2_validation_methods($flux){
	include_spip('crayons.js_fonctions');
	$flux['data'] .= pack_cQuery(generer_url_public('i2_validation_methods_geo.js','lang='.$flux['args']['lang']));
	return $flux;
}

/**
 * 
 * Pipeline i2_verifications_specifiques
 * Déclare les fonctions de validation servant dans la validation du formulaire
 *  
 * @return array Retourne l'$array de départ complété
 * @param array $array
 */	
function i2_geo_i2_verifications_specifiques($array){
	// Les latitudes : fonction inc/inscription2_valide_latitude
	$array['latitude'] = 'valide_latitude';

	// Les longitudes : fonction inc/inscription2_valide_longitude
	$array['longitude'] = 'valide_longitude';
	
	return $array;
}
?>
