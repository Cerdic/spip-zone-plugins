<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Fonction appelé par le pipeline
function noizetier_autoriser(){}


function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}

function autoriser_noizetier_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'noizetier', $id, $qui,  $opt);
}

function autoriser_bando_noizetier_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'noizetier', $id, $qui,  $opt);
}

?>