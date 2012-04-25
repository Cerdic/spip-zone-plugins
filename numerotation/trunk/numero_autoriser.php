<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function numero_autoriser(){}

// Autoriser à numéroter en général : ceux qui peuvent configurer
function autoriser_numeroter_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('configurer', $quoi, $id, $qui, $options);
}

// Autoriser à numéroter le contenu d'une rubrique : ceux qui peuvent modifier cette rubrique
function autoriser_rubrique_numeroter_dist($faire, $quoi, $id, $qui, $options){
	return autoriser('modifier', 'rubrique', $id, $qui, $options);
}

?>
