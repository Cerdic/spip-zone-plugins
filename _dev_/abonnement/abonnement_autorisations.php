<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function abonnement_autoriser(){}

// declarations d'autorisations
function autoriser_abonnement_bouton_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'abonnement', $id, $qui, $opt);
}

function autoriser_abonnement_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', '', $id, $qui, $opt);
}

function autoriser_abonnement_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', '', $id, $qui, $opt);
}
?>
