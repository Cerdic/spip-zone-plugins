<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function langonet_autoriser(){}

// declarations d'autorisations
function autoriser_langonet_onglet_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}
?>
