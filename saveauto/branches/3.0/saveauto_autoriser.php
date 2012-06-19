<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// fonction pour le pipeline, n'a rien a effectuer
function saveauto_autoriser(){}

// declarations d'autorisations
function autoriser_saveauto_onglet_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('sauvegarder', 'saveauto', $id, $qui, $opt);
}

function autoriser_saveauto_sauvegarder_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('sauvegarder', '', $id, $qui, $opt);
}

?>