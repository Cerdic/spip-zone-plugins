<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction pour le pipeline, n'a rien a effectuer
 *
*/
function gestionml_autoriser(){}

/**
 * Fonction gerant l'autorisation de gestion d'une ML
 *
*/
function autoriser_ml_gerer_dist($faire, $type, $id, $qui, $opt) {
	$config = lire_config('gestionml',array());
	if( sizeof($config)) {
		$chp = 'listes_auteur_'.$qui['id_auteur'] ;
		if( is_array($config[$chp])){
			return in_array($opt['ml'],$config[$chp]);
		} else {
			return( false ) ;
		}
	} else {
		return( false ) ;
	}
}

function autoriser_gestionml_menu_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
function autoriser_gestionml_configurer_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['webmestre'] == 'oui');
}
?>