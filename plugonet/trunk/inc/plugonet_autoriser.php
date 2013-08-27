<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function plugonet_autoriser() {}

function autoriser_plugonet_generer_onglet_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}
function autoriser_plugonet_verifier_onglet_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}
function autoriser_plugonet_valider_onglet_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}
?>