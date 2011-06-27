<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function zaboarticle_autoriser(){}

function autoriser_zaboarticle_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', '', $id, $qui, $opt);
}

function autoriser_zaboarticle_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', '', $id, $qui, $opt);
}

?>
