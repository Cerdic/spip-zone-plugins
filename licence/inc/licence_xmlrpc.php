<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Récupère la liste des licences disponibles
 * 
 * Arguments possibles :
 * -* id_licence int (ne renverra que le contenu d'une licence)
 */
function licence_liste_licences($args) {
	global $spip_xmlrpc_serveur;
	
	include_spip('inc/licence');
	$licences = $GLOBALS['licence_licences'];
	
	foreach($licences as $licence => $values){
		if (isset($values['icon'])){
			$licences[$licence]['icon'] = url_absolue(find_in_path('img_pack/'.$values['icon']));
		}
	}
	
	if(is_numeric($args['id_licence']))
		$licences = $licences[$args['id_licence']];
	return $licences;
}
?>