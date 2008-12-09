<?php

/**
 * Retourne la definition de la barre markitup designee.
 * (cette declaration est au format json)
 * 
 * Deux pipelines 'porte_plume_pre_charger' et 'porte_plume_charger' 
 * permettent de recuperer l'objet de classe Barre_outil
 * avant son export en json pour modifier des elements.
 * 
 * @return string : declaration json
 */
function porte_plume_creer_json_markitup(){
	$sets = pipeline('porte_plume_barre_declarer', array(
		'spip', 'spip_forum',
	));
	
	include_spip('inc/barre_outils');

	// 1 on initialise tous les jeux de barres
	$barres = array();
	foreach($sets as $set) {
		if (($barre = barre_outils_initialiser($set)) AND is_object($barre))
			$barres[$set] = $barre;
	}
	
	// 2 prechargement
	// charge des nouveaux boutons au besoin
	// exemples : 
	//		$barre = &$flux['data']['spip'];
	//  	$barre->ajouterApres('bold',array(params));
	//  	$barre->ajouterAvant('bold',array(params));		
	// 
	//  	$bold = $barre->get('bold');
	//  	$bold['id'] = 'bold2';
	//  	$barre->ajouterApres('italic',$bold);
	$barres = pipeline('porte_plume_barre_pre_charger', $barres);

		
	// 3 chargement
	// 		permet de cacher ou afficher certains boutons au besoin
	// 		exemples :
	//		$barre = &$flux['data']['spip'];
	//  	$barre->afficher('bold');
	//  	$barre->cacher('bold');
	//
	//		$barre->cacherTout();
	//		$barre->afficher(array('bold','italic','header1'));
	$barres = pipeline('porte_plume_barre_charger', $barres);


	// 4 on cree les jsons
	$json = "";
	foreach($barres as $set=>$barre) {
		$json .= $barre->creer_json();
	}
	return $json;
}


?>
