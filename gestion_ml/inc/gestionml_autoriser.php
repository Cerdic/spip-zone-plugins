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

/**
 * Fonction gerant l'autorisation d'administration des ML
 *
*/
function autoriser_ml_administrer_dist($faire,$quoi,$id,$qui,$options) {
	return $qui['statut'] == '0minirezo';
}

function autoriser_gestionml21_bouton_dist($faire,$quoi,$id,$qui,$options) {
	return autoriser('administrer','gestionml',$id,$qui,$options);
}
function autoriser_gestionml_bouton_dist($faire,$quoi,$id,$qui,$options) {
	return autoriser('administrer','gestionml',$id,$qui,$options);
}
?>